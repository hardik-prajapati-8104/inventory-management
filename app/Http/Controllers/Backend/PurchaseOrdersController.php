<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\SparePart;
use App\Models\Supplier;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PurchaseOrdersController extends Controller
{
    public $user;

    public function __construct()
    {
         $this->user = Auth::guard('admin')->user();
    }

    public function index()
    {
        if (is_null($this->user) || ! $this->user->can('purchase-order.view')) {
            abort(403, 'Sorry !! You are Unauthorized to view Purchase Orders !');
        }

        $purchaseOrders = PurchaseOrder::with(['supplier', 'warehouse', 'items'])->latest()->paginate(15);

        return view('backend.purchase-orders.index', compact('purchaseOrders'));
    }

    public function create()
    {
        if (is_null($this->user) || ! $this->user->can('purchase-order.create')) {
            abort(403, 'Sorry !! You are Unauthorized to create Purchase Orders !');
        }

        $suppliers = Supplier::where('status', 1)->orderBy('company_name')->get();
        $warehouses = Warehouse::where('status', 1)->orderBy('name')->get();
        $spareParts = SparePart::orderBy('name')->get(['id', 'name', 'part_number', 'purchase_price']);

        // Section 25: Automatic Reorder — pre-fill lines when arriving from
        // "Convert to Purchase Order" on the Reorder Suggestions page.
        $prefill = session()->pull('po_prefill', []);

        return view('backend.purchase-orders.create', compact('suppliers', 'warehouses', 'spareParts', 'prefill'));
    }

    public function store(Request $request)
    {
        if (is_null($this->user) || ! $this->user->can('purchase-order.create')) {
            abort(403);
        }

        $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'po_date' => 'required|date',
            'expected_delivery_date' => 'nullable|date|after_or_equal:po_date',
            'warehouse_id' => 'required|exists:warehouses,id',
            'payment_terms' => 'nullable|max:100',
            'notes' => 'nullable',
            'spare_part_id' => 'required|array|min:1',
            'spare_part_id.*' => 'exists:spare_parts,id',
            'quantity' => 'required|array',
            'quantity.*' => 'integer|min:1',
            'purchase_price' => 'required|array',
            'purchase_price.*' => 'numeric|min:0',
            'discount' => 'nullable|array',
            'tax' => 'nullable|array',
        ]);

        $po = DB::transaction(function () use ($request) {
            $po = PurchaseOrder::create([
                'po_number' => $this->nextNumber(),
                'supplier_id' => $request->supplier_id,
                'po_date' => $request->po_date,
                'expected_delivery_date' => $request->expected_delivery_date,
                'warehouse_id' => $request->warehouse_id,
                'payment_terms' => $request->payment_terms,
                'notes' => $request->notes,
                'status' => 'pending',
                'created_by' => $this->user->id,
            ]);

            foreach ($request->spare_part_id as $i => $partId) {
                $qty = (int) $request->quantity[$i];
                $price = (float) $request->purchase_price[$i];
                $discount = (float) ($request->discount[$i] ?? 0);
                $tax = (float) ($request->tax[$i] ?? 0);
                $total = ($qty * $price) - $discount + $tax;

                PurchaseOrderItem::create([
                    'purchase_order_id' => $po->id,
                    'spare_part_id' => $partId,
                    'quantity' => $qty,
                    'purchase_price' => $price,
                    'discount' => $discount,
                    'tax' => $tax,
                    'total' => $total,
                ]);
            }

            return $po;
        });

        AuditLog::record('create', 'Purchase Orders', $po, "Created purchase order {$po->po_number}");

        session()->flash('success', "Purchase Order {$po->po_number} created.");
        return redirect()->route('admin.purchase-orders.index');
    }

    public function show(int $id)
    {
        if (is_null($this->user) || ! $this->user->can('purchase-order.view')) {
            abort(403);
        }

        $po = PurchaseOrder::with(['supplier', 'warehouse', 'items.sparePart', 'goodsReceipts'])->findOrFail($id);

        return view('backend.purchase-orders.show', compact('po'));
    }

    public function approve(int $id)
    {
        if (is_null($this->user) || ! $this->user->can('purchase-order.approve')) {
            abort(403, 'Sorry !! You are Unauthorized to approve Purchase Orders !');
        }

        $po = PurchaseOrder::findOrFail($id);
        $po->update(['status' => 'approved']);

        AuditLog::record('approve', 'Purchase Orders', $po, "Approved purchase order {$po->po_number}");

        session()->flash('success', "Purchase Order {$po->po_number} approved — ready to receive against.");
        return back();
    }

    public function cancel(int $id)
    {
        if (is_null($this->user) || ! $this->user->can('purchase-order.approve')) {
            abort(403);
        }

        $po = PurchaseOrder::findOrFail($id);

        if (in_array($po->status, ['received', 'partially_received'])) {
            session()->flash('error', 'Cannot cancel a purchase order that has already received stock.');
            return back();
        }

        $po->update(['status' => 'cancelled']);
        AuditLog::record('cancel', 'Purchase Orders', $po, "Cancelled purchase order {$po->po_number}");

        session()->flash('success', "Purchase Order {$po->po_number} cancelled.");
        return back();
    }

    private function nextNumber(): string
    {
        $next = (PurchaseOrder::max('id') ?? 0) + 1;
        return 'PO-'.now()->format('ym').'-'.str_pad($next, 4, '0', STR_PAD_LEFT);
    }
}
