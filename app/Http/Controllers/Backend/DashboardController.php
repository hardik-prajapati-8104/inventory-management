<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Purchase;
use App\Models\PurchaseReturn;
use App\Models\Sale;
use App\Models\SalesReturn;
use App\Models\SparePart;
use App\Models\StockMovement;
use App\Models\Supplier;
use App\Models\Customer;
use App\Services\NotificationService;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public $user;

    public function __construct()
    {
         $this->user = Auth::guard('admin')->user();
    }

    public function index()
    {
        $kpis = [
            'total_spare_parts' => SparePart::count(),
            'total_stock_qty' => (int) SparePart::sum('current_stock'),
            'low_stock_items' => SparePart::lowStock()->where('current_stock', '>', 0)->count(),
            'out_of_stock_items' => SparePart::outOfStock()->count(),
            'total_suppliers' => Supplier::count(),
            'total_customers' => Customer::count(),
            'todays_purchases' => (float) Purchase::whereDate('invoice_date', today())->sum('grand_total'),
            'todays_sales' => (float) Sale::whereDate('invoice_date', today())->sum('grand_total'),
            'monthly_purchases' => (float) Purchase::whereMonth('invoice_date', now()->month)->whereYear('invoice_date', now()->year)->sum('grand_total'),
            'monthly_sales' => (float) Sale::whereMonth('invoice_date', now()->month)->whereYear('invoice_date', now()->year)->sum('grand_total'),
            'purchase_returns' => PurchaseReturn::where('status', 'approved')->count(),
            'sales_returns' => SalesReturn::where('status', 'approved')->count(),
            'stock_value' => (float) (SparePart::selectRaw('SUM(current_stock * purchase_price) as val')->value('val') ?? 0),
            // Section 32: gross profit = grand total - cost of goods sold - tax,
            // summed across every sale this month (cost_total is the
            // purchase_price snapshot captured at sale time, see SaleItem).
            'estimated_profit' => (float) Sale::whereMonth('invoice_date', now()->month)
                ->whereYear('invoice_date', now()->year)
                ->selectRaw('SUM(grand_total - cost_total - tax_total) as profit')
                ->value('profit') ?? 0,
        ];

        // Stock movements over the last 7 days, split into inbound (OPENING_STOCK
        // + returns/transfers in, later PURCHASE too) vs outbound (returns/
        // transfers out + damage, later SALE too) — a real signal even before
        // Purchases/Sales (Phases 4/5) exist, since Adjustments/Transfers already
        // move stock today.
        $days = collect(range(6, 0))->map(fn ($d) => now()->subDays($d)->toDateString());

        $inboundByDay = StockMovement::selectRaw('DATE(created_at) as d, SUM(quantity) as qty')
            ->whereIn('type', StockMovement::inboundTypes())
            ->where('created_at', '>=', now()->subDays(6)->startOfDay())
            ->groupBy('d')->pluck('qty', 'd');

        $outboundByDay = StockMovement::selectRaw('DATE(created_at) as d, SUM(ABS(quantity)) as qty')
            ->whereIn('type', StockMovement::outboundTypes())
            ->where('created_at', '>=', now()->subDays(6)->startOfDay())
            ->groupBy('d')->pluck('qty', 'd');

        $salesChart = [
            'labels' => $days->map(fn ($d) => \Carbon\Carbon::parse($d)->format('D'))->toArray(),
            'data' => $days->map(fn ($d) => (int) ($outboundByDay[$d] ?? 0))->toArray(),
        ];

        $purchaseChart = [
            'labels' => $salesChart['labels'],
            'data' => $days->map(fn ($d) => (int) ($inboundByDay[$d] ?? 0))->toArray(),
        ];

        $stockChart = [
            'labels' => ['In Stock', 'Low Stock', 'Out of Stock', 'Damaged'],
            'data' => [
                SparePart::where('current_stock', '>', 0)->whereColumn('current_stock', '>', 'minimum_stock')->count(),
                $kpis['low_stock_items'],
                $kpis['out_of_stock_items'],
                SparePart::where('damaged_stock', '>', 0)->count(),
            ],
        ];

        return view('backend.dashboard.index', compact('kpis', 'salesChart', 'purchaseChart', 'stockChart'))
            ->with('showLowStockBanner', NotificationService::lowStockNotificationsEnabled() && $kpis['low_stock_items'] > 0);
    }
}
