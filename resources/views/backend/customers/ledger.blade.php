@extends('backend.layouts.master')

@section('title')
{{ $customer->customer_name }} Ledger - Vehicle Spare Parts Inventory
@endsection

@section('admin-content')

<div class="page-title-area">
    <div class="row align-items-center">
        <div class="col-md-8">
            <h4 class="page-title mb-1">{{ $customer->customer_name }}</h4>
            <ul class="breadcrumbs">
                <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li><a href="{{ route('admin.customers.index') }}">Customers</a></li>
                <li><span>Ledger</span></li>
            </ul>
        </div>
        <div class="col-md-4 text-md-end mt-2 mt-md-0">
            <a href="{{ route('admin.customers.edit', $customer->id) }}" class="btn btn-outline-secondary"><i class="bi bi-pencil"></i> Edit Customer</a>
        </div>
    </div>
</div>

<div class="row g-3 mb-3">
    <div class="col-md-4">
        <div class="vsp-kpi"><div class="vsp-kpi__icon"><i class="bi bi-cash-coin"></i></div><div><div class="vsp-kpi__value">₹{{ number_format($customer->outstanding_balance, 2) }}</div><div class="vsp-kpi__label">Outstanding Balance</div></div></div>
    </div>
    <div class="col-md-4">
        <div class="vsp-kpi"><div class="vsp-kpi__icon"><i class="bi bi-cart-check"></i></div><div><div class="vsp-kpi__value">{{ $customer->sales->count() }}</div><div class="vsp-kpi__label">Sales Invoices</div></div></div>
    </div>
    <div class="col-md-4">
        <div class="vsp-kpi"><div class="vsp-kpi__icon"><i class="bi bi-arrow-return-left"></i></div><div><div class="vsp-kpi__value">{{ $customer->salesReturns->count() }}</div><div class="vsp-kpi__label">Sales Returns</div></div></div>
    </div>
</div>

<div class="card mb-3">
    <div class="card-body">
        <h6 class="mb-3">Sales Invoices</h6>
        <table class="table table-bordered table-striped">
            <thead><tr><th>Invoice #</th><th>Date</th><th>Grand Total</th><th>Paid</th><th>Due</th><th>Status</th></tr></thead>
            <tbody>
                @forelse ($customer->sales as $s)
                <tr>
                    <td><a href="{{ route('admin.sales.show', $s->id) }}">{{ $s->invoice_number }}</a></td>
                    <td>{{ $s->invoice_date->format('Y-m-d') }}</td>
                    <td>₹{{ number_format($s->grand_total, 2) }}</td>
                    <td>₹{{ number_format($s->paid_amount, 2) }}</td>
                    <td>₹{{ number_format($s->due_amount, 2) }}</td>
                    <td><span class="badge {{ $s->payment_status === 'paid' ? 'bg-success' : 'bg-warning' }}">{{ ucfirst(str_replace('_',' ',$s->payment_status)) }}</span></td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center text-muted py-3">No invoices yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <h6 class="mb-3">Sales Returns</h6>
        <table class="table table-bordered table-striped">
            <thead><tr><th>Return #</th><th>Date</th><th>Status</th></tr></thead>
            <tbody>
                @forelse ($customer->salesReturns as $r)
                <tr>
                    <td>{{ $r->return_number }}</td>
                    <td>{{ $r->return_date->format('Y-m-d') }}</td>
                    <td>{{ ucfirst($r->status) }}</td>
                </tr>
                @empty
                <tr><td colspan="3" class="text-center text-muted py-3">No returns yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection
