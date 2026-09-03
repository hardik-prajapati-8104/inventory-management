@extends('backend.layouts.master')

@section('title')
Dashboard - Vehicle Spare Parts Inventory
@endsection

@section('admin-content')

<div class="page-title-area">
    <div class="row align-items-center">
        <div class="col-md-8">
            <h4 class="page-title mb-1">Dashboard</h4>
            <ul class="breadcrumbs">
                <li><span>Overview</span></li>
            </ul>
        </div>
        <div class="col-md-4 text-md-end mt-2 mt-md-0">
            <a href="{{ route('admin.spare-parts.create') }}" class="btn btn-add text-white btn-sm"><i class="bi bi-plus-lg"></i> Add Spare Part</a>
            <a href="{{ route('admin.purchases.create') }}" class="btn btn-add text-white btn-sm"><i class="bi bi-cart-plus"></i> New Purchase</a>
            <a href="{{ route('admin.sales.create') }}" class="btn btn-add text-white btn-sm"><i class="bi bi-cart-check"></i> New Sale</a>
        </div>
    </div>
</div>

@if ($showLowStockBanner)
<div class="mb-3">
    <a href="{{ route('admin.stock.low') }}" class="vsp-low-stock-banner">
        <span class="vsp-low-stock-banner__icon"><i class="bi bi-exclamation-triangle-fill"></i></span>
        <span class="vsp-low-stock-banner__text">
            <strong>⚠ {{ $kpis['low_stock_items'] }} {{ $kpis['low_stock_items'] === 1 ? 'Product Is' : 'Products Are' }} Low in Stock</strong>
            <span class="d-none d-sm-inline">— review and reorder before they run out.</span>
        </span>
        <span class="vsp-low-stock-banner__cta">View Low Stock <i class="bi bi-arrow-right"></i></span>
    </a>
</div>
@endif

{{-- KPI Cards --}}
<div class="row g-3 mb-3">
    @php
        $cards = [
            ['icon' => 'bi-gear-wide-connected', 'label' => 'Total Spare Parts', 'value' => $kpis['total_spare_parts']],
            ['icon' => 'bi-box-seam', 'label' => 'Total Stock Quantity', 'value' => $kpis['total_stock_qty']],
            ['icon' => 'bi-exclamation-triangle', 'label' => 'Low Stock Items', 'value' => $kpis['low_stock_items']],
            ['icon' => 'bi-x-octagon', 'label' => 'Out of Stock', 'value' => $kpis['out_of_stock_items']],
            ['icon' => 'bi-people', 'label' => 'Total Suppliers', 'value' => $kpis['total_suppliers']],
            ['icon' => 'bi-person-badge', 'label' => 'Total Customers', 'value' => $kpis['total_customers']],
            ['icon' => 'bi-cart-plus', 'label' => "Today's Purchases", 'value' => $kpis['todays_purchases'], 'currency' => true],
            ['icon' => 'bi-cart-check', 'label' => "Today's Sales", 'value' => $kpis['todays_sales'], 'currency' => true],
            ['icon' => 'bi-graph-up', 'label' => 'Monthly Purchases', 'value' => $kpis['monthly_purchases'], 'currency' => true],
            ['icon' => 'bi-graph-up-arrow', 'label' => 'Monthly Sales', 'value' => $kpis['monthly_sales'], 'currency' => true],
            ['icon' => 'bi-arrow-counterclockwise', 'label' => 'Purchase Returns', 'value' => $kpis['purchase_returns']],
            ['icon' => 'bi-arrow-return-left', 'label' => 'Sales Returns', 'value' => $kpis['sales_returns']],
            ['icon' => 'bi-cash-coin', 'label' => 'Stock Value', 'value' => $kpis['stock_value'], 'currency' => true],
            ['icon' => 'bi-piggy-bank', 'label' => 'Estimated Profit', 'value' => $kpis['estimated_profit'], 'currency' => true],
        ];
    @endphp

    @foreach ($cards as $card)
        <div class="col-6 col-md-4 col-xl-3">
            <div class="vsp-kpi">
                <div class="vsp-kpi__icon"><i class="bi {{ $card['icon'] }}"></i></div>
                <div>
                    <div class="vsp-kpi__value">{{ ($card['currency'] ?? false) ? '₹' : '' }}{{ number_format($card['value'], ($card['currency'] ?? false) ? 2 : 0) }}</div>
                    <div class="vsp-kpi__label">{{ $card['label'] }}</div>
                </div>
            </div>
        </div>
    @endforeach
</div>

{{-- Charts --}}
<div class="row g-3">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-body">
                <h6 class="mb-1">Stock Outbound — Last 7 Days</h6>
                <p class="small text-muted mb-3">Sales, purchase returns, transfers out &amp; damage.</p>
                <canvas id="salesChart" height="180"></canvas>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card">
            <div class="card-body">
                <h6 class="mb-1">Stock Inbound — Last 7 Days</h6>
                <p class="small text-muted mb-3">Opening stock, purchases, sales returns (resalable) &amp; transfers in.</p>
                <canvas id="purchaseChart" height="180"></canvas>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card">
            <div class="card-body">
                <h6 class="mb-3">Stock Status</h6>
                <canvas id="stockChart" height="180"></canvas>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card h-100">
            <div class="card-body">
                <h6 class="mb-3">Getting Started</h6>
                <p class="text-muted small mb-2">
                    All core modules are live — every KPI and chart above reads real
                    data, including Estimated Profit for the first time.
                </p>
                <ul class="small text-muted mb-0">
                    <li class="text-decoration-line-through">Phase 2 — Master Data (Spare Parts, Categories, Vehicles)</li>
                    <li class="text-decoration-line-through">Phase 3 — Inventory (Warehouses, Stock Movement)</li>
                    <li class="text-decoration-line-through">Phase 4 — Purchasing (Suppliers, PO, GRN, Invoices)</li>
                    <li class="text-decoration-line-through">Phase 5 — Sales (Customers, Invoices, Returns)</li>
                    <li>Phase 6 — Reports</li>
                </ul>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
<script>
    const primary = getComputedStyle(document.documentElement).getPropertyValue('--vsp-primary').trim();

    new Chart(document.getElementById('salesChart'), {
        type: 'line',
        data: {
            labels: @json($salesChart['labels']),
            datasets: [{ label: 'Sales', data: @json($salesChart['data']), borderColor: primary, backgroundColor: primary + '33', tension: .35, fill: true }]
        },
        options: { plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true } } }
    });

    new Chart(document.getElementById('purchaseChart'), {
        type: 'bar',
        data: {
            labels: @json($purchaseChart['labels']),
            datasets: [{ label: 'Purchases', data: @json($purchaseChart['data']), backgroundColor: primary }]
        },
        options: { plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true } } }
    });

    new Chart(document.getElementById('stockChart'), {
        type: 'doughnut',
        data: {
            labels: @json($stockChart['labels']),
            datasets: [{ data: @json($stockChart['data']), backgroundColor: ['#2e8b57', '#d4a017', '#c0392b', '#8c8272'] }]
        },
        options: { plugins: { legend: { position: 'bottom' } } }
    });
</script>
@endsection
