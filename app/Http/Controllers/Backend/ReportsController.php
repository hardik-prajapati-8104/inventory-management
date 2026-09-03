<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\SparePart;
use App\Models\Supplier;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReportsController extends Controller
{
    public $user;

    public function __construct()
    {
         $this->user = Auth::guard('admin')->user();
    }

    private function guard(): void
    {
        if (is_null($this->user) || ! $this->user->can('report.view')) {
            abort(403, 'Sorry !! You are Unauthorized to view Reports !');
        }
    }

    public function index()
    {
        $this->guard();

        return view('backend.reports.index');
    }

    /**
     * Section 18/26/43: current stock valued at the recommended Weighted
     * Average Cost method — approximated here as spare_parts.purchase_price
     * (already kept current on every purchase line, see PurchasesController/
     * GoodsReceiptsController) times current_stock, per part.
     */
    public function stockValuation(Request $request)
    {
        $this->guard();

        $query = SparePart::with(['category', 'brand', 'unit'])->where('current_stock', '>', 0);

        if ($categoryId = $request->get('category_id')) {
            $query->where('category_id', $categoryId);
        }

        $parts = $query->orderByDesc('current_stock')->get();
        $totalValue = $parts->sum(fn ($p) => $p->current_stock * $p->purchase_price);
        $totalUnits = $parts->sum('current_stock');

        if ($request->get('export') === 'csv') {
            return $this->csv('stock-valuation', ['Part', 'Part Number', 'Category', 'Qty', 'Unit Cost', 'Total Value'],
                $parts->map(fn ($p) => [$p->name, $p->part_number, $p->category->name ?? '', $p->current_stock, $p->purchase_price, $p->current_stock * $p->purchase_price]));
        }

        $categories = \App\Models\Category::topLevel()->orderBy('name')->get();

        return view('backend.reports.stock-valuation', compact('parts', 'totalValue', 'totalUnits', 'categories'));
    }

    /**
     * Section 31 Purchase Reports: Summary / by Supplier / by Product / by
     * Category / by Date — one page, switched with ?group_by=.
     */
    public function purchases(Request $request)
    {
        $this->guard();

        [$from, $to] = $this->dateRange($request);
        $groupBy = $request->get('group_by', 'summary');

        $baseQuery = Purchase::whereBetween('invoice_date', [$from, $to]);

        $rows = collect();
        $labelCol = 'Group';

        if ($groupBy === 'supplier') {
            $labelCol = 'Supplier';
            $rows = $baseQuery->join('suppliers', 'suppliers.id', '=', 'purchases.supplier_id')
                ->select('suppliers.company_name as label', DB::raw('COUNT(*) as invoices'), DB::raw('SUM(grand_total) as total'))
                ->groupBy('suppliers.company_name')->orderByDesc('total')->get();
        } elseif ($groupBy === 'product') {
            $labelCol = 'Product';
            $rows = PurchaseItem::join('purchases', 'purchases.id', '=', 'purchase_items.purchase_id')
                ->join('spare_parts', 'spare_parts.id', '=', 'purchase_items.spare_part_id')
                ->whereBetween('purchases.invoice_date', [$from, $to])
                ->select('spare_parts.name as label', DB::raw('SUM(purchase_items.quantity) as qty'), DB::raw('SUM(purchase_items.total) as total'))
                ->groupBy('spare_parts.name')->orderByDesc('total')->get();
        } elseif ($groupBy === 'category') {
            $labelCol = 'Category';
            $rows = PurchaseItem::join('purchases', 'purchases.id', '=', 'purchase_items.purchase_id')
                ->join('spare_parts', 'spare_parts.id', '=', 'purchase_items.spare_part_id')
                ->leftJoin('categories', 'categories.id', '=', 'spare_parts.category_id')
                ->whereBetween('purchases.invoice_date', [$from, $to])
                ->select(DB::raw("COALESCE(categories.name, 'Uncategorized') as label"), DB::raw('SUM(purchase_items.quantity) as qty'), DB::raw('SUM(purchase_items.total) as total'))
                ->groupBy('label')->orderByDesc('total')->get();
        } else {
            $groupBy = 'summary';
            $rows = $baseQuery->select(
                DB::raw('DATE(invoice_date) as label'),
                DB::raw('COUNT(*) as invoices'),
                DB::raw('SUM(grand_total) as total')
            )->groupBy('label')->orderByDesc('label')->get();
            $labelCol = 'Date';
        }

        $grandTotal = $rows->sum('total');

        if ($request->get('export') === 'csv') {
            $headers = $groupBy === 'summary' ? [$labelCol, 'Invoices', 'Total'] : ($groupBy === 'supplier' ? [$labelCol, 'Invoices', 'Total'] : [$labelCol, 'Qty', 'Total']);
            return $this->csv('purchases-'.$groupBy, $headers, $rows->map(fn ($r) => array_values((array) $r)));
        }

        return view('backend.reports.purchases', compact('rows', 'labelCol', 'groupBy', 'grandTotal', 'from', 'to'));
    }

    /**
     * Section 31 Sales Reports: Summary / by Customer / by Product / by
     * Category / by Salesperson.
     */
    public function sales(Request $request)
    {
        $this->guard();

        [$from, $to] = $this->dateRange($request);
        $groupBy = $request->get('group_by', 'summary');

        $baseQuery = Sale::whereBetween('invoice_date', [$from, $to]);

        $rows = collect();
        $labelCol = 'Group';

        if ($groupBy === 'customer') {
            $labelCol = 'Customer';
            $rows = $baseQuery->join('customers', 'customers.id', '=', 'sales.customer_id')
                ->select('customers.customer_name as label', DB::raw('COUNT(*) as invoices'), DB::raw('SUM(grand_total) as total'), DB::raw('SUM(grand_total - cost_total - tax_total) as profit'))
                ->groupBy('customers.customer_name')->orderByDesc('total')->get();
        } elseif ($groupBy === 'product') {
            $labelCol = 'Product';
            $rows = SaleItem::join('sales', 'sales.id', '=', 'sale_items.sale_id')
                ->join('spare_parts', 'spare_parts.id', '=', 'sale_items.spare_part_id')
                ->whereBetween('sales.invoice_date', [$from, $to])
                ->select('spare_parts.name as label', DB::raw('SUM(sale_items.quantity) as qty'), DB::raw('SUM(sale_items.total) as total'), DB::raw('SUM((sale_items.selling_price - sale_items.cost_price) * sale_items.quantity) as profit'))
                ->groupBy('spare_parts.name')->orderByDesc('total')->get();
        } elseif ($groupBy === 'category') {
            $labelCol = 'Category';
            $rows = SaleItem::join('sales', 'sales.id', '=', 'sale_items.sale_id')
                ->join('spare_parts', 'spare_parts.id', '=', 'sale_items.spare_part_id')
                ->leftJoin('categories', 'categories.id', '=', 'spare_parts.category_id')
                ->whereBetween('sales.invoice_date', [$from, $to])
                ->select(DB::raw("COALESCE(categories.name, 'Uncategorized') as label"), DB::raw('SUM(sale_items.quantity) as qty'), DB::raw('SUM(sale_items.total) as total'))
                ->groupBy('label')->orderByDesc('total')->get();
        } elseif ($groupBy === 'salesperson') {
            $labelCol = 'Salesperson';
            $rows = $baseQuery->join('admins', 'admins.id', '=', 'sales.salesperson_id')
                ->select(DB::raw("CONCAT(admins.first_name, ' ', admins.last_name) as label"), DB::raw('COUNT(*) as invoices'), DB::raw('SUM(grand_total) as total'))
                ->groupBy('label')->orderByDesc('total')->get();
        } else {
            $groupBy = 'summary';
            $labelCol = 'Date';
            $rows = $baseQuery->select(
                DB::raw('DATE(invoice_date) as label'),
                DB::raw('COUNT(*) as invoices'),
                DB::raw('SUM(grand_total) as total'),
                DB::raw('SUM(grand_total - cost_total - tax_total) as profit')
            )->groupBy('label')->orderByDesc('label')->get();
        }

        $grandTotal = $rows->sum('total');
        $grandProfit = $rows->sum('profit');

        if ($request->get('export') === 'csv') {
            $headers = array_keys((array) ($rows->first() ?? []));
            return $this->csv('sales-'.$groupBy, $headers ?: [$labelCol], $rows->map(fn ($r) => array_values((array) $r)));
        }

        return view('backend.reports.sales', compact('rows', 'labelCol', 'groupBy', 'grandTotal', 'grandProfit', 'from', 'to'));
    }

    /**
     * Section 32: profit report, daily/monthly/yearly or by product/invoice.
     */
    public function profit(Request $request)
    {
        $this->guard();

        [$from, $to] = $this->dateRange($request);
        $period = $request->get('period', 'daily'); // daily | monthly | yearly

        $dateFormat = match ($period) {
            'yearly' => '%Y',
            'monthly' => '%Y-%m',
            default => '%Y-%m-%d',
        };

        $rows = Sale::whereBetween('invoice_date', [$from, $to])
            ->select(
                DB::raw("DATE_FORMAT(invoice_date, '$dateFormat') as label"),
                DB::raw('COUNT(*) as invoices'),
                DB::raw('SUM(grand_total) as revenue'),
                DB::raw('SUM(cost_total) as cost'),
                DB::raw('SUM(grand_total - cost_total - tax_total) as profit')
            )
            ->groupBy('label')->orderBy('label')->get();

        $totalRevenue = $rows->sum('revenue');
        $totalCost = $rows->sum('cost');
        $totalProfit = $rows->sum('profit');
        $marginPct = $totalRevenue > 0 ? round(($totalProfit / $totalRevenue) * 100, 2) : 0;

        // Top/bottom margin products this range, for the "Product Profit" view.
        $productRows = SaleItem::join('sales', 'sales.id', '=', 'sale_items.sale_id')
            ->join('spare_parts', 'spare_parts.id', '=', 'sale_items.spare_part_id')
            ->whereBetween('sales.invoice_date', [$from, $to])
            ->select(
                'spare_parts.name as label',
                DB::raw('SUM(sale_items.quantity) as qty'),
                DB::raw('SUM(sale_items.total) as revenue'),
                DB::raw('SUM((sale_items.selling_price - sale_items.cost_price) * sale_items.quantity) as profit')
            )
            ->groupBy('spare_parts.name')->orderByDesc('profit')->limit(20)->get();

        if ($request->get('export') === 'csv') {
            return $this->csv('profit-'.$period, ['Period', 'Invoices', 'Revenue', 'Cost', 'Profit'], $rows->map(fn ($r) => array_values((array) $r)));
        }

        return view('backend.reports.profit', compact('rows', 'productRows', 'period', 'from', 'to', 'totalRevenue', 'totalCost', 'totalProfit', 'marginPct'));
    }

    /**
     * Section 50: Supplier Price Comparison. Reuses purchase_items history —
     * no new schema needed, since every purchase line already records which
     * supplier, at what price, and when.
     */
    public function supplierPriceComparison(Request $request)
    {
        $this->guard();

        $sparePartId = $request->get('spare_part_id');
        $rows = collect();
        $part = null;

        if ($sparePartId) {
            $part = SparePart::find($sparePartId);

            if ($part) {
                $rows = PurchaseItem::join('purchases', 'purchases.id', '=', 'purchase_items.purchase_id')
                    ->join('suppliers', 'suppliers.id', '=', 'purchases.supplier_id')
                    ->where('purchase_items.spare_part_id', $sparePartId)
                    ->select(
                        'suppliers.id as supplier_id',
                        'suppliers.company_name',
                        DB::raw('COUNT(*) as purchase_count'),
                        DB::raw('SUM(purchase_items.quantity) as total_qty'),
                        DB::raw('MIN(purchase_items.purchase_price) as lowest_price'),
                        DB::raw('MAX(purchase_items.purchase_price) as highest_price'),
                        DB::raw('AVG(purchase_items.purchase_price) as avg_price'),
                        DB::raw('MAX(purchases.invoice_date) as last_purchase_date')
                    )
                    ->groupBy('suppliers.id', 'suppliers.company_name')
                    ->orderBy('lowest_price')
                    ->get();

                // The price paid on the single most recent purchase from
                // each supplier, shown alongside the aggregate stats since
                // "cheapest historically" and "cheapest right now" can differ.
                foreach ($rows as $row) {
                    $row->last_price = PurchaseItem::join('purchases', 'purchases.id', '=', 'purchase_items.purchase_id')
                        ->where('purchase_items.spare_part_id', $sparePartId)
                        ->where('purchases.supplier_id', $row->supplier_id)
                        ->orderByDesc('purchases.invoice_date')
                        ->value('purchase_items.purchase_price');
                }
            }
        }

        $spareParts = SparePart::orderBy('name')->get(['id', 'name', 'part_number']);

        return view('backend.reports.supplier-price-comparison', compact('spareParts', 'part', 'rows'));
    }

    public function outstandingSuppliers()
    {
        $this->guard();

        $suppliers = Supplier::withSum(['purchases as due_sum' => fn ($q) => $q->where('due_amount', '>', 0)], 'due_amount')
            ->having('due_sum', '>', 0)
            ->orderByDesc('due_sum')
            ->get();

        $total = $suppliers->sum('due_sum');

        return view('backend.reports.outstanding-suppliers', compact('suppliers', 'total'));
    }

    public function outstandingCustomers()
    {
        $this->guard();

        $customers = Customer::withSum(['sales as due_sum' => fn ($q) => $q->where('due_amount', '>', 0)], 'due_amount')
            ->having('due_sum', '>', 0)
            ->orderByDesc('due_sum')
            ->get();

        $total = $customers->sum('due_sum');

        return view('backend.reports.outstanding-customers', compact('customers', 'total'));
    }

    private function dateRange(Request $request): array
    {
        $from = $request->get('from') ?: now()->startOfMonth()->toDateString();
        $to = $request->get('to') ?: now()->toDateString();

        return [$from, $to];
    }

    private function csv(string $filename, array $headers, $rows)
    {
        return response()->streamDownload(function () use ($headers, $rows) {
            $out = fopen('php://output', 'w');
            fputcsv($out, $headers);
            foreach ($rows as $row) {
                fputcsv($out, $row);
            }
            fclose($out);
        }, $filename.'-'.now()->format('Ymd_His').'.csv', ['Content-Type' => 'text/csv']);
    }
}
