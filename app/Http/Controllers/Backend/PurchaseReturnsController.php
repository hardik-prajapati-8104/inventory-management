<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Purchase;
use App\Models\PurchaseReturn;
use App\Models\PurchaseReturnItem;
use App\Models\SparePart;
use App\Models\Supplier;
use App\Models\Warehouse;
use App\Services\StockService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PurchaseReturnsController extends Controller
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
        if (is_null($this->user) || ! $this->user->can('purchase-return.view')) {
            abort(403, 'Sorry !! You are Unauthorized to view Purchase Returns !');
        }

        $returns = PurchaseReturn::with(['supplier', 'warehouse', 'items'])->latest()->paginate(20);

        return view('backend.purchase-returns.index', compact('returns'));
    }

    public function create()
    {
        if (is_null($this->user) || ! $this->user->can('purchase-return.create')) {
            abort(403, 'Sorry !! You are Unauthorized to create Purchase Returns !');
        }

        $suppliers = Supplier::where('status', 1)->orderBy('company_name')->get();
        $warehouses = Warehouse::where('status', 1)->orderBy('name')->get();
        $spareParts = SparePart::orderBy('name')->get(['id', 'name', 'part_number', 'current_stock', 'purchase_price']);
        $purchases = Purchase::latest()->limit(200)->get(['id', 'invoice_number', 'supplier_id']);

        return view('backend.purchase-returns.create', compact('suppliers', 'warehouses', 'spareParts', 'purchases'));
    }

    public function store(Request $request)
    {
        if (is_null($this->user) || ! $this->user->can('purchase-return.create')) {
            abort(403);
        }

        $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'purchase_id' => 'nullable|exists:purchases,id',
            'warehouse_id' => 'required|exists:warehouses,id',
            'return_date' => 'required|date',
            'remarks' => 'nullable',
            'spare_part_id' => 'required|array|min:1',
            'spare_part_id.*' => 'exists:spare_parts,id',
            'quantity' => 'required|array',
            'quantity.*' => 'integer|min:1',
            'reason' => 'nullable|array',
            'amount' => 'nullable|array',
        ]);

        $return = DB::transaction(function () use ($request) {
            $return = PurchaseReturn::create([
                'return_number' => $this->nextNumber(),
                'supplier_id' => $request->supplier_id,
                'purchase_id' => $request->purchase_id,
                'warehouse_id' => $request->warehouse_id,
                'return_date' => $request->return_date,
                'remarks' => $request->remarks,
                'status' => 'pending',
                'created_by' => $this->user->id,
            ]);

            foreach ($request->spare_part_id as $i => $partId) {
                PurchaseReturnItem::create([
                    'purchase_return_id' => $return->id,
                    'spare_part_id' => $partId,
                    'quantity' => $request->quantity[$i],
                    'reason' => $request->reason[$i] ?? null,
                    'amount' => $request->amount[$i] ?? 0,
                ]);
            }

            return $return;
        });

        AuditLog::record('create', 'Purchase Returns', $return, "Created purchase return {$return->return_number} (pending approval)");

        session()->flash('success', "Purchase Return {$return->return_number} created — awaiting approval.");
        return redirect()->route('admin.purchase-returns.index');
    }

    /**
     * Section 15: "Stock should automatically decrease after confirmation."
     * Only approve() touches stock_movements, via StockService, type
     * PURCHASE_RETURN.
     */
    public function approve(int $id)
    {
        if (is_null($this->user) || ! $this->user->can('purchase-return.approve')) {
            abort(403, 'Sorry !! You are Unauthorized to approve Purchase Returns !');
        }

        $return = PurchaseReturn::with(['items.sparePart', 'warehouse'])->findOrFail($id);

        if ($return->status !== 'pending') {
            session()->flash('error', 'This return has already been processed.');
            return back();
        }

        DB::transaction(function () use ($return) {
            foreach ($return->items as $item) {
                $this->stockService->move(
                    $item->sparePart,
                    $return->warehouse,
                    'PURCHASE_RETURN',
                    -abs($item->quantity),
                    $return,
                    "Purchase Return {$return->return_number}: {$item->reason}"
                );
            }

            $return->update(['status' => 'approved', 'approved_by' => $this->user->id, 'approved_at' => now()]);
        });

        AuditLog::record('approve', 'Purchase Returns', $return, "Approved purchase return {$return->return_number}");

        session()->flash('success', "Purchase Return {$return->return_number} approved — stock decreased.");
        return back();
    }

    private function nextNumber(): string
    {
        $next = (PurchaseReturn::max('id') ?? 0) + 1;
        return 'PRT-'.now()->format('ym').'-'.str_pad($next, 4, '0', STR_PAD_LEFT);
    }
}
