<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\SparePart;
use App\Models\StockAdjustment;
use App\Models\StockAdjustmentItem;
use App\Models\Warehouse;
use App\Services\StockService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StockAdjustmentsController extends Controller
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
        if (is_null($this->user) || ! $this->user->can('stock-adjustment.view')) {
            abort(403, 'Sorry !! You are Unauthorized to view Stock Adjustments !');
        }

        $adjustments = StockAdjustment::with(['warehouse', 'createdBy', 'items'])->latest()->paginate(20);

        return view('backend.stock-adjustments.index', compact('adjustments'));
    }

    public function create()
    {
        if (is_null($this->user) || ! $this->user->can('stock-adjustment.create')) {
            abort(403, 'Sorry !! You are Unauthorized to create Stock Adjustments !');
        }

        $warehouses = Warehouse::where('status', 1)->orderBy('name')->get();
        $spareParts = SparePart::orderBy('name')->get(['id', 'name', 'part_number', 'current_stock']);

        return view('backend.stock-adjustments.create', compact('warehouses', 'spareParts'));
    }

    /**
     * Creates the adjustment as `pending` — quantities are NOT touched here.
     * Section 22/48 treats adjustments as something a supervisor approves,
     * so the actual stock_movements write happens only in approve().
     */
    public function store(Request $request)
    {
        if (is_null($this->user) || ! $this->user->can('stock-adjustment.create')) {
            abort(403);
        }

        $request->validate([
            'warehouse_id' => 'required|exists:warehouses,id',
            'reason' => 'required|in:Physical stock difference,Damaged product,Lost product,Found stock,Data correction,Opening stock correction',
            'remarks' => 'nullable',
            'spare_part_id' => 'required|array|min:1',
            'spare_part_id.*' => 'exists:spare_parts,id',
            'adjustment_type' => 'required|array',
            'adjustment_type.*' => 'in:increase,decrease',
            'adjustment_quantity' => 'required|array',
            'adjustment_quantity.*' => 'integer|min:1',
        ]);

        $adjustment = DB::transaction(function () use ($request) {
            $adjustment = StockAdjustment::create([
                'adjustment_number' => $this->nextNumber(),
                'warehouse_id' => $request->warehouse_id,
                'reason' => $request->reason,
                'remarks' => $request->remarks,
                'status' => 'pending',
                'created_by' => $this->user->id,
            ]);

            foreach ($request->spare_part_id as $i => $partId) {
                $part = SparePart::find($partId);
                StockAdjustmentItem::create([
                    'stock_adjustment_id' => $adjustment->id,
                    'spare_part_id' => $partId,
                    'current_quantity' => $part->current_stock,
                    'adjustment_type' => $request->adjustment_type[$i],
                    'adjustment_quantity' => $request->adjustment_quantity[$i],
                ]);
            }

            return $adjustment;
        });

        AuditLog::record('create', 'Stock Adjustments', $adjustment, "Created stock adjustment {$adjustment->adjustment_number} (pending approval)");

        session()->flash('success', "Adjustment {$adjustment->adjustment_number} created — awaiting approval.");
        return redirect()->route('admin.stock-adjustments.index');
    }

    /**
     * The only place adjustment quantities actually touch stock_movements.
     */
    public function approve(int $id)
    {
        if (is_null($this->user) || ! $this->user->can('stock-adjustment.approve')) {
            abort(403, 'Sorry !! You are Unauthorized to approve Stock Adjustments !');
        }

        $adjustment = StockAdjustment::with('items.sparePart', 'warehouse')->findOrFail($id);

        if ($adjustment->status !== 'pending') {
            session()->flash('error', 'This adjustment has already been processed.');
            return back();
        }

        DB::transaction(function () use ($adjustment) {
            foreach ($adjustment->items as $item) {
                $this->stockService->adjust(
                    $item->sparePart,
                    $adjustment->warehouse,
                    $item->adjustment_type,
                    $item->adjustment_quantity,
                    $adjustment,
                    "Stock Adjustment {$adjustment->adjustment_number}: {$adjustment->reason}"
                );
            }

            $adjustment->update([
                'status' => 'approved',
                'approved_by' => $this->user->id,
                'approved_at' => now(),
            ]);
        });

        AuditLog::record('approve', 'Stock Adjustments', $adjustment, "Approved stock adjustment {$adjustment->adjustment_number}");

        session()->flash('success', "Adjustment {$adjustment->adjustment_number} approved and applied to stock.");
        return back();
    }

    public function reject(int $id)
    {
        if (is_null($this->user) || ! $this->user->can('stock-adjustment.approve')) {
            abort(403);
        }

        $adjustment = StockAdjustment::findOrFail($id);
        $adjustment->update(['status' => 'rejected', 'approved_by' => $this->user->id, 'approved_at' => now()]);

        AuditLog::record('cancel', 'Stock Adjustments', $adjustment, "Rejected stock adjustment {$adjustment->adjustment_number}");

        session()->flash('success', "Adjustment {$adjustment->adjustment_number} rejected.");
        return back();
    }

    private function nextNumber(): string
    {
        $next = (StockAdjustment::max('id') ?? 0) + 1;
        return 'ADJ-'.now()->format('ym').'-'.str_pad($next, 4, '0', STR_PAD_LEFT);
    }
}
