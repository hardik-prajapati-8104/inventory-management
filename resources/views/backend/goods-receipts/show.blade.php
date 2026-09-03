@extends('backend.layouts.master')

@section('title')
{{ $grn->grn_number }} - Vehicle Spare Parts Inventory
@endsection

@section('admin-content')

<div class="page-title-area">
    <div class="row align-items-center">
        <div class="col-md-8">
            <h4 class="page-title mb-1">Goods Receipt — {{ $grn->grn_number }}</h4>
            <ul class="breadcrumbs">
                <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li><a href="{{ route('admin.goods-receipts.index') }}">Goods Receipts</a></li>
                <li><span>{{ $grn->grn_number }}</span></li>
            </ul>
        </div>
        <div class="col-md-4 text-md-end mt-2 mt-md-0">
            @can('purchase.create')
            <a href="{{ route('admin.purchases.create', ['goods_receipt_id' => $grn->id]) }}" class="btn btn-add text-white"><i class="bi bi-receipt"></i> Raise Purchase Invoice</a>
            @endcan
        </div>
    </div>
</div>

<div class="row g-3 mb-3">
    <div class="col-md-3"><div class="card"><div class="card-body"><div class="small text-muted">PO</div><div class="fw-medium">{{ $grn->purchaseOrder->po_number ?? '-' }}</div></div></div></div>
    <div class="col-md-3"><div class="card"><div class="card-body"><div class="small text-muted">Supplier</div><div class="fw-medium">{{ $grn->supplier->company_name }}</div></div></div></div>
    <div class="col-md-3"><div class="card"><div class="card-body"><div class="small text-muted">Warehouse</div><div class="fw-medium">{{ $grn->warehouse->name }}</div></div></div></div>
    <div class="col-md-3"><div class="card"><div class="card-body"><div class="small text-muted">Received</div><div class="fw-medium">{{ $grn->receiving_date->format('Y-m-d') }} by {{ $grn->receivedBy->name ?? '-' }}</div></div></div></div>
</div>

<div class="card">
    <div class="card-body">
        <table class="table table-bordered">
            <thead><tr><th>Part</th><th>Ordered</th><th>Received</th><th>Damaged</th><th>Short</th></tr></thead>
            <tbody>
                @foreach ($grn->items as $item)
                <tr>
                    <td>{{ $item->sparePart->name ?? '-' }} <span class="small text-muted">({{ $item->sparePart->part_number ?? '' }})</span></td>
                    <td>{{ $item->quantity_ordered }}</td>
                    <td>{{ $item->quantity_received }}</td>
                    <td>{{ $item->quantity_damaged }}</td>
                    <td>{{ $item->quantity_short }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

@endsection
