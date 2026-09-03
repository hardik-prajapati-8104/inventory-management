@extends('backend.layouts.master')

@section('title')
Receive Goods - {{ $po->po_number }}
@endsection

@section('admin-content')

<div class="page-title-area">
    <div class="row align-items-center">
        <div class="col-md-8">
            <h4 class="page-title mb-1">Receive Goods — {{ $po->po_number }}</h4>
            <ul class="breadcrumbs">
                <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li><a href="{{ route('admin.purchase-orders.show', $po->id) }}">{{ $po->po_number }}</a></li>
                <li><span>Receive</span></li>
            </ul>
        </div>
        <div class="col-md-4 text-md-end mt-2 mt-md-0">
            <a href="{{ route('admin.purchase-orders.show', $po->id) }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i> Back</a>
        </div>
    </div>
</div>

<div class="alert alert-light border small">
    <i class="bi bi-info-circle me-1"></i> Confirming this receipt increases stock in <strong>{{ $po->warehouse->name }}</strong> immediately for every "Good" quantity entered below. Damaged units are logged but excluded from sellable stock.
</div>

<div class="card">
    <div class="card-body">
        <form action="{{ route('admin.goods-receipts.store', $po->id) }}" method="POST">
            @csrf

            <div class="row g-3 mb-3">
                <div class="col-md-4">
                    <label>Receiving Date<span class="text-error">*</span></label>
                    <input type="date" name="receiving_date" required class="form-control" value="{{ date('Y-m-d') }}">
                </div>
                <div class="col-md-4">
                    <label>Supplier Invoice Number</label>
                    <input type="text" name="supplier_invoice_number" class="form-control">
                </div>
                <div class="col-md-4">
                    <label>Remarks</label>
                    <input type="text" name="remarks" class="form-control">
                </div>
            </div>

            <table class="table table-bordered align-middle">
                <thead>
                    <tr><th>Part</th><th width="10%">Ordered</th><th width="10%">Already Rcvd</th><th width="14%">Receiving Now</th><th width="12%">Damaged</th><th width="12%">Short</th></tr>
                </thead>
                <tbody>
                    @foreach ($po->items as $item)
                    <tr>
                        <td>
                            {{ $item->sparePart->name ?? '-' }} <span class="small text-muted">({{ $item->sparePart->part_number ?? '' }})</span>
                            <input type="hidden" name="purchase_order_item_id[]" value="{{ $item->id }}">
                        </td>
                        <td>{{ $item->quantity }}</td>
                        <td>{{ $item->quantity_received }}</td>
                        <td><input type="number" name="quantity_received[]" min="0" max="{{ $item->pending_quantity }}" class="form-control" value="{{ $item->pending_quantity > 0 ? $item->pending_quantity : 0 }}"></td>
                        <td><input type="number" name="quantity_damaged[]" min="0" value="0" class="form-control"></td>
                        <td><input type="number" name="quantity_short[]" min="0" value="0" class="form-control"></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="row mt-4">
                <div class="text-center col-12">
                    <button type="submit" class="btn btn-add text-white pr-4 pl-4"><i class="bi bi-check-lg"></i> Confirm Receipt &amp; Update Stock</button>
                    <a href="{{ route('admin.purchase-orders.show', $po->id) }}" class="btn btn-outline-secondary pr-4 pl-4">Cancel</a>
                </div>
            </div>
        </form>
    </div>
</div>

@endsection
