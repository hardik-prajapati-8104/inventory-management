<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Setting;
use App\Models\SparePart;
use App\Models\Stock;
use App\Models\StockMovement;
use App\Models\Warehouse;
use App\Services\StockService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

/**
 * These tests exist to protect the one architectural rule this whole app is
 * built around: every stock quantity change goes through StockService, which
 * writes an immutable stock_movements row before ever touching a cached
 * current_stock column. If these pass, "never update stock blindly from
 * multiple places" is actually true, not just a comment.
 */
class StockServiceTest extends TestCase
{
    use RefreshDatabase;

    private StockService $service;
    private SparePart $part;
    private Warehouse $warehouse;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = app(StockService::class);
        $this->warehouse = Warehouse::factory()->create();
        $this->part = SparePart::factory()->create(['minimum_stock' => 5]);

        // StockMovement.created_by reads the authenticated admin guard.
        Auth::guard('admin')->login(Admin::factory()->create());
    }

    public function test_move_creates_a_ledger_row_and_updates_the_cache(): void
    {
        $movement = $this->service->move($this->part, $this->warehouse, 'PURCHASE', 50);

        $this->assertDatabaseHas('stock_movements', [
            'id' => $movement->id,
            'spare_part_id' => $this->part->id,
            'warehouse_id' => $this->warehouse->id,
            'type' => 'PURCHASE',
            'quantity' => 50,
            'stock_before' => 0,
            'stock_after' => 50,
        ]);

        $stock = Stock::where('spare_part_id', $this->part->id)->where('warehouse_id', $this->warehouse->id)->first();
        $this->assertEquals(50, $stock->current_stock);

        // The denormalized total on spare_parts must match the sum across warehouses.
        $this->assertEquals(50, $this->part->fresh()->current_stock);
    }

    public function test_current_stock_is_the_sum_of_every_movement_not_just_the_last_one(): void
    {
        $this->service->move($this->part, $this->warehouse, 'OPENING_STOCK', 20);
        $this->service->move($this->part, $this->warehouse, 'PURCHASE', 30);
        $this->service->move($this->part, $this->warehouse, 'SALE', -15);
        $this->service->move($this->part, $this->warehouse, 'DAMAGE', -5);

        $this->assertEquals(4, StockMovement::where('spare_part_id', $this->part->id)->count());
        $this->assertEquals(30, $this->part->fresh()->current_stock); // 20 + 30 - 15 - 5
    }

    public function test_a_sale_cannot_take_stock_negative_by_default(): void
    {
        $this->service->move($this->part, $this->warehouse, 'OPENING_STOCK', 10);

        $this->expectException(\RuntimeException::class);
        $this->service->move($this->part, $this->warehouse, 'SALE', -11);
    }

    public function test_negative_stock_is_allowed_once_the_setting_is_enabled(): void
    {
        Setting::set('inventory', 'allow_negative_stock', '1');

        $this->service->move($this->part, $this->warehouse, 'OPENING_STOCK', 10);
        $movement = $this->service->move($this->part, $this->warehouse, 'SALE', -15);

        $this->assertEquals(-5, $movement->stock_after);
        $this->assertEquals(-5, $this->part->fresh()->current_stock);
    }

    public function test_purchase_stock_restocks_an_existing_part_via_a_purchase_movement(): void
    {
        $this->service->openingStock($this->part, $this->warehouse, 10);

        $movement = $this->service->purchaseStock($this->part, $this->warehouse, 15, notes: 'Restocked via import: invoice.pdf');

        $this->assertSame('PURCHASE', $movement->type);
        $this->assertSame(15, $movement->quantity);
        $this->assertSame('Restocked via import: invoice.pdf', $movement->notes);
        $this->assertEquals(25, $this->part->fresh()->current_stock); // 10 + 15
    }

    public function test_purchase_stock_does_nothing_for_a_zero_or_negative_quantity(): void
    {
        $this->assertNull($this->service->purchaseStock($this->part, $this->warehouse, 0));
        $this->assertNull($this->service->purchaseStock($this->part, $this->warehouse, -5));
        $this->assertEquals(0, $this->part->fresh()->current_stock);
    }

    public function test_transfer_moves_stock_between_warehouses_atomically(): void
    {
        $warehouseB = Warehouse::factory()->create();
        $this->service->move($this->part, $this->warehouse, 'OPENING_STOCK', 50);

        [$out, $in] = $this->service->transfer($this->part, $this->warehouse, $warehouseB, 20, $this->makeReferenceable());

        $this->assertEquals(-20, $out->quantity);
        $this->assertEquals(20, $in->quantity);

        $sourceStock = Stock::where('spare_part_id', $this->part->id)->where('warehouse_id', $this->warehouse->id)->first();
        $destStock = Stock::where('spare_part_id', $this->part->id)->where('warehouse_id', $warehouseB->id)->first();

        $this->assertEquals(30, $sourceStock->current_stock);
        $this->assertEquals(20, $destStock->current_stock);
        $this->assertEquals(50, $this->part->fresh()->current_stock); // total unchanged, just relocated
    }

    public function test_resalable_sales_return_increases_sellable_stock(): void
    {
        $this->service->move($this->part, $this->warehouse, 'OPENING_STOCK', 10);
        $this->service->move($this->part, $this->warehouse, 'SALE', -4);

        $this->service->receiveSalesReturn($this->part, $this->warehouse, 2, 'resalable', $this->makeReferenceable());

        $this->assertEquals(8, $this->part->fresh()->current_stock); // 10 - 4 + 2
        $this->assertEquals(0, $this->part->fresh()->damaged_stock);
    }

    public function test_damaged_sales_return_does_not_increase_sellable_stock(): void
    {
        $this->service->move($this->part, $this->warehouse, 'OPENING_STOCK', 10);
        $this->service->move($this->part, $this->warehouse, 'SALE', -4);

        $movement = $this->service->receiveSalesReturn($this->part, $this->warehouse, 2, 'damaged', $this->makeReferenceable());

        // Sellable stock is untouched by the damaged return...
        $this->assertEquals(6, $this->part->fresh()->current_stock); // 10 - 4, the return did NOT add back
        // ...but the unit is still accounted for, just in the damaged bucket.
        $this->assertEquals(2, $this->part->fresh()->damaged_stock);
        // And there's still a ledger row for traceability, even though it's zero-net.
        $this->assertEquals(0, $movement->quantity);
        $this->assertEquals('SALES_RETURN', $movement->type);
    }

    /**
     * StockService's $reference argument accepts any saved Eloquent model —
     * it only needs get_class() and ->id for the polymorphic column. A second
     * Warehouse is a convenient stand-in here so this test file doesn't need
     * to build a full Sale/PurchaseReturn with all their required relations.
     */
    private function makeReferenceable(): Warehouse
    {
        return Warehouse::factory()->create();
    }
}
