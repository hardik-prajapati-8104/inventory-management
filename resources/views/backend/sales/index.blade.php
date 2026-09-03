@extends('backend.layouts.master')

@section('title')
Sales - Vehicle Spare Parts Inventory
@endsection

@section('admin-content')

<div class="page-title-area">
    <div class="row align-items-center">
        <div class="col-md-8">
            <h4 class="page-title mb-1">Sales</h4>
            <ul class="breadcrumbs">
                <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li><span>Sales</span></li>
            </ul>
        </div>
        <div class="col-md-4 text-md-end mt-2 mt-md-0">
            @can('sale.create')
            <a href="{{ route('admin.sales.create') }}" class="btn btn-add text-white"><i class="bi bi-plus-lg"></i> New Sale</a>
            @endcan
        </div>
    </div>
</div>

<div class="card mb-3">
    <div class="card-body">
        <form method="GET" class="row g-2">
            <div class="col-md-3">
                <select name="payment_status" class="form-select">
                    <option value="">All Payment Statuses</option>
                    <option value="unpaid" {{ request('payment_status') == 'unpaid' ? 'selected' : '' }}>Unpaid</option>
                    <option value="partially_paid" {{ request('payment_status') == 'partially_paid' ? 'selected' : '' }}>Partially Paid</option>
                    <option value="paid" {{ request('payment_status') == 'paid' ? 'selected' : '' }}>Paid</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-add text-white w-100">Filter</button>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-striped align-middle">
                <thead><tr><th>Invoice #</th><th>Customer</th><th>Date</th><th>Grand Total</th><th>Paid</th><th>Due</th><th>Status</th><th width="8%">Action</th></tr></thead>
                <tbody>
                    @forelse ($sales as $s)
                    <tr>
                        <td>{{ $s->invoice_number }}</td>
                        <td>{{ $s->customer->customer_name ?? '-' }}</td>
                        <td class="small">{{ $s->invoice_date->format('Y-m-d') }}</td>
                        <td>₹{{ number_format($s->grand_total, 2) }}</td>
                        <td>₹{{ number_format($s->paid_amount, 2) }}</td>
                        <td>₹{{ number_format($s->due_amount, 2) }}</td>
                        <td>
                            @php $badge = match($s->payment_status) { 'paid' => 'bg-success', 'partially_paid' => 'bg-warning', default => 'bg-secondary' }; @endphp
                            <span class="badge {{ $badge }}">{{ ucwords(str_replace('_', ' ', $s->payment_status)) }}</span>
                        </td>
                        <td><a href="{{ route('admin.sales.show', $s->id) }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-eye"></i></a></td>
                    </tr>
                    @empty
                    <tr><td colspan="8" class="text-center text-muted py-4">No sales yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mt-3 gap-2">
            <div class="text-muted small">
                Showing {{ $sales->firstItem() ?? 0 }}
                to {{ $sales->lastItem() ?? 0 }}
                of {{ $sales->total() }} results
            </div>

            <div>
                {{ $sales->withQueryString()->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
</div>

@endsection
