@extends('backend.layouts.master')

@section('title')
New Purchase Invoice - Vehicle Spare Parts Inventory
@endsection

@section('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/css/select2.min.css" rel="stylesheet" />
@endsection

@section('admin-content')

<div class="page-title-area">
    <div class="row align-items-center">
        <div class="col-md-8">
            <h4 class="page-title mb-1">New Purchase Invoice</h4>
            <ul class="breadcrumbs">
                <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li><a href="{{ route('admin.purchases.index') }}">Purchases</a></li>
                <li><span>New</span></li>
            </ul>
        </div>
        <div class="col-md-4 text-md-end mt-2 mt-md-0">
            <a href="{{ route('admin.purchases.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i> Back</a>
        </div>
    </div>
</div>

@if ($goodsReceipt)
<div class="alert alert-light border small">
    <i class="bi bi-check-circle text-success me-1"></i> Linked to <strong>{{ $goodsReceipt->grn_number }}</strong> — stock was already received when that GRN was confirmed, so saving this invoice will <strong>not</strong> move stock again. This form just records the financials.
</div>
@else
<div class="alert alert-light border small">
    <i class="bi bi-info-circle me-1"></i> Quick entry — no linked Goods Receipt, so saving this invoice will increase stock in the selected warehouse immediately. Use this for informal / no-PO purchases; use the full <a href="{{ route('admin.purchase-orders.create') }}">Purchase Order → Goods Receipt</a> flow when you need approval and receiving steps.
</div>
@endif

<div class="card">
    <div class="card-body">
        <form action="{{ route('admin.purchases.store') }}" method="POST">
            @csrf
            @if ($goodsReceipt)
                <input type="hidden" name="goods_receipt_id" value="{{ $goodsReceipt->id }}">
            @endif

            <div class="row g-3 mb-3">
                <div class="col-md-3">
                    <label>Supplier<span class="text-error">*</span></label>
                    <select name="supplier_id" required class="form-select select2" {{ $goodsReceipt ? 'disabled' : '' }}>
                        <option value="">Select Supplier</option>
                        @foreach ($suppliers as $s)
                            <option value="{{ $s->id }}" {{ ($goodsReceipt->supplier_id ?? null) == $s->id ? 'selected' : '' }}>{{ $s->company_name }}</option>
                        @endforeach
                    </select>
                    @if ($goodsReceipt)<input type="hidden" name="supplier_id" value="{{ $goodsReceipt->supplier_id }}">@endif
                </div>
                <div class="col-md-3">
                    <label>Warehouse<span class="text-error">*</span></label>
                    <select name="warehouse_id" required class="form-select" {{ $goodsReceipt ? 'disabled' : '' }}>
                        <option value="">Select Warehouse</option>
                        @foreach ($warehouses as $wh)
                            <option value="{{ $wh->id }}" {{ ($goodsReceipt->warehouse_id ?? null) == $wh->id ? 'selected' : '' }}>{{ $wh->name }}</option>
                        @endforeach
                    </select>
                    @if ($goodsReceipt)<input type="hidden" name="warehouse_id" value="{{ $goodsReceipt->warehouse_id }}">@endif
                </div>
                <div class="col-md-3">
                    <label>Invoice Date<span class="text-error">*</span></label>
                    <input type="date" name="invoice_date" required class="form-control" value="{{ date('Y-m-d') }}">
                </div>
                <div class="col-md-3">
                    <label>Due Date</label>
                    <input type="date" name="due_date" class="form-control">
                </div>
                <div class="col-12">
                    <label>Notes</label>
                    <input type="text" name="notes" class="form-control">
                </div>
            </div>

            <hr>

            <table class="table table-bordered align-middle" id="itemsTable">
                <thead>
                    <tr><th>Spare Part</th><th width="10%">Qty</th><th width="14%">Price</th><th width="12%">Discount</th><th width="12%">Tax</th><th width="14%">Total</th><th width="5%"></th></tr>
                </thead>
                <tbody id="itemRows">
                    @if ($goodsReceipt)
                        @foreach ($goodsReceipt->items as $item)
                        <tr>
                            <td>
                                <select name="spare_part_id_display[]" class="form-select" disabled>
                                    <option selected>{{ $item->sparePart->name ?? '-' }} ({{ $item->sparePart->part_number ?? '' }})</option>
                                </select>
                                <input type="hidden" name="spare_part_id[]" value="{{ $item->spare_part_id }}">
                            </td>
                            <td><input type="number" class="form-control qty" value="{{ $item->quantity_received - $item->quantity_damaged }}" readonly>
                                <input type="hidden" name="quantity[]" value="{{ $item->quantity_received - $item->quantity_damaged }}"></td>
                            <td><input type="number" step="0.01" name="purchase_price[]" min="0" required class="form-control price" value="{{ $item->sparePart->purchase_price ?? 0 }}"></td>
                            <td><input type="number" step="0.01" name="discount[]" min="0" value="0" class="form-control discount"></td>
                            <td><input type="number" step="0.01" name="tax[]" min="0" value="0" class="form-control tax"></td>
                            <td><input type="text" class="form-control line-total" readonly value="0.00"></td>
                            <td></td>
                        </tr>
                        @endforeach
                    @else
                        <tr>
                            <td>
                                <select name="spare_part_id[]" class="form-select select2 part-select" required>
                                    <option value="">Select Part</option>
                                    @foreach ($spareParts as $p)
                                        <option value="{{ $p->id }}" data-price="{{ $p->purchase_price }}">{{ $p->name }} ({{ $p->part_number }})</option>
                                    @endforeach
                                </select>
                            </td>
                            <td><input type="number" name="quantity[]" min="1" required class="form-control qty"></td>
                            <td><input type="number" step="0.01" name="purchase_price[]" min="0" required class="form-control price"></td>
                            <td><input type="number" step="0.01" name="discount[]" min="0" value="0" class="form-control discount"></td>
                            <td><input type="number" step="0.01" name="tax[]" min="0" value="0" class="form-control tax"></td>
                            <td><input type="text" class="form-control line-total" readonly value="0.00"></td>
                            <td><button type="button" class="btn btn-sm btn-outline-danger remove-item"><i class="bi bi-trash"></i></button></td>
                        </tr>
                    @endif
                </tbody>
            </table>

            @unless ($goodsReceipt)
            <button type="button" id="addItemRow" class="btn btn-outline-secondary btn-sm"><i class="bi bi-plus-lg"></i> Add Part</button>
            @endunless

            <div class="text-end mt-3">
                <h6>Grand Total: <span id="orderTotal">₹0.00</span></h6>
            </div>

            <div class="row mt-4">
                <div class="text-center col-12">
                    <button type="submit" class="btn btn-add text-white pr-4 pl-4"><i class="bi bi-save"></i> Save Invoice</button>
                    <a href="{{ route('admin.purchases.index') }}" class="btn btn-outline-secondary pr-4 pl-4">Cancel</a>
                </div>
            </div>
        </form>
    </div>
</div>

@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js"></script>
<script>
$(document).ready(function () {
    $('.select2').select2({ width: '100%' });

    function recalcRow($row) {
        const qty = parseFloat($row.find('.qty').val()) || 0;
        const price = parseFloat($row.find('.price').val()) || 0;
        const discount = parseFloat($row.find('.discount').val()) || 0;
        const tax = parseFloat($row.find('.tax').val()) || 0;
        const total = (qty * price) - discount + tax;
        $row.find('.line-total').val(total.toFixed(2));
        recalcOrderTotal();
    }

    function recalcOrderTotal() {
        let sum = 0;
        $('.line-total').each(function () { sum += parseFloat($(this).val()) || 0; });
        $('#orderTotal').text('₹' + sum.toFixed(2));
    }

    $('#itemRows tr').each(function () { recalcRow($(this)); });

    $(document).on('input', '.qty, .price, .discount, .tax', function () { recalcRow($(this).closest('tr')); });

    $(document).on('change', '.part-select', function () {
        const price = $(this).find(':selected').data('price') || 0;
        $(this).closest('tr').find('.price').val(price);
        recalcRow($(this).closest('tr'));
    });

    $('#addItemRow').on('click', function () {
        const $row = $('#itemRows tr:first').clone();
        $row.find('select, input').val('');
        $row.find('.discount, .tax').val(0);
        $row.find('.line-total').val('0.00');
        $('#itemRows').append($row);
        $row.find('.select2').select2({ width: '100%' });
    });

    $(document).on('click', '.remove-item', function () {
        if ($('#itemRows tr').length > 1) { $(this).closest('tr').remove(); recalcOrderTotal(); }
    });
});
</script>
@endsection
