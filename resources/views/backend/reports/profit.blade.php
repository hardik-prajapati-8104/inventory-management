@extends('backend.layouts.master')

@section('title')
Profit Report - Vehicle Spare Parts Inventory
@endsection

@section('admin-content')

<div class="page-title-area">
    <div class="row align-items-center">
        <div class="col-md-8">
            <h4 class="page-title mb-1">Profit Report</h4>
            <ul class="breadcrumbs">
                <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li><a href="{{ route('admin.reports.index') }}">Reports</a></li>
                <li><span>Profit</span></li>
            </ul>
        </div>
        <div class="col-md-4 text-md-end mt-2 mt-md-0">
            <a href="{{ route('admin.reports.profit', array_merge(request()->query(), ['export' => 'csv'])) }}" class="btn btn-outline-secondary"><i class="bi bi-download"></i> Export CSV</a>
        </div>
    </div>
</div>

<div class="card mb-3">
    <div class="card-body">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-3">
                <label class="small mb-1">From</label>
                <input type="date" name="from" class="form-control" value="{{ $from }}">
            </div>
            <div class="col-md-3">
                <label class="small mb-1">To</label>
                <input type="date" name="to" class="form-control" value="{{ $to }}">
            </div>
            <div class="col-md-3">
                <label class="small mb-1">Period</label>
                <select name="period" class="form-select">
                    <option value="daily" {{ $period == 'daily' ? 'selected' : '' }}>Daily</option>
                    <option value="monthly" {{ $period == 'monthly' ? 'selected' : '' }}>Monthly</option>
                    <option value="yearly" {{ $period == 'yearly' ? 'selected' : '' }}>Yearly</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-add text-white w-100">Apply</button>
            </div>
        </form>
    </div>
</div>

<div class="row g-3 mb-3">
    <div class="col-md-3"><div class="vsp-kpi"><div class="vsp-kpi__icon"><i class="bi bi-graph-up"></i></div><div><div class="vsp-kpi__value">₹{{ number_format($totalRevenue, 2) }}</div><div class="vsp-kpi__label">Revenue</div></div></div></div>
    <div class="col-md-3"><div class="vsp-kpi"><div class="vsp-kpi__icon"><i class="bi bi-cash-stack"></i></div><div><div class="vsp-kpi__value">₹{{ number_format($totalCost, 2) }}</div><div class="vsp-kpi__label">Cost of Goods Sold</div></div></div></div>
    <div class="col-md-3"><div class="vsp-kpi"><div class="vsp-kpi__icon"><i class="bi bi-piggy-bank"></i></div><div><div class="vsp-kpi__value">₹{{ number_format($totalProfit, 2) }}</div><div class="vsp-kpi__label">Gross Profit</div></div></div></div>
    <div class="col-md-3"><div class="vsp-kpi"><div class="vsp-kpi__icon"><i class="bi bi-percent"></i></div><div><div class="vsp-kpi__value">{{ $marginPct }}%</div><div class="vsp-kpi__label">Margin</div></div></div></div>
</div>

<div class="card mb-3">
    <div class="card-body">
        <h6 class="mb-3">Profit Over Time</h6>
        <canvas id="profitChart" height="90"></canvas>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <h6 class="mb-3">Top Products by Profit</h6>
        <div class="table-responsive">
            <table class="table table-bordered table-striped align-middle">
                <thead><tr><th>Product</th><th>Qty Sold</th><th>Revenue</th><th>Profit</th></tr></thead>
                <tbody>
                    @forelse ($productRows as $p)
                    <tr>
                        <td>{{ $p->label }}</td>
                        <td>{{ $p->qty }}</td>
                        <td>₹{{ number_format($p->revenue, 2) }}</td>
                        <td class="{{ $p->profit >= 0 ? 'text-success' : 'text-danger' }}">₹{{ number_format($p->profit, 2) }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="text-center text-muted py-4">No sales in this range.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
<script>
    const primary = getComputedStyle(document.documentElement).getPropertyValue('--vsp-primary').trim();
    new Chart(document.getElementById('profitChart'), {
        type: 'line',
        data: {
            labels: @json($rows->pluck('label')),
            datasets: [
                { label: 'Revenue', data: @json($rows->pluck('revenue')), borderColor: primary, backgroundColor: primary + '22', tension: .3, fill: true },
                { label: 'Profit', data: @json($rows->pluck('profit')), borderColor: '#2e8b57', backgroundColor: '#2e8b5722', tension: .3, fill: true },
            ]
        },
        options: { scales: { y: { beginAtZero: true } } }
    });
</script>
@endsection
