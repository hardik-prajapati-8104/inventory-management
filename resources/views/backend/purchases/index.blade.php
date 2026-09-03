@extends('backend.layouts.master')

@section('title')
Purchases - Vehicle Spare Parts Inventory
@endsection

@section('admin-content')

<div class="page-title-area">
    <div class="row align-items-center">
        <div class="col-md-8">
            <h4 class="page-title mb-1">Purchases</h4>
            <ul class="breadcrumbs">
                <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li><span>Purchases</span></li>
            </ul>
        </div>
        <div class="col-md-4 text-md-end mt-2 mt-md-0">
            @can('purchase.create')
            <a href="{{ route('admin.purchases.create') }}" class="btn btn-add text-white"><i class="bi bi-plus-lg"></i> Quick Purchase Entry</a>
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
                    <option value="overdue" {{ request('payment_status') == 'overdue' ? 'selected' : '' }}>Overdue</option>
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
                <thead><tr><th>Invoice #</th><th>Supplier</th><th>Date</th><th>Grand Total</th><th>Paid</th><th>Due</th><th>Status</th><th width="8%">Action</th></tr></thead>
                <tbody>
                    @forelse ($purchases as $p)
                    <tr>
                        <td>{{ $p->invoice_number }}</td>
                        <td>{{ $p->supplier->company_name ?? '-' }}</td>
                        <td class="small">{{ $p->invoice_date->format('Y-m-d') }}</td>
                        <td>₹{{ number_format($p->grand_total, 2) }}</td>
                        <td>₹{{ number_format($p->paid_amount, 2) }}</td>
                        <td>₹{{ number_format($p->due_amount, 2) }}</td>
                        <td>
                            @php
                                $badge = match($p->payment_status) { 'paid' => 'bg-success', 'partially_paid' => 'bg-warning', 'overdue' => 'bg-danger', default => 'bg-secondary' };
                            @endphp
                            <span class="badge {{ $badge }}">{{ ucwords(str_replace('_', ' ', $p->payment_status)) }}</span>
                        </td>
                        <td><a href="{{ route('admin.purchases.show', $p->id) }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-eye"></i></a></td>
                    </tr>
                    @empty
                    <tr><td colspan="8" class="text-center text-muted py-4">No purchase invoices yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $purchases->links() }}
    </div>
</div>

@endsection
