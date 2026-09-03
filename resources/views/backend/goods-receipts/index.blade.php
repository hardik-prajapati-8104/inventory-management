@extends('backend.layouts.master')

@section('title')
Goods Receipts - Vehicle Spare Parts Inventory
@endsection

@section('admin-content')

<div class="page-title-area">
    <div class="row align-items-center">
        <div class="col-12">
            <h4 class="page-title mb-1">Goods Receipts</h4>
            <ul class="breadcrumbs">
                <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li><span>Goods Receipts</span></li>
            </ul>
            <p class="small text-muted mb-0 mt-2">Receive against an approved <a href="{{ route('admin.purchase-orders.index') }}">Purchase Order</a> — that's where stock is confirmed into the warehouse.</p>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-striped align-middle">
                <thead><tr><th>GRN #</th><th>PO #</th><th>Supplier</th><th>Warehouse</th><th>Date</th><th>Received By</th></tr></thead>
                <tbody>
                    @forelse ($goodsReceipts as $grn)
                    <tr>
                        <td><a href="{{ route('admin.goods-receipts.show', $grn->id) }}">{{ $grn->grn_number }}</a></td>
                        <td><a href="{{ route('admin.purchase-orders.show', $grn->purchase_order_id) }}">{{ $grn->purchaseOrder->po_number ?? '-' }}</a></td>
                        <td>{{ $grn->supplier->company_name ?? '-' }}</td>
                        <td>{{ $grn->warehouse->name ?? '-' }}</td>
                        <td class="small">{{ $grn->receiving_date->format('Y-m-d') }}</td>
                        <td>{{ $grn->receivedBy->name ?? '-' }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="text-center text-muted py-4">No goods receipts yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $goodsReceipts->links() }}
    </div>
</div>

@endsection
