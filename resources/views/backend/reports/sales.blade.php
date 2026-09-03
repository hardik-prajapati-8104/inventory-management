@extends('backend.layouts.master')

@section('title')
Sales Reports - Vehicle Spare Parts Inventory
@endsection

@section('admin-content')

<div class="page-title-area">
    <div class="row align-items-center">
        <div class="col-md-8">
            <h4 class="page-title mb-1">Sales Reports</h4>
            <ul class="breadcrumbs">
                <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li><a href="{{ route('admin.reports.index') }}">Reports</a></li>
                <li><span>Sales</span></li>
            </ul>
        </div>
        <div class="col-md-4 text-md-end mt-2 mt-md-0">
            <a href="{{ route('admin.reports.sales', array_merge(request()->query(), ['export' => 'csv'])) }}" class="btn btn-outline-secondary"><i class="bi bi-download"></i> Export CSV</a>
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
                <label class="small mb-1">Group By</label>
                <select name="group_by" class="form-select">
                    <option value="summary" {{ $groupBy == 'summary' ? 'selected' : '' }}>Date (Summary)</option>
                    <option value="customer" {{ $groupBy == 'customer' ? 'selected' : '' }}>Customer</option>
                    <option value="product" {{ $groupBy == 'product' ? 'selected' : '' }}>Product</option>
                    <option value="category" {{ $groupBy == 'category' ? 'selected' : '' }}>Category</option>
                    <option value="salesperson" {{ $groupBy == 'salesperson' ? 'selected' : '' }}>Salesperson</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-add text-white w-100">Apply</button>
            </div>
        </form>
    </div>
</div>

<div class="row g-3 mb-3">
    <div class="col-md-4">
        <div class="vsp-kpi"><div class="vsp-kpi__icon"><i class="bi bi-cart-check"></i></div><div><div class="vsp-kpi__value">₹{{ number_format($grandTotal, 2) }}</div><div class="vsp-kpi__label">Total Sales ({{ $from }} to {{ $to }})</div></div></div>
    </div>
    @if ($grandProfit)
    <div class="col-md-4">
        <div class="vsp-kpi"><div class="vsp-kpi__icon"><i class="bi bi-piggy-bank"></i></div><div><div class="vsp-kpi__value">₹{{ number_format($grandProfit, 2) }}</div><div class="vsp-kpi__label">Gross Profit</div></div></div>
    </div>
    @endif
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-striped align-middle">
                <thead>
                    <tr>
                        <th>{{ $labelCol }}</th>
                        @if (in_array($groupBy, ['product', 'category']))
                            <th>Qty</th>
                        @else
                            <th>Invoices</th>
                        @endif
                        <th>Total</th>
                        @if (in_array($groupBy, ['summary', 'customer', 'product']))
                            <th>Profit</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @forelse ($rows as $row)
                    <tr>
                        <td>{{ $row->label }}</td>
                        <td>{{ in_array($groupBy, ['product', 'category']) ? $row->qty : $row->invoices }}</td>
                        <td>₹{{ number_format($row->total, 2) }}</td>
                        @if (in_array($groupBy, ['summary', 'customer', 'product']))
                            <td class="{{ ($row->profit ?? 0) >= 0 ? 'text-success' : 'text-danger' }}">₹{{ number_format($row->profit ?? 0, 2) }}</td>
                        @endif
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
