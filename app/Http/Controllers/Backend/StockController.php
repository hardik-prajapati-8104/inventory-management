<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\SparePart;
use App\Models\StockMovement;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StockController extends Controller
{
    public $user;

    public function __construct()
    {
         $this->user = Auth::guard('admin')->user();
    }

    /**
     * Current stock — reads the denormalized spare_parts.current_stock total
     * (Section 18) for fast listing/search. Per-warehouse breakdown is
     * available via the "View Locations" expand, backed by the `stock` table.
     */
    public function index(Request $request)
    {
        if (is_null($this->user) || ! $this->user->can('stock.view')) {
            abort(403, 'Sorry !! You are Unauthorized to view Stock !');
        }

        $query = SparePart::with(['category', 'unit', 'stock.warehouse']);

        if ($term = $request->get('q')) {
            $query->where(function ($q) use ($term) {
                $q->where('name', 'like', "%$term%")
                    ->orWhere('part_number', 'like', "%$term%")
                    ->orWhere('sku', 'like', "%$term%");
            });
        }

        if ($warehouseId = $request->get('warehouse_id')) {
            $query->whereHas('stock', fn ($q) => $q->where('warehouse_id', $warehouseId)->where('current_stock', '>', 0));
        }

        $spareParts = $query->orderBy('name')->paginate(30)->withQueryString();
        $warehouses = Warehouse::orderBy('name')->get();

        return view('backend.stock.index', compact('spareParts', 'warehouses'));
    }

    public function lowStock()
    {
        if (is_null($this->user) || ! $this->user->can('stock.view')) {
            abort(403);
        }

        $spareParts = SparePart::with(['category', 'unit'])
            ->lowStock()
            ->where('current_stock', '>', 0)
            ->orderBy('current_stock')
            ->paginate(30);

        return view('backend.stock.low-stock', compact('spareParts'));
    }

    public function outOfStock()
    {
        if (is_null($this->user) || ! $this->user->can('stock.view')) {
            abort(403);
        }

        $spareParts = SparePart::with(['category', 'unit'])
            ->outOfStock()
            ->orderBy('name')
            ->paginate(30);

        return view('backend.stock.out-of-stock', compact('spareParts'));
    }

    public function damagedStock()
    {
        if (is_null($this->user) || ! $this->user->can('stock.view')) {
            abort(403);
        }

        $spareParts = SparePart::with(['category', 'unit'])
            ->where('damaged_stock', '>', 0)
            ->orderByDesc('damaged_stock')
            ->paginate(30);

        return view('backend.stock.damaged-stock', compact('spareParts'));
    }

    /**
     * Section 25: Automatic Reorder. Suggested quantity = Maximum Stock -
     * Current Stock when a maximum is set; otherwise falls back to
     * (Minimum Stock x 2) - Current Stock, so a part with no explicit max
     * still gets a sensible suggestion instead of being skipped.
     */
    public function reorderSuggestions()
    {
        if (is_null($this->user) || ! $this->user->can('stock.view')) {
            abort(403, 'Sorry !! You are Unauthorized to view Reorder Suggestions !');
        }

        $parts = \App\Models\SparePart::with(['category', 'brand', 'unit'])
            ->whereColumn('current_stock', '<=', 'minimum_stock')
            ->where('status', 'active')
            ->orderBy('current_stock')
            ->get()
            ->map(function ($part) {
                $target = $part->maximum_stock ?: ($part->minimum_stock * 2);
                $part->suggested_quantity = max(1, $target - $part->current_stock);
                return $part;
            });

        return view('backend.stock.reorder-suggestions', compact('parts'));
    }

    /**
     * Stashes the selected suggestions in the session and hands off to
     * Purchase Order creation, which reads them back to pre-fill its line
     * items — "The purchase manager can convert the suggestion into a
     * Purchase Order" (Section 25).
     */
    public function convertReorderToPO(\Illuminate\Http\Request $request)
    {
        if (is_null($this->user) || ! $this->user->can('purchase-order.create')) {
            abort(403, 'Sorry !! You are Unauthorized to create Purchase Orders !');
        }

        $request->validate([
            'spare_part_id' => 'required|array|min:1',
            'spare_part_id.*' => 'exists:spare_parts,id',
            'suggested_quantity' => 'required|array',
        ]);

        $prefill = [];
        foreach ($request->spare_part_id as $i => $partId) {
            $qty = (int) ($request->suggested_quantity[$i] ?? 0);
            if ($qty > 0) {
                $prefill[] = ['spare_part_id' => $partId, 'quantity' => $qty];
            }
        }

        session(['po_prefill' => $prefill]);

        return redirect()->route('admin.purchase-orders.create');
    }

    /**
     * Section 42: the full, filterable stock_movements ledger — the
     * reconstructable history every quantity in the system derives from.
     */
    public function movements(Request $request)
    {
        if (is_null($this->user) || ! $this->user->can('stock.view')) {
            abort(403);
        }

        $query = StockMovement::with(['sparePart', 'warehouse', 'createdBy']);

        if ($type = $request->get('type')) {
            $query->where('type', $type);
        }

        if ($partId = $request->get('spare_part_id')) {
            $query->where('spare_part_id', $partId);
        }

        $movements = $query->latest()->paginate(50)->withQueryString();

        return view('backend.stock.movements', compact('movements'));
    }
}
