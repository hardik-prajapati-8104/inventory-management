@extends('backend.layouts.master')

@section('title')
Stock Valuation Report - Vehicle Spare Parts Inventory
@endsection

@section('admin-content')

<div class="page-title-area">
    <div class="row align-items-center">
        <div class="col-md-8">
            <h4 class="page-title mb-1">Stock Valuation Report</h4>
            <ul class="breadcrumbs">
                <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li><a href="{{ route('admin.reports.index') }}">Reports</a></li>
                <li><span>Stock Valuation</span></li>
            </ul>
        </div>
        <div class="col-md-4 text-md-end mt-2 mt-md-0">
            <a href="{{ route('admin.reports.stock-valuation', array_merge(request()->query(), ['export' => 'csv'])) }}" class="btn btn-outline-secondary"><i class="bi bi-download"></i> Export CSV</a>
        </div>
    </div>
</div>

<div class="card mb-3">
    <div class="card-body">
        <form method="GET" class="row g-2">
            <div class="col-md-4">
                <select name="category_id" class="form-select">
                    <option value="">All Categories</option>
                    @foreach ($categories as $cat)
                        <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-add text-white w-100">Filter</button>
            </div>
        </form>
    </div>
</div>

<div class="row g-3 mb-3">
    <div class="col-md-4">
        <div class="vsp-kpi"><div class="vsp-kpi__icon"><i class="bi bi-box-seam"></i></div><div><div class="vsp-kpi__value">{{ number_format($totalUnits) }}</div><div class="vsp-kpi__label">Total Units</div></div></div>
    </div>
    <div class="col-md-4">
        <div class="vsp-kpi"><div class="vsp-kpi__icon"><i class="bi bi-cash-coin"></i></div><div><div class="vsp-kpi__value">₹{{ number_format($totalValue, 2) }}</div><div class="vsp-kpi__label">Total Stock Value</div></div></div>
    </div>
    <div class="col-md-4">
        <div class="vsp-kpi"><div class="vsp-kpi__icon"><i class="bi bi-gear-wide-connected"></i></div><div><div class="vsp-kpi__value">{{ $parts->count() }}</div><div class="vsp-kpi__label">Parts in Stock</div></div></div>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-striped align-middle">
                <thead><tr><th>Part</th><th>Category</th><th>Brand</th><th>Qty</th><th>Unit Cost</th><th>Total Value</th></tr></thead>
                <tbody>
                    @forelse ($parts as $p)
                    <tr>
                        <td>{{ $p->name }} <span class="small text-muted">({{ $p->part_number }})</span></td>
                        <td>{{ $p->category->name ?? '-' }}</td>
                        <td>{{ $p->brand->name ?? '-' }}</td>
                        <td>{{ $p->current_stock }} {{ $p->unit->short_code ?? '' }}</td>
                        <td>₹{{ number_format($p->purchase_price, 2) }}</td>
                        <td>₹{{ number_format($p->current_stock * $p->purchase_price, 2) }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="text-center text-muted py-4">No stock to value yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection
