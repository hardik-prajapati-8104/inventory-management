@extends('backend.layouts.master')

@section('title')
{{ $po->po_number }} - Vehicle Spare Parts Inventory
@endsection

@section('admin-content')

<div class="page-title-area">
    <div class="row align-items-center">
        <div class="col-md-8">
            <h4 class="page-title mb-1">Purchase Order — {{ $po->po_number }}</h4>
            <ul class="breadcrumbs">
                <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li><a href="{{ route('admin.purchase-orders.index') }}">Purchase Orders</a></li>
                <li><span>{{ $po->po_number }}</span></li>
            </ul>
        </div>
        <div class="col-md-4 text-md-end mt-2 mt-md-0">
            @can('purchase-order.approve')
                @if ($po->status === 'pending')
                <form action="{{ route('admin.purchase-orders.approve', $po->id) }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-add text-white"><i class="bi bi-check-lg"></i> Approve</button>
                </form>
                @endif
            @endcan
            @can('purchase.create')
                @if (in_array($po->status, ['approved', 'partially_received']))
                <a href="{{ route('admin.goods-receipts.create', $po->id) }}" class="btn btn-outline-primary"><i class="bi bi-box-arrow-in-down"></i> Receive Goods</a>
                @endif
            @endcan
        </div>
    </div>
</div>

<div class="row g-3 mb-3">
    <div class="col-md-3"><div class="card"><div class="card-body"><div class="small text-muted">Supplier</div><div class="fw-medium">{{ $po->supplier->company_name }}</div></div></div></div>
    <div class="col-md-3"><div class="card"><div class="card-body"><div class="small text-muted">Warehouse</div><div class="fw-medium">{{ $po->warehouse->name }}</div></div></div></div>
    <div class="col-md-3"><div class="card"><div class="card-body"><div class="small text-muted">PO Date</div><div class="fw-medium">{{ $po->po_date->format('Y-m-d') }}</div></div></div></div>
    <div class="col-md-3"><div class="card"><div class="card-body"><div class="small text-muted">Status</div><div class="fw-medium">{{ ucwords(str_replace('_',' ',$po->status)) }}</div></div></div></div>
</div>

<div class="card mb-3">
    <div class="card-body">
        <h6 class="mb-3">Items</h6>
        <table class="table table-bordered">
            <thead><tr><th>Part</th><th>Ordered</th><th>Received</th><th>Pending</th><th>Price</th><th>Total</th></tr></thead>
            <tbody>
                @foreach ($po->items as $item)
                <tr>
                    <td>{{ $item->sparePart->name ?? '-' }} <span class="small text-muted">({{ $item->sparePart->part_number ?? '' }})</span></td>
                    <td>{{ $item->quantity }}</td>
                    <td>{{ $item->quantity_received }}</td>
                    <td>{{ $item->pending_quantity }}</td>
                    <td>₹{{ number_format($item->purchase_price, 2) }}</td>
                    <td>₹{{ number_format($item->total, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div class="text-end"><strong>Order Total: ₹{{ number_format($po->total, 2) }}</strong></div>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <h6 class="mb-3">Goods Receipts</h6>
        <table class="table table-bordered">
            <thead><tr><th>GRN #</th><th>Date</th><th>Received By</th></tr></thead>
            <tbody>
                @forelse ($po->goodsReceipts as $grn)
                <tr>
                    <td><a href="{{ route('admin.goods-receipts.show', $grn->id) }}">{{ $grn->grn_number }}</a></td>
                    <td>{{ $grn->receiving_date->format('Y-m-d') }}</td>
                    <td>{{ $grn->receivedBy->name ?? '-' }}</td>
                </tr>
                @empty
                <tr><td colspan="3" class="text-center text-muted py-3">Nothing received against this PO yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection
