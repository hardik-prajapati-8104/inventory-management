@extends('backend.layouts.master')

@section('title')
New Sale - Vehicle Spare Parts Inventory
@endsection

@section('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/css/select2.min.css" rel="stylesheet" />
@endsection

@section('admin-content')

<div class="page-title-area">
    <div class="row align-items-center">
        <div class="col-md-8">
            <h4 class="page-title mb-1">New Sale</h4>
            <ul class="breadcrumbs">
                <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li><a href="{{ route('admin.sales.index') }}">Sales</a></li>
                <li><span>New</span></li>
            </ul>
        </div>
        <div class="col-md-4 text-md-end mt-2 mt-md-0">
            <a href="{{ route('admin.sales.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i> Back</a>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <form action="{{ route('admin.sales.store') }}" method="POST" id="saleForm">
            @csrf

            <div class="row g-3 mb-3">
                <div class="col-md-4">
                    <label>Customer<span class="text-error">*</span></label>
                    <select name="customer_id" required class="form-select select2">
                        <option value="">Select Customer</option>
                        @foreach ($customers as $c)<option value="{{ $c->id }}">{{ $c->customer_name }}</option>@endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label>Warehouse<span class="text-error">*</span></label>
                    <select name="warehouse_id" required class="form-select">
                        <option value="">Select Warehouse</option>
                        @foreach ($warehouses as $wh)<option value="{{ $wh->id }}">{{ $wh->name }}</option>@endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label>Invoice Date<span class="text-error">*</span></label>
                    <input type="date" name="invoice_date" required class="form-control" value="{{ date('Y-m-d') }}">
                </div>
                <div class="col-12">
                    <label>Notes</label>
                    <input type="text" name="notes" class="form-control">
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label>Scan or Enter Barcode / SKU to Add</label>
                    <div class="input-group">
                        <input type="text" id="barcodeInput" class="form-control" placeholder="Scan barcode, or type SKU/part number and press Enter" autofocus>
                        <button type="button" id="barcodeAddBtn" class="btn btn-outline-secondary"><i class="bi bi-upc-scan"></i> Add</button>
                    </div>
                    <div id="barcodeError" class="text-error small mt-1 d-none"></div>
                </div>
            </div>

            <hr>

            <table class="table table-bordered align-middle" id="itemsTable">
                <thead>
                    <tr><th>Spare Part</th><th width="9%">In Stock</th><th width="9%">Qty</th><th width="12%">Price</th><th width="10%">Discount</th><th width="10%">Tax</th><th width="12%">Total</th><th width="5%"></th></tr>
                </thead>
                <tbody id="itemRows">
                    <tr>
                        <td>
                            <select name="spare_part_id[]" class="form-select select2 part-select" required>
                                <option value="">Select Part</option>
                                @foreach ($spareParts as $p)
                                    <option value="{{ $p->id }}" data-price="{{ $p->retail_price }}" data-tax="{{ $p->tax_percentage }}" data-stock="{{ $p->current_stock }}">{{ $p->name }} ({{ $p->part_number }})</option>
                                @endforeach
                            </select>
                        </td>
                        <td class="stock-display text-center">-</td>
                        <td><input type="number" name="quantity[]" min="1" required class="form-control qty"></td>
                        <td><input type="number" step="0.01" name="selling_price[]" min="0" required class="form-control price"></td>
                        <td><input type="number" step="0.01" name="discount[]" min="0" value="0" class="form-control discount"></td>
                        <td><input type="number" step="0.01" name="tax[]" min="0" value="0" class="form-control tax"></td>
                        <td><input type="text" class="form-control line-total" readonly value="0.00"></td>
                        <td><button type="button" class="btn btn-sm btn-outline-danger remove-item"><i class="bi bi-trash"></i></button></td>
                    </tr>
                </tbody>
            </table>

            <button type="button" id="addItemRow" class="btn btn-outline-secondary btn-sm"><i class="bi bi-plus-lg"></i> Add Part</button>

            <div class="text-end mt-3">
                <h6>Grand Total: <span id="orderTotal">₹0.00</span></h6>
            </div>

            <div class="row mt-4">
                <div class="text-center col-12">
                    <button type="submit" class="btn btn-add text-white pr-4 pl-4"><i class="bi bi-save"></i> Complete Sale</button>
                    <a href="{{ route('admin.sales.index') }}" class="btn btn-outline-secondary pr-4 pl-4">Cancel</a>
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

    const partsData = @json($spareParts->map(fn($p) => ['id' => $p->id, 'name' => $p->name, 'part_number' => $p->part_number, 'sku' => $p->sku, 'barcode' => $p->barcode, 'price' => $p->retail_price, 'tax' => $p->tax_percentage, 'stock' => $p->current_stock]));

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

    function fillRowFromPart($row, part) {
        $row.find('.part-select').val(part.id).trigger('change.select2');
        $row.find('.price').val(part.price);
        $row.find('.tax').val((part.tax / 100 * part.price).toFixed(2));
        $row.find('.stock-display').text(part.stock);
        if (! $row.find('.qty').val()) $row.find('.qty').val(1);
        recalcRow($row);
    }

    $(document).on('change', '.part-select', function () {
        const $opt = $(this).find(':selected');
        const price = $opt.data('price') || 0;
        const stock = $opt.data('stock') ?? '-';
        const $row = $(this).closest('tr');
        $row.find('.price').val(price);
        $row.find('.stock-display').text(stock);
        recalcRow($row);
    });

    $(document).on('input', '.qty, .price, .discount, .tax', function () { recalcRow($(this).closest('tr')); });

    $('#addItemRow').on('click', function () {
        const $row = $('#itemRows tr:first').clone();
        $row.find('select').val('').trigger('change.select2');
        $row.find('input').val('');
        $row.find('.discount, .tax').val(0);
        $row.find('.line-total').val('0.00');
        $row.find('.stock-display').text('-');
        $('#itemRows').append($row);
        $row.find('.select2').select2({ width: '100%' });
        return $row;
    });

    $(document).on('click', '.remove-item', function () {
        if ($('#itemRows tr').length > 1) { $(this).closest('tr').remove(); recalcOrderTotal(); }
    });

    // ---- Barcode / SKU / Part Number quick-add (Section 9 barcode-to-sell) ----
    function addByCode(code) {
        code = code.trim();
        if (! code) return;

        const part = partsData.find(p => p.barcode === code || p.sku === code || p.part_number === code);
        $('#barcodeError').addClass('d-none');

        if (! part) {
            $('#barcodeError').removeClass('d-none').text(`No spare part found matching "${code}".`);
            return;
        }

        // If this part already has a row, just bump its quantity.
        let $existingRow = null;
        $('.part-select').each(function () {
            if ($(this).val() == part.id) $existingRow = $(this).closest('tr');
        });

        if ($existingRow) {
            const currentQty = parseInt($existingRow.find('.qty').val()) || 0;
            $existingRow.find('.qty').val(currentQty + 1);
            recalcRow($existingRow);
        } else {
            // Use the first empty row if present, otherwise add a new one.
            let $row = $('#itemRows tr').filter(function () { return ! $(this).find('.part-select').val(); }).first();
            if (! $row.length) $row = $('#addItemRow').trigger('click');
            fillRowFromPart($row, part);
        }

        $('#barcodeInput').val('').focus();
    }

    $('#barcodeAddBtn').on('click', function () { addByCode($('#barcodeInput').val()); });
    $('#barcodeInput').on('keypress', function (e) {
        if (e.which === 13) { e.preventDefault(); addByCode($(this).val()); }
    });
});
</script>
@endsection
