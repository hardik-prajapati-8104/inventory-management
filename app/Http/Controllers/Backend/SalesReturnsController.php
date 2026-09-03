<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Customer;
use App\Models\Sale;
use App\Models\SalesReturn;
use App\Models\SalesReturnItem;
use App\Models\SparePart;
use App\Models\Warehouse;
use App\Services\StockService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SalesReturnsController extends Controller
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
        if (is_null($this->user) || ! $this->user->can('sale-return.view')) {
            abort(403, 'Sorry !! You are Unauthorized to view Sales Returns !');
        }

        $returns = SalesReturn::with(['customer', 'warehouse', 'items'])->latest()->paginate(15);

        return view('backend.sales-returns.index', compact('returns'));
    }

    public function create()
    {
        if (is_null($this->user) || ! $this->user->can('sale-return.create')) {
            abort(403, 'Sorry !! You are Unauthorized to create Sales Returns !');
        }

        $customers = Customer::where('status', 1)->orderBy('customer_name')->get();
        $warehouses = Warehouse::where('status', 1)->orderBy('name')->get();
        $spareParts = SparePart::orderBy('name')->get(['id', 'name', 'part_number']);
        $sales = Sale::latest()->limit(200)->get(['id', 'invoice_number', 'customer_id']);

        return view('backend.sales-returns.create', compact('customers', 'warehouses', 'spareParts', 'sales'));
    }

    public function store(Request $request)
    {
        if (is_null($this->user) || ! $this->user->can('sale-return.create')) {
            abort(403);
        }

        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'sale_id' => 'nullable|exists:sales,id',
            'warehouse_id' => 'required|exists:warehouses,id',
            'return_date' => 'required|date',
            'remarks' => 'nullable',
            'spare_part_id' => 'required|array|min:1',
            'spare_part_id.*' => 'exists:spare_parts,id',
            'quantity' => 'required|array',
            'quantity.*' => 'integer|min:1',
            'condition' => 'required|array',
            'condition.*' => 'in:resalable,damaged,defective',
            'return_reason' => 'nullable|array',
            'refund_amount' => 'nullable|array',
        ]);

        $return = DB::transaction(function () use ($request) {
            $return = SalesReturn::create([
                'return_number' => $this->nextNumber(),
                'customer_id' => $request->customer_id,
                'sale_id' => $request->sale_id,
                'warehouse_id' => $request->warehouse_id,
                'return_date' => $request->return_date,
                'remarks' => $request->remarks,
                'status' => 'pending',
                'created_by' => $this->user->id,
            ]);

            foreach ($request->spare_part_id as $i => $partId) {
                SalesReturnItem::create([
                    'sales_return_id' => $return->id,
                    'spare_part_id' => $partId,
                    'quantity' => $request->quantity[$i],
                    'return_reason' => $request->return_reason[$i] ?? null,
                    'condition' => $request->condition[$i],
                    'refund_amount' => $request->refund_amount[$i] ?? 0,
                ]);
            }

            return $return;
        });

        AuditLog::record('create', 'Sales Returns', $return, "Created sales return {$return->return_number} (pending approval)");

        session()->flash('success', "Sales Return {$return->return_number} created — awaiting approval.");
        return redirect()->route('admin.sales-returns.index');
    }

    /**
     * Section 17: resalable units go back to available stock; damaged/
     * defective units go to damaged stock instead. StockService::
     * receiveSalesReturn() carries that branch so this controller doesn't
     * have to know the difference — it just calls it once per line.
     */
    public function approve(int $id)
    {
        if (is_null($this->user) || ! $this->user->can('sale-return.approve')) {
            abort(403, 'Sorry !! You are Unauthorized to approve Sales Returns !');
        }

        $return = SalesReturn::with(['items.sparePart', 'warehouse'])->findOrFail($id);

        if ($return->status !== 'pending') {
            session()->flash('error', 'This return has already been processed.');
            return back();
        }

        DB::transaction(function () use ($return) {
            foreach ($return->items as $item) {
                $this->stockService->receiveSalesReturn(
                    $item->sparePart,
                    $return->warehouse,
                    $item->quantity,
                    $item->condition,
                    $return,
                    "Sales Return {$return->return_number}: {$item->return_reason}"
                );
            }

            $return->update(['status' => 'approved', 'approved_by' => $this->user->id, 'approved_at' => now()]);
        });

        AuditLog::record('approve', 'Sales Returns', $return, "Approved sales return {$return->return_number}");

        session()->flash('success', "Sales Return {$return->return_number} approved — stock updated.");
        return back();
    }

    private function nextNumber(): string
    {
        $next = (SalesReturn::max('id') ?? 0) + 1;
        return 'SRT-'.now()->format('ym').'-'.str_pad($next, 4, '0', STR_PAD_LEFT);
    }
}
