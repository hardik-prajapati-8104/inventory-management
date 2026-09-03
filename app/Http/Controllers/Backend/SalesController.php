<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Customer;
use App\Models\CustomerPayment;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\SparePart;
use App\Models\Warehouse;
use App\Services\StockService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SalesController extends Controller
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
        if (is_null($this->user) || ! $this->user->can('sale.view')) {
            abort(403, 'Sorry !! You are Unauthorized to view Sales !');
        }

        $query = Sale::with(['customer', 'warehouse']);

        if ($status = $request->get('payment_status')) {
            $query->where('payment_status', $status);
        }

        $sales = $query->latest()->paginate(20)->withQueryString();

        return view('backend.sales.index', compact('sales'));
    }

    public function create()
    {
        if (is_null($this->user) || ! $this->user->can('sale.create')) {
            abort(403, 'Sorry !! You are Unauthorized to create Sales !');
        }

        $customers = Customer::where('status', 1)->orderBy('customer_name')->get();
        $warehouses = Warehouse::where('status', 1)->orderBy('name')->get();
        $spareParts = SparePart::orderBy('name')->get(['id', 'name', 'part_number', 'barcode', 'sku', 'retail_price', 'purchase_price', 'current_stock', 'tax_percentage']);

        return view('backend.sales.create', compact('customers', 'warehouses', 'spareParts'));
    }

    /**
     * Section 16: confirming a sale decreases stock, and the system should
     * prevent selling more than available unless negative stock is
     * explicitly enabled — StockService::move() already enforces exactly
     * that via the inventory.allow_negative_stock setting, so this
     * controller just needs to catch the RuntimeException it throws and
     * turn it into a normal validation error instead of a 500.
     */
    public function store(Request $request)
    {
        if (is_null($this->user) || ! $this->user->can('sale.create')) {
            abort(403);
        }

        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'invoice_date' => 'required|date',
            'warehouse_id' => 'required|exists:warehouses,id',
            'notes' => 'nullable',
            'spare_part_id' => 'required|array|min:1',
            'spare_part_id.*' => 'exists:spare_parts,id',
            'quantity' => 'required|array',
            'quantity.*' => 'integer|min:1',
            'selling_price' => 'required|array',
            'selling_price.*' => 'numeric|min:0',
            'discount' => 'nullable|array',
            'tax' => 'nullable|array',
        ]);

        try {
            $sale = DB::transaction(function () use ($request) {
                $subtotal = 0;
                $discountTotal = 0;
                $taxTotal = 0;
                $costTotal = 0;
                $lines = [];

                foreach ($request->spare_part_id as $i => $partId) {
                    $part = SparePart::findOrFail($partId);
                    $qty = (int) $request->quantity[$i];
                    $price = (float) $request->selling_price[$i];
                    $discount = (float) ($request->discount[$i] ?? 0);
                    $tax = (float) ($request->tax[$i] ?? 0);
                    $total = ($qty * $price) - $discount + $tax;

                    $subtotal += $qty * $price;
                    $discountTotal += $discount;
                    $taxTotal += $tax;
                    $costTotal += $qty * (float) $part->purchase_price;

                    $lines[] = ['part' => $part, 'qty' => $qty, 'price' => $price, 'discount' => $discount, 'tax' => $tax, 'total' => $total];
                }

                $grandTotal = $subtotal - $discountTotal + $taxTotal;

                $sale = Sale::create([
                    'invoice_number' => $this->nextNumber(),
                    'customer_id' => $request->customer_id,
                    'invoice_date' => $request->invoice_date,
                    'warehouse_id' => $request->warehouse_id,
                    'salesperson_id' => $this->user->id,
                    'subtotal' => $subtotal,
                    'discount_total' => $discountTotal,
                    'tax_total' => $taxTotal,
                    'grand_total' => $grandTotal,
                    'cost_total' => $costTotal,
                    'paid_amount' => 0,
                    'due_amount' => $grandTotal,
                    'payment_status' => 'unpaid',
                    'notes' => $request->notes,
                    'created_by' => $this->user->id,
                ]);

                $warehouse = Warehouse::find($request->warehouse_id);

                foreach ($lines as $line) {
                    SaleItem::create([
                        'sale_id' => $sale->id,
                        'spare_part_id' => $line['part']->id,
                        'quantity' => $line['qty'],
                        'selling_price' => $line['price'],
                        'cost_price' => $line['part']->purchase_price,
                        'discount' => $line['discount'],
                        'tax' => $line['tax'],
                        'total' => $line['total'],
                    ]);

                    // The only stock-touching call in this whole controller —
                    // throws if it would take stock negative and negative
                    // stock isn't allowed, which the catch block below turns
                    // into a friendly validation error.
                    $this->stockService->move(
                        $line['part'],
                        $warehouse,
                        'SALE',
                        -abs($line['qty']),
                        $sale,
                        "Sales Invoice {$sale->invoice_number}"
                    );
                }

                return $sale;
            });
        } catch (\RuntimeException $e) {
            return back()->withInput()->withErrors(['spare_part_id' => $e->getMessage()]);
        }

        AuditLog::record('create', 'Sales', $sale, "Created sales invoice {$sale->invoice_number}", new: $sale->only(['invoice_number', 'grand_total']));

        session()->flash('success', "Sales Invoice {$sale->invoice_number} created — stock updated.");
        return redirect()->route('admin.sales.show', $sale->id);
    }

    public function show(int $id)
    {
        if (is_null($this->user) || ! $this->user->can('sale.view')) {
            abort(403);
        }

        $sale = Sale::with(['customer', 'warehouse', 'items.sparePart', 'payments', 'salesperson'])->findOrFail($id);

        return view('backend.sales.show', compact('sale'));
    }

    public function storePayment(Request $request, int $id)
    {
        if (is_null($this->user) || ! $this->user->can('sale.edit')) {
            abort(403, 'Sorry !! You are Unauthorized to record Payments !');
        }

        $sale = Sale::findOrFail($id);

        $request->validate([
            'amount' => 'required|numeric|min:0.01|max:'.max(0.01, $sale->due_amount),
            'payment_date' => 'required|date',
            'payment_method' => 'required|in:cash,bank_transfer,card,cheque,online,other',
            'reference_number' => 'nullable|max:60',
            'notes' => 'nullable',
        ]);

        DB::transaction(function () use ($request, $sale) {
            $sale->payments()->create([
                'payment_number' => 'RCPT-'.now()->format('ymd').'-'.str_pad((CustomerPayment::max('id') ?? 0) + 1, 4, '0', STR_PAD_LEFT),
                'payment_date' => $request->payment_date,
                'amount' => $request->amount,
                'payment_method' => $request->payment_method,
                'reference_number' => $request->reference_number,
                'notes' => $request->notes,
                'created_by' => $this->user->id,
            ]);

            $sale->refreshPaymentStatus();
        });

        AuditLog::record('create', 'Sales', $sale, "Recorded payment of ₹{$request->amount} for invoice {$sale->invoice_number}");

        session()->flash('success', 'Payment recorded.');
        return back();
    }

    private function nextNumber(): string
    {
        $next = (Sale::max('id') ?? 0) + 1;
        return 'SINV-'.now()->format('ym').'-'.str_pad($next, 4, '0', STR_PAD_LEFT);
    }
}
