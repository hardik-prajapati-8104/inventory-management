@extends('backend.layouts.master')

@section('title')
{{ $purchase->invoice_number }} - Vehicle Spare Parts Inventory
@endsection

@section('admin-content')

<div class="page-title-area">
    <div class="row align-items-center">
        <div class="col-12">
            <h4 class="page-title mb-1">Purchase Invoice — {{ $purchase->invoice_number }}</h4>
            <ul class="breadcrumbs">
                <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li><a href="{{ route('admin.purchases.index') }}">Purchases</a></li>
                <li><span>{{ $purchase->invoice_number }}</span></li>
            </ul>
        </div>
    </div>
</div>

<div class="row g-3 mb-3">
    <div class="col-md-3"><div class="card"><div class="card-body"><div class="small text-muted">Supplier</div><div class="fw-medium">{{ $purchase->supplier->company_name }}</div></div></div></div>
    <div class="col-md-3"><div class="card"><div class="card-body"><div class="small text-muted">Grand Total</div><div class="fw-medium">₹{{ number_format($purchase->grand_total, 2) }}</div></div></div></div>
    <div class="col-md-3"><div class="card"><div class="card-body"><div class="small text-muted">Paid</div><div class="fw-medium">₹{{ number_format($purchase->paid_amount, 2) }}</div></div></div></div>
    <div class="col-md-3"><div class="card"><div class="card-body"><div class="small text-muted">Due</div><div class="fw-medium">₹{{ number_format($purchase->due_amount, 2) }}</div></div></div></div>
</div>

@if ($purchase->goodsReceipt)
<div class="alert alert-light border small"><i class="bi bi-link-45deg me-1"></i> Linked to Goods Receipt <a href="{{ route('admin.goods-receipts.show', $purchase->goods_receipt_id) }}">{{ $purchase->goodsReceipt->grn_number }}</a> — stock was received there.</div>
@elseif ($purchase->stock_received_directly)
<div class="alert alert-light border small"><i class="bi bi-box-arrow-in-down me-1"></i> This invoice received stock directly (quick entry, no PO/GRN).</div>
@endif

<div class="card mb-3">
    <div class="card-body">
        <h6 class="mb-3">Items</h6>
        <table class="table table-bordered">
            <thead><tr><th>Part</th><th>Qty</th><th>Price</th><th>Discount</th><th>Tax</th><th>Total</th></tr></thead>
            <tbody>
                @foreach ($purchase->items as $item)
                <tr>
                    <td>{{ $item->sparePart->name ?? '-' }} <span class="small text-muted">({{ $item->sparePart->part_number ?? '' }})</span></td>
                    <td>{{ $item->quantity }}</td>
                    <td>₹{{ number_format($item->purchase_price, 2) }}</td>
                    <td>₹{{ number_format($item->discount, 2) }}</td>
                    <td>₹{{ number_format($item->tax, 2) }}</td>
                    <td>₹{{ number_format($item->total, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<div class="row g-3">
    <div class="col-md-6">
        <div class="card">
            <div class="card-body">
                <h6 class="mb-3">Payment History</h6>
                <table class="table table-bordered">
                    <thead><tr><th>Date</th><th>Amount</th><th>Method</th></tr></thead>
                    <tbody>
                        @forelse ($purchase->payments as $pay)
                        <tr><td class="small">{{ $pay->payment_date->format('Y-m-d') }}</td><td>₹{{ number_format($pay->amount, 2) }}</td><td>{{ ucfirst(str_replace('_',' ',$pay->payment_method)) }}</td></tr>
                        @empty
                        <tr><td colspan="3" class="text-center text-muted py-3">No payments recorded yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        @can('purchase.edit')
        @if ($purchase->due_amount > 0)
        <div class="card">
            <div class="card-body">
                <h6 class="mb-3">Record Payment</h6>
                <form action="{{ route('admin.purchases.payments.store', $purchase->id) }}" method="POST" class="row g-2">
                    @csrf
                    <div class="col-6">
                        <label class="small">Amount (max ₹{{ number_format($purchase->due_amount, 2) }})</label>
                        <input type="number" step="0.01" name="amount" max="{{ $purchase->due_amount }}" required class="form-control">
                    </div>
                    <div class="col-6">
                        <label class="small">Date</label>
                        <input type="date" name="payment_date" required class="form-control" value="{{ date('Y-m-d') }}">
                    </div>
                    <div class="col-6">
                        <label class="small">Method</label>
                        <select name="payment_method" required class="form-select">
                            <option value="cash">Cash</option>
                            <option value="bank_transfer">Bank Transfer</option>
                            <option value="card">Card</option>
                            <option value="cheque">Cheque</option>
                            <option value="online">Online</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    <div class="col-6">
                        <label class="small">Reference #</label>
                        <input type="text" name="reference_number" class="form-control">
                    </div>
                    <div class="col-12 mt-2">
                        <button type="submit" class="btn btn-add text-white w-100"><i class="bi bi-cash"></i> Record Payment</button>
                    </div>
                </form>
            </div>
        </div>
        @else
        <div class="card"><div class="card-body text-center text-success"><i class="bi bi-check-circle fs-3"></i><div>Fully paid</div></div></div>
        @endif
        @endcan
    </div>
</div>

@endsection
