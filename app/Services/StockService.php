<?php

namespace App\Services;

use App\Models\SparePart;
use App\Models\Stock;
use App\Models\StockMovement;
use App\Models\Warehouse;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * The single write path for every stock-quantity change in the system.
 *
 * Nothing outside this class should ever touch `stock.current_stock` or
 * `spare_parts.current_stock` directly. Every mutation:
 *   1. Locks the relevant `stock` row
 *   2. Writes an immutable `stock_movements` row (the audit trail)
 *   3. Updates the `stock` cache row from before/after quantities
 *   4. Updates the denormalized `spare_parts.current_stock` total (used for
 *      fast list/search display without summing every warehouse each time)
 *
 * This mirrors the AuditLog::record() pattern from Phase 1 — one write path,
 * fully reconstructable history, nothing edited in place.
 */
class StockService
{
    /**
     * Apply a signed quantity change and record the movement.
     *
     * @param  int  $quantity  Positive to increase stock, negative to decrease.
     */
    public function move(
        SparePart $sparePart,
        Warehouse $warehouse,
        string $type,
        int $quantity,
        ?Model $reference = null,
        ?string $notes = null
    ): StockMovement {
        return DB::transaction(function () use ($sparePart, $warehouse, $type, $quantity, $reference, $notes) {
            $stock = Stock::lockForUpdate()->firstOrCreate(
                ['spare_part_id' => $sparePart->id, 'warehouse_id' => $warehouse->id],
                ['current_stock' => 0, 'reserved_stock' => 0, 'damaged_stock' => 0]
            );

            $before = $stock->current_stock;
            $after = $before + $quantity;

            if ($after < 0 && ! $this->negativeStockAllowed()) {
                throw new \RuntimeException(
                    "Insufficient stock for \"{$sparePart->name}\" in {$warehouse->name}: ".
                    "have {$before}, tried to remove ".abs($quantity).'.'
                );
            }

            $movement = StockMovement::create([
                'spare_part_id' => $sparePart->id,
                'warehouse_id' => $warehouse->id,
                'type' => $type,
                'quantity' => $quantity,
                'stock_before' => $before,
                'stock_after' => $after,
                'reference_type' => $reference ? get_class($reference) : null,
                'reference_id' => $reference?->id,
                'notes' => $notes,
                'created_by' => Auth::guard('admin')->id(),
            ]);

            $stock->current_stock = $after;
            $stock->save();

            // Denormalized total across every warehouse — recomputed from the
            // `stock` cache (cheap: one indexed sum), not from stock_movements.
            $sparePart->current_stock = Stock::where('spare_part_id', $sparePart->id)->sum('current_stock');
            $sparePart->save();

            return $movement;
        });
    }

    public function openingStock(SparePart $sparePart, Warehouse $warehouse, int $quantity, ?string $notes = null): ?StockMovement
    {
        if ($quantity <= 0) {
            return null;
        }

        return $this->move($sparePart, $warehouse, 'OPENING_STOCK', $quantity, notes: $notes ?? 'Opening stock at part creation');
    }

    /**
     * Restocks an *existing* spare part — e.g. a supplier invoice/estimate
     * import matched an already-known part and is adding more units of it,
     * as opposed to openingStock() which is only for a brand-new part being
     * created for the first time. Same ledger discipline as everything else
     * here: one PURCHASE movement, no direct writes to current_stock.
     */
    public function purchaseStock(SparePart $sparePart, Warehouse $warehouse, int $quantity, ?Model $reference = null, ?string $notes = null): ?StockMovement
    {
        if ($quantity <= 0) {
            return null;
        }

        return $this->move($sparePart, $warehouse, 'PURCHASE', $quantity, $reference, $notes ?? 'Restocked via import');
    }

    public function adjust(SparePart $sparePart, Warehouse $warehouse, string $direction, int $quantity, Model $reference, ?string $notes = null): StockMovement
    {
        $type = $direction === 'increase' ? 'ADJUSTMENT_IN' : 'ADJUSTMENT_OUT';
        $signedQty = $direction === 'increase' ? abs($quantity) : -abs($quantity);

        return $this->move($sparePart, $warehouse, $type, $signedQty, $reference, $notes);
    }

    /**
     * Moves stock between two warehouses as a single logical operation: an
     * OUT movement from the source and an IN movement into the destination.
     * Both happen inside one DB transaction so a transfer can never leave
     * stock stuck "in transit" due to a partial failure.
     */
    public function transfer(SparePart $sparePart, Warehouse $from, Warehouse $to, int $quantity, Model $reference, ?string $notes = null): array
    {
        return DB::transaction(function () use ($sparePart, $from, $to, $quantity, $reference, $notes) {
            $out = $this->move($sparePart, $from, 'TRANSFER_OUT', -abs($quantity), $reference, $notes);
            $in = $this->move($sparePart, $to, 'TRANSFER_IN', abs($quantity), $reference, $notes);

            return [$out, $in];
        });
    }

    public function markDamaged(SparePart $sparePart, Warehouse $warehouse, int $quantity, ?Model $reference = null, ?string $notes = null): StockMovement
    {
        return $this->move($sparePart, $warehouse, 'DAMAGE', -abs($quantity), $reference, $notes);
    }

    /**
     * Section 17: a sales return's stock treatment depends on its condition.
     * - Resalable: goes straight back into sellable stock — a normal
     *   SALES_RETURN ledger entry that increases current_stock, same as any
     *   other inbound movement.
     * - Damaged / Defective: the physical unit is back in the warehouse but
     *   is NOT sellable, so current_stock must NOT increase. It still needs
     *   a ledger row for traceability (a return happened, quantity is
     *   accounted for), so this writes a zero-net SALES_RETURN movement and
     *   separately increments the `damaged_stock` bucket on both `stock`
     *   and the denormalized `spare_parts` total — the same reasoning
     *   markDamaged() uses, just for stock coming back in rather than stock
     *   already on the shelf going bad.
     */
    public function receiveSalesReturn(SparePart $sparePart, Warehouse $warehouse, int $quantity, string $condition, Model $reference, ?string $notes = null): StockMovement
    {
        if ($condition === 'resalable') {
            return $this->move($sparePart, $warehouse, 'SALES_RETURN', abs($quantity), $reference, $notes);
        }

        return DB::transaction(function () use ($sparePart, $warehouse, $quantity, $reference, $notes, $condition) {
            $stock = Stock::lockForUpdate()->firstOrCreate(
                ['spare_part_id' => $sparePart->id, 'warehouse_id' => $warehouse->id],
                ['current_stock' => 0, 'reserved_stock' => 0, 'damaged_stock' => 0]
            );

            // Zero-net movement: records that a return happened without
            // touching sellable current_stock.
            $movement = StockMovement::create([
                'spare_part_id' => $sparePart->id,
                'warehouse_id' => $warehouse->id,
                'type' => 'SALES_RETURN',
                'quantity' => 0,
                'stock_before' => $stock->current_stock,
                'stock_after' => $stock->current_stock,
                'reference_type' => get_class($reference),
                'reference_id' => $reference->id,
                'notes' => ($notes ?? '')." (received as {$condition}, ".abs($quantity)." unit(s) added to damaged stock, not sellable)",
                'created_by' => Auth::guard('admin')->id(),
            ]);

            $stock->damaged_stock += abs($quantity);
            $stock->save();

            $sparePart->damaged_stock = Stock::where('spare_part_id', $sparePart->id)->sum('damaged_stock');
            $sparePart->save();

            return $movement;
        });
    }

    private function negativeStockAllowed(): bool
    {
        return (bool) \App\Models\Setting::get('inventory', 'allow_negative_stock', '0');
    }
}
