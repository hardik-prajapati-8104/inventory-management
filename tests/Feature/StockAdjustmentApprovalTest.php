<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\SparePart;
use App\Models\StockAdjustment;
use App\Models\StockAdjustmentItem;
use App\Models\StockMovement;
use App\Models\Warehouse;
use App\Services\StockService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

/**
 * Covers the "pending doesn't move stock, only approval does" pattern shared
 * by Stock Adjustments, Stock Transfers, and Purchase/Sales Returns. Written
 * against StockAdjustment specifically since it's the simplest of the four.
 */
class StockAdjustmentApprovalTest extends TestCase
{
    use RefreshDatabase;

    public function test_creating_an_adjustment_does_not_touch_stock_until_it_is_approved(): void
    {
        $admin = Admin::factory()->create();
        Auth::guard('admin')->login($admin);

        $warehouse = Warehouse::factory()->create();
        $part = SparePart::factory()->create();

        app(StockService::class)->openingStock($part, $warehouse, 20);
        $this->assertEquals(20, $part->fresh()->current_stock);

        $adjustment = StockAdjustment::create([
            'adjustment_number' => 'ADJ-TEST-0001',
            'warehouse_id' => $warehouse->id,
            'reason' => 'Physical stock difference',
            'status' => 'pending',
            'created_by' => $admin->id,
        ]);

        StockAdjustmentItem::create([
            'stock_adjustment_id' => $adjustment->id,
            'spare_part_id' => $part->id,
            'current_quantity' => 20,
            'adjustment_type' => 'increase',
            'adjustment_quantity' => 5,
        ]);

        // Still pending: stock must be exactly what it was before the adjustment was drafted.
        $this->assertEquals(20, $part->fresh()->current_stock);
        $this->assertEquals(1, StockMovement::where('spare_part_id', $part->id)->count()); // just the opening stock

        // Now approve it, the same way StockAdjustmentsController::approve() does.
        app(StockService::class)->adjust($part, $warehouse, 'increase', 5, $adjustment, 'test approval');

        $this->assertEquals(25, $part->fresh()->current_stock);
        $this->assertEquals(2, StockMovement::where('spare_part_id', $part->id)->count());
        $this->assertDatabaseHas('stock_movements', [
            'spare_part_id' => $part->id,
            'type' => 'ADJUSTMENT_IN',
            'quantity' => 5,
            'reference_type' => StockAdjustment::class,
            'reference_id' => $adjustment->id,
        ]);
    }
}
