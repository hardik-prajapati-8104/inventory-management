@extends('backend.layouts.master')

@section('title')
Purchase Orders - Vehicle Spare Parts Inventory
@endsection

@section('admin-content')

<div class="page-title-area">
    <div class="row align-items-center">
        <div class="col-md-8">
            <h4 class="page-title mb-1">Purchase Orders</h4>
            <ul class="breadcrumbs">
                <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li><span>Purchase Orders</span></li>
            </ul>
        </div>
        <div class="col-md-4 text-md-end mt-2 mt-md-0">
            @can('purchase-order.create')
            <a href="{{ route('admin.purchase-orders.create') }}" class="btn btn-add text-white"><i class="bi bi-plus-lg"></i> New PO</a>
            @endcan
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-striped align-middle">
                <thead><tr><th>PO #</th><th>Supplier</th><th>Warehouse</th><th>Items</th><th>Date</th><th>Status</th><th width="16%">Action</th></tr></thead>
                <tbody>
                    @forelse ($purchaseOrders as $po)
                    <tr>
                        <td><a href="{{ route('admin.purchase-orders.show', $po->id) }}">{{ $po->po_number }}</a></td>
                        <td>{{ $po->supplier->company_name ?? '-' }}</td>
                        <td>{{ $po->warehouse->name ?? '-' }}</td>
                        <td>{{ $po->items->count() }}</td>
                        <td class="small">{{ $po->po_date->format('Y-m-d') }}</td>
                        <td>
                            @php
                                $badge = match($po->status) {
                                    'draft' => 'bg-secondary', 'pending' => 'bg-secondary',
                                    'approved' => 'bg-primary', 'partially_received' => 'bg-warning',
                                    'received' => 'bg-success', 'cancelled' => 'bg-danger', default => 'bg-secondary',
                                };
                            @endphp
                            <span class="badge {{ $badge }}">{{ ucwords(str_replace('_', ' ', $po->status)) }}</span>
                        </td>
                        <td>
                            <a href="{{ route('admin.purchase-orders.show', $po->id) }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-eye"></i></a>
                            @can('purchase-order.approve')
                                @if ($po->status === 'pending')
                                <form action="{{ route('admin.purchase-orders.approve', $po->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-outline-success"><i class="bi bi-check-lg"></i> Approve</button>
                                </form>
                                @endif
                            @endcan
                            @can('purchase.create')
                                @if (in_array($po->status, ['approved', 'partially_received']))
                                <a href="{{ route('admin.goods-receipts.create', $po->id) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-box-arrow-in-down"></i> Receive</a>
                                @endif
                            @endcan
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="text-center text-muted py-4">No purchase orders yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $purchaseOrders->links() }}
    </div>
</div>

@endsection
