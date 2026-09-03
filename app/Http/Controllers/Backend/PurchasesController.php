<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\GoodsReceipt;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\SparePart;
use App\Models\Supplier;
use App\Models\Warehouse;
use App\Services\StockService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PurchasesController extends Controller
{
    public $user;
    private StockService $stockService;

    public function __construct(StockService $stockService)
    {
        $this->stockService = $stockService;

         $this->user = Auth::guard('admin')->user();
    }

    public function index(Request $request)
    {
        if (is_null($this->user) || ! $this->user->can('purchase.view')) {
            abort(403, 'Sorry !! You are Unauthorized to view Purchases !');
        }

        $query = Purchase::with(['supplier', 'warehouse']);

        if ($status = $request->get('payment_status')) {
            $query->where('payment_status', $status);
        }

        $purchases = $query->latest()->paginate(20)->withQueryString();

        return view('backend.purchases.index', compact('purchases'));
    }

    /**
     * If a goods_receipt_id is passed (from GoodsReceiptsController redirect),
     * the invoice is pre-filled from that GRN and will NOT move stock again —
     * the GRN already did. Otherwise this is a quick, standalone purchase
     * entry (Section 49's MVP flow) that moves stock itself on save.
     */
    public function create(Request $request)
    {
        if (is_null($this->user) || ! $this->user->can('purchase.create')) {
            abort(403, 'Sorry !! You are Unauthorized to create Purchases !');
        }

        $suppliers = Supplier::where('status', 1)->orderBy('company_name')->get();
        $warehouses = Warehouse::where('status', 1)->orderBy('name')->get();
        $spareParts = SparePart::orderBy('name')->get(['id', 'name', 'part_number', 'purchase_price']);

        $goodsReceipt = null;
        if ($request->filled('goods_receipt_id')) {
            $goodsReceipt = GoodsReceipt::with(['items.sparePart', 'supplier', 'warehouse', 'purchaseOrder.items'])
                ->find($request->goods_receipt_id);
        }

        return view('backend.purchases.create', compact('suppliers', 'warehouses', 'spareParts', 'goodsReceipt'));
    }

    public function store(Request $request)
    {
        if (is_null($this->user) || ! $this->user->can('purchase.create')) {
            abort(403);
        }

        $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'goods_receipt_id' => 'nullable|exists:goods_receipts,id',
            'invoice_date' => 'required|date',
            'due_date' => 'nullable|date|after_or_equal:invoice_date',
            'warehouse_id' => 'required|exists:warehouses,id',
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

        $linkedToReceivedGrn = $request->filled('goods_receipt_id');

        $purchase = DB::transaction(function () use ($request, $linkedToReceivedGrn) {
            $subtotal = 0;
            $discountTotal = 0;
            $taxTotal = 0;
            $lines = [];

            foreach ($request->spare_part_id as $i => $partId) {
                $qty = (int) $request->quantity[$i];
                $price = (float) $request->purchase_price[$i];
                $discount = (float) ($request->discount[$i] ?? 0);
                $tax = (float) ($request->tax[$i] ?? 0);
                $total = ($qty * $price) - $discount + $tax;

                $subtotal += $qty * $price;
                $discountTotal += $discount;
                $taxTotal += $tax;

                $lines[] = compact('partId', 'qty', 'price', 'discount', 'tax', 'total');
            }

            $grandTotal = $subtotal - $discountTotal + $taxTotal;

            $purchase = Purchase::create([
                'invoice_number' => $this->nextNumber(),
                'supplier_id' => $request->supplier_id,
                'goods_receipt_id' => $request->goods_receipt_id,
                'invoice_date' => $request->invoice_date,
                'due_date' => $request->due_date,
                'warehouse_id' => $request->warehouse_id,
                'subtotal' => $subtotal,
                'discount_total' => $discountTotal,
                'tax_total' => $taxTotal,
                'grand_total' => $grandTotal,
                'paid_amount' => 0,
                'due_amount' => $grandTotal,
                'payment_status' => 'unpaid',
                'stock_received_directly' => ! $linkedToReceivedGrn,
                'notes' => $request->notes,
                'created_by' => $this->user->id,
            ]);

            $warehouse = Warehouse::find($request->warehouse_id);

            foreach ($lines as $line) {
                PurchaseItem::create([
                    'purchase_id' => $purchase->id,
                    'spare_part_id' => $line['partId'],
                    'quantity' => $line['qty'],
                    'purchase_price' => $line['price'],
                    'discount' => $line['discount'],
                    'tax' => $line['tax'],
                    'total' => $line['total'],
                ]);

                // Keep the part's default purchase_price current so future
                // Purchase Order / Purchase lines pre-fill with the latest cost.
                $part = SparePart::find($line['partId']);
                if ($part) {
                    $part->update(['purchase_price' => $line['price']]);
                }

                // Quick-entry path only: no GRN already moved this stock, so
                // the invoice itself is what receives it.
                if (! $linkedToReceivedGrn && $part) {
                    $this->stockService->move(
                        $part,
                        $warehouse,
                        'PURCHASE',
                        $line['qty'],
                        $purchase,
                        "Purchase Invoice {$purchase->invoice_number} (direct entry)"
                    );
                }
            }

            return $purchase;
        });

        AuditLog::record(
            'create',
            'Purchases',
            $purchase,
            $linkedToReceivedGrn
                ? "Created purchase invoice {$purchase->invoice_number} (stock already received via GRN)"
                : "Created purchase invoice {$purchase->invoice_number} — stock received directly",
            new: $purchase->only(['invoice_number', 'grand_total']),
        );

        session()->flash('success', "Purchase Invoice {$purchase->invoice_number} created.");
        return redirect()->route('admin.purchases.show', $purchase->id);
    }

    public function show(int $id)
    {
        if (is_null($this->user) || ! $this->user->can('purchase.view')) {
            abort(403);
        }

        $purchase = Purchase::with(['supplier', 'warehouse', 'items.sparePart', 'payments', 'goodsReceipt'])->findOrFail($id);

        return view('backend.purchases.show', compact('purchase'));
    }

    /**
     * Section 29: record a supplier payment against this invoice.
     */
    public function storePayment(Request $request, int $id)
    {
        if (is_null($this->user) || ! $this->user->can('purchase.edit')) {
            abort(403, 'Sorry !! You are Unauthorized to record Payments !');
        }

        $purchase = Purchase::findOrFail($id);

        $request->validate([
            'amount' => 'required|numeric|min:0.01|max:'.max(0.01, $purchase->due_amount),
            'payment_date' => 'required|date',
            'payment_method' => 'required|in:cash,bank_transfer,card,cheque,online,other',
            'reference_number' => 'nullable|max:60',
            'notes' => 'nullable',
        ]);

        DB::transaction(function () use ($request, $purchase) {
            $purchase->payments()->create([
                'payment_number' => 'PAY-'.now()->format('ymd').'-'.str_pad((\App\Models\PurchasePayment::max('id') ?? 0) + 1, 4, '0', STR_PAD_LEFT),
                'payment_date' => $request->payment_date,
                'amount' => $request->amount,
                'payment_method' => $request->payment_method,
                'reference_number' => $request->reference_number,
                'notes' => $request->notes,
                'created_by' => $this->user->id,
            ]);

            $purchase->refreshPaymentStatus();
        });

        AuditLog::record('create', 'Purchases', $purchase, "Recorded payment of ₹{$request->amount} for invoice {$purchase->invoice_number}");

        session()->flash('success', 'Payment recorded.');
        return back();
    }

    private function nextNumber(): string
    {
        $next = (Purchase::max('id') ?? 0) + 1;
        return 'PINV-'.now()->format('ym').'-'.str_pad($next, 4, '0', STR_PAD_LEFT);
    }
}
