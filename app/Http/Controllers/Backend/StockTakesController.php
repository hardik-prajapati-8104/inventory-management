<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\SparePart;
use App\Models\StockAdjustment;
use App\Models\StockAdjustmentItem;
use App\Models\StockTake;
use App\Models\StockTakeItem;
use App\Models\Warehouse;
use App\Services\StockService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StockTakesController extends Controller
{
    public $user;
    private StockService $stockService;

    public function __construct(StockService $stockService)
    {
        $this->stockService = $stockService;

         $this->user = Auth::guard('admin')->user();
    }

    public function index()
    {
        if (is_null($this->user) || ! $this->user->can('stock.view')) {
            abort(403, 'Sorry !! You are Unauthorized to view Stock Takes !');
        }

        $stockTakes = StockTake::with(['warehouse', 'createdBy', 'items'])->latest()->paginate(15);

        return view('backend.stock-takes.index', compact('stockTakes'));
    }

    public function create()
    {
        if (is_null($this->user) || ! $this->user->can('stock-adjustment.create')) {
            abort(403, 'Sorry !! You are Unauthorized to create Stock Takes !');
        }

        $warehouses = Warehouse::where('status', 1)->orderBy('name')->get();

        return view('backend.stock-takes.create', compact('warehouses'));
    }

    /**
     * Section 23 flow, step 1-2: "Create Stock Take -> Select Warehouse ->
     * Generate Stock Sheet". Snapshots every spare part's current system
     * quantity into stock_take_items so the count sheet reflects a single
     * point in time, unaffected by sales/purchases happening during the count.
     */
    public function store(Request $request)
    {
        if (is_null($this->user) || ! $this->user->can('stock-adjustment.create')) {
            abort(403);
        }

        $request->validate(['warehouse_id' => 'required|exists:warehouses,id']);

        $stockTake = DB::transaction(function () use ($request) {
            $stockTake = StockTake::create([
                'stock_take_number' => $this->nextNumber(),
                'warehouse_id' => $request->warehouse_id,
                'status' => 'counting',
                'created_by' => $this->user->id,
            ]);

            $parts = SparePart::whereHas('stock', fn ($q) => $q->where('warehouse_id', $request->warehouse_id))->get();

            foreach ($parts as $part) {
                $systemQty = $part->stock()->where('warehouse_id', $request->warehouse_id)->value('current_stock') ?? 0;

                StockTakeItem::create([
                    'stock_take_id' => $stockTake->id,
                    'spare_part_id' => $part->id,
                    'system_quantity' => $systemQty,
                ]);
            }

            return $stockTake;
        });

        AuditLog::record('create', 'Stock Takes', $stockTake, "Started stock take {$stockTake->stock_take_number}");

        session()->flash('success', "Stock take {$stockTake->stock_take_number} started — count sheet generated.");
        return redirect()->route('admin.stock-takes.count', $stockTake->id);
    }

    /**
     * Section 23, step 3-4: "Count Physical Stock -> Enter Counted Quantity".
     */
    public function count(int $id)
    {
        if (is_null($this->user) || ! $this->user->can('stock-adjustment.create')) {
            abort(403);
        }

        $stockTake = StockTake::with(['items.sparePart', 'warehouse'])->findOrFail($id);

        return view('backend.stock-takes.count', compact('stockTake'));
    }

    public function saveCounts(Request $request, int $id)
    {
        if (is_null($this->user) || ! $this->user->can('stock-adjustment.create')) {
            abort(403);
        }

        $stockTake = StockTake::with('items')->findOrFail($id);

        $counted = $request->input('counted_quantity', []); // [item_id => qty]

        foreach ($stockTake->items as $item) {
            if (! isset($counted[$item->id]) || $counted[$item->id] === '') {
                continue;
            }
            $qty = (int) $counted[$item->id];
            $item->update([
                'counted_quantity' => $qty,
                'difference' => $qty - $item->system_quantity,
            ]);
        }

        $stockTake->update(['status' => 'pending_approval']);

        session()->flash('success', 'Counts saved. Review the variance and approve to create the stock adjustment.');
        return redirect()->route('admin.stock-takes.count', $stockTake->id);
    }

    /**
     * Section 23, step 5-6: "Compare System vs Physical -> Approve Difference
     * -> Create Stock Adjustment". This is the one place a Stock Take is
     * allowed to change quantities, and it does so by creating a normal,
     * already-approved StockAdjustment — so the resulting stock_movements
     * rows are indistinguishable in the ledger from a manual adjustment,
     * just tagged back to this stock take via the polymorphic reference.
     */
    public function approve(int $id)
    {
        if (is_null($this->user) || ! $this->user->can('stock-adjustment.approve')) {
            abort(403, 'Sorry !! You are Unauthorized to approve Stock Takes !');
        }

        $stockTake = StockTake::with(['items.sparePart', 'warehouse'])->findOrFail($id);

        $variances = $stockTake->items->filter(fn ($i) => $i->counted_quantity !== null && $i->difference !== 0);

        if ($variances->isEmpty()) {
            $stockTake->update(['status' => 'completed']);
            session()->flash('success', 'No variance found — stock take completed, nothing to adjust.');
            return back();
        }

        DB::transaction(function () use ($stockTake, $variances) {
            $adjustment = StockAdjustment::create([
                'adjustment_number' => 'ADJ-'.now()->format('ym').'-'.str_pad((StockAdjustment::max('id') ?? 0) + 1, 4, '0', STR_PAD_LEFT),
                'warehouse_id' => $stockTake->warehouse_id,
                'reason' => 'Physical stock difference',
                'remarks' => "Generated from Stock Take {$stockTake->stock_take_number}",
                'status' => 'approved',
                'created_by' => $this->user->id,
                'approved_by' => $this->user->id,
                'approved_at' => now(),
            ]);

            foreach ($variances as $item) {
                $direction = $item->difference > 0 ? 'increase' : 'decrease';

                StockAdjustmentItem::create([
                    'stock_adjustment_id' => $adjustment->id,
                    'spare_part_id' => $item->spare_part_id,
                    'current_quantity' => $item->system_quantity,
                    'adjustment_type' => $direction,
                    'adjustment_quantity' => abs($item->difference),
                ]);

                $this->stockService->adjust(
                    $item->sparePart,
                    $stockTake->warehouse,
                    $direction,
                    abs($item->difference),
                    $adjustment,
                    "Stock Take {$stockTake->stock_take_number} variance"
                );
            }

            $stockTake->update(['status' => 'completed']);
        });

        AuditLog::record('approve', 'Stock Takes', $stockTake, "Approved variance for stock take {$stockTake->stock_take_number}, adjustment created");

        session()->flash('success', "Stock take {$stockTake->stock_take_number} approved — stock adjusted to match physical count.");
        return redirect()->route('admin.stock-takes.index');
    }

    private function nextNumber(): string
    {
        $next = (StockTake::max('id') ?? 0) + 1;
        return 'ST-'.now()->format('ym').'-'.str_pad($next, 4, '0', STR_PAD_LEFT);
    }
}
