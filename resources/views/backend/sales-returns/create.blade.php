@extends('backend.layouts.master')

@section('title')
New Sales Return - Vehicle Spare Parts Inventory
@endsection

@section('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/css/select2.min.css" rel="stylesheet" />
@endsection

@section('admin-content')

<div class="page-title-area">
    <div class="row align-items-center">
        <div class="col-md-8">
            <h4 class="page-title mb-1">New Sales Return</h4>
            <ul class="breadcrumbs">
                <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li><a href="{{ route('admin.sales-returns.index') }}">Sales Returns</a></li>
                <li><span>New</span></li>
            </ul>
        </div>
        <div class="col-md-4 text-md-end mt-2 mt-md-0">
            <a href="{{ route('admin.sales-returns.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i> Back</a>
        </div>
    </div>
</div>

<div class="alert alert-light border small">
    <i class="bi bi-info-circle me-1"></i> <strong>Resalable</strong> items go straight back into available stock. <strong>Damaged</strong> or <strong>Defective</strong> items are received into the warehouse but tracked separately in damaged stock — they won't be sellable until reviewed.
</div>

<div class="card">
    <div class="card-body">
        <form action="{{ route('admin.sales-returns.store') }}" method="POST">
            @csrf

            <div class="row g-3 mb-3">
                <div class="col-md-3">
                    <label>Customer<span class="text-error">*</span></label>
                    <select name="customer_id" required class="form-select select2">
                        <option value="">Select Customer</option>
                        @foreach ($customers as $c)<option value="{{ $c->id }}">{{ $c->customer_name }}</option>@endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label>Original Sales Invoice</label>
                    <select name="sale_id" class="form-select select2">
                        <option value="">None / Not linked</option>
                        @foreach ($sales as $s)<option value="{{ $s->id }}">{{ $s->invoice_number }}</option>@endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label>Warehouse<span class="text-error">*</span></label>
                    <select name="warehouse_id" required class="form-select">
                        <option value="">Select Warehouse</option>
                        @foreach ($warehouses as $wh)<option value="{{ $wh->id }}">{{ $wh->name }}</option>@endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label>Return Date<span class="text-error">*</span></label>
                    <input type="date" name="return_date" required class="form-control" value="{{ date('Y-m-d') }}">
                </div>
                <div class="col-12">
                    <label>Remarks</label>
                    <input type="text" name="remarks" class="form-control">
                </div>
            </div>

            <hr>

            <table class="table table-bordered align-middle" id="itemsTable">
                <thead><tr><th>Spare Part</th><th width="10%">Qty</th><th width="16%">Condition</th><th width="20%">Reason</th><th width="14%">Refund</th><th width="5%"></th></tr></thead>
                <tbody id="itemRows">
                    <tr>
                        <td>
                            <select name="spare_part_id[]" class="form-select select2" required>
                                <option value="">Select Part</option>
                                @foreach ($spareParts as $p)
                                    <option value="{{ $p->id }}">{{ $p->name }} ({{ $p->part_number }})</option>
                                @endforeach
                            </select>
                        </td>
                        <td><input type="number" name="quantity[]" min="1" required class="form-control"></td>
                        <td>
                            <select name="condition[]" required class="form-select">
                                <option value="resalable">Resalable</option>
                                <option value="damaged">Damaged</option>
                                <option value="defective">Defective</option>
                            </select>
                        </td>
                        <td><input type="text" name="return_reason[]" class="form-control"></td>
                        <td><input type="number" step="0.01" name="refund_amount[]" min="0" value="0" class="form-control"></td>
                        <td><button type="button" class="btn btn-sm btn-outline-danger remove-item"><i class="bi bi-trash"></i></button></td>
                    </tr>
                </tbody>
            </table>

            <button type="button" id="addItemRow" class="btn btn-outline-secondary btn-sm"><i class="bi bi-plus-lg"></i> Add Part</button>

            <div class="row mt-4">
                <div class="text-center col-12">
                    <button type="submit" class="btn btn-add text-white pr-4 pl-4"><i class="bi bi-save"></i> Submit for Approval</button>
                    <a href="{{ route('admin.sales-returns.index') }}" class="btn btn-outline-secondary pr-4 pl-4">Cancel</a>
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

    $('#addItemRow').on('click', function () {
        const $row = $('#itemRows tr:first').clone();
        $row.find('select, input').val('');
        $('#itemRows').append($row);
        $row.find('.select2').select2({ width: '100%' });
    });

    $(document).on('click', '.remove-item', function () {
        if ($('#itemRows tr').length > 1) $(this).closest('tr').remove();
    });
});
</script>
@endsection
