@extends('backend.layouts.master')

@section('title')
{{ $supplier->company_name }} Ledger - Vehicle Spare Parts Inventory
@endsection

@section('admin-content')

<div class="page-title-area">
    <div class="row align-items-center">
        <div class="col-md-8">
            <h4 class="page-title mb-1">{{ $supplier->company_name }}</h4>
            <ul class="breadcrumbs">
                <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li><a href="{{ route('admin.suppliers.index') }}">Suppliers</a></li>
                <li><span>Ledger</span></li>
            </ul>
        </div>
        <div class="col-md-4 text-md-end mt-2 mt-md-0">
            <a href="{{ route('admin.suppliers.edit', $supplier->id) }}" class="btn btn-outline-secondary"><i class="bi bi-pencil"></i> Edit Supplier</a>
        </div>
    </div>
</div>

<div class="row g-3 mb-3">
    <div class="col-md-3">
        <div class="vsp-kpi"><div class="vsp-kpi__icon"><i class="bi bi-cash-coin"></i></div><div><div class="vsp-kpi__value">₹{{ number_format($supplier->outstanding_balance, 2) }}</div><div class="vsp-kpi__label">Outstanding Balance</div></div></div>
    </div>
    <div class="col-md-3">
        <div class="vsp-kpi"><div class="vsp-kpi__icon"><i class="bi bi-cart-plus"></i></div><div><div class="vsp-kpi__value">{{ $supplier->purchases->count() }}</div><div class="vsp-kpi__label">Purchase Invoices</div></div></div>
    </div>
    <div class="col-md-3">
        <div class="vsp-kpi"><div class="vsp-kpi__icon"><i class="bi bi-file-earmark-text"></i></div><div><div class="vsp-kpi__value">{{ $supplier->purchaseOrders->count() }}</div><div class="vsp-kpi__label">Purchase Orders</div></div></div>
    </div>
    <div class="col-md-3">
        <div class="vsp-kpi"><div class="vsp-kpi__icon"><i class="bi bi-arrow-counterclockwise"></i></div><div><div class="vsp-kpi__value">{{ $supplier->purchaseReturns->count() }}</div><div class="vsp-kpi__label">Purchase Returns</div></div></div>
    </div>
</div>

<div class="card mb-3">
    <div class="card-body">
        <h6 class="mb-3">Purchase Invoices</h6>
        <table class="table table-bordered table-striped">
            <thead><tr><th>Invoice #</th><th>Date</th><th>Grand Total</th><th>Paid</th><th>Due</th><th>Status</th></tr></thead>
            <tbody>
                @forelse ($supplier->purchases as $p)
                <tr>
                    <td><a href="{{ route('admin.purchases.show', $p->id) }}">{{ $p->invoice_number }}</a></td>
                    <td>{{ $p->invoice_date->format('Y-m-d') }}</td>
                    <td>₹{{ number_format($p->grand_total, 2) }}</td>
                    <td>₹{{ number_format($p->paid_amount, 2) }}</td>
                    <td>₹{{ number_format($p->due_amount, 2) }}</td>
                    <td><span class="badge {{ $p->payment_status === 'paid' ? 'bg-success' : 'bg-warning' }}">{{ ucfirst(str_replace('_',' ',$p->payment_status)) }}</span></td>
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
        <h6 class="mb-3">Purchase Orders</h6>
        <table class="table table-bordered table-striped">
            <thead><tr><th>PO #</th><th>Date</th><th>Status</th></tr></thead>
            <tbody>
                @forelse ($supplier->purchaseOrders as $po)
                <tr>
                    <td><a href="{{ route('admin.purchase-orders.show', $po->id) }}">{{ $po->po_number }}</a></td>
                    <td>{{ $po->po_date->format('Y-m-d') }}</td>
                    <td>{{ ucwords(str_replace('_',' ',$po->status)) }}</td>
                </tr>
                @empty
                <tr><td colspan="3" class="text-center text-muted py-3">No purchase orders yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection
