@extends('backend.layouts.master')

@section('title')
New Stock Adjustment - Vehicle Spare Parts Inventory
@endsection

@section('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/css/select2.min.css" rel="stylesheet" />
@endsection

@section('admin-content')

<div class="page-title-area">
    <div class="row align-items-center">
        <div class="col-md-8">
            <h4 class="page-title mb-1">New Stock Adjustment</h4>
            <ul class="breadcrumbs">
                <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li><a href="{{ route('admin.stock-adjustments.index') }}">Stock Adjustments</a></li>
                <li><span>New</span></li>
            </ul>
        </div>
        <div class="col-md-4 text-md-end mt-2 mt-md-0">
            <a href="{{ route('admin.stock-adjustments.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i> Back</a>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <form action="{{ route('admin.stock-adjustments.store') }}" method="POST">
            @csrf

            <div class="row g-3 mb-3">
                <div class="col-md-4">
                    <label>Warehouse<span class="text-error">*</span></label>
                    <select name="warehouse_id" required class="form-select">
                        <option value="">Select Warehouse</option>
                        @foreach ($warehouses as $wh)<option value="{{ $wh->id }}">{{ $wh->name }}</option>@endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label>Reason<span class="text-error">*</span></label>
                    <select name="reason" required class="form-select">
                        @foreach (['Physical stock difference','Damaged product','Lost product','Found stock','Data correction','Opening stock correction'] as $reason)
                            <option value="{{ $reason }}">{{ $reason }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label>Remarks</label>
                    <input type="text" name="remarks" class="form-control">
                </div>
            </div>

            <hr>

            <table class="table table-bordered align-middle" id="itemsTable">
                <thead>
                    <tr>
                        <th>Spare Part</th>
                        <th width="15%">Current Qty</th>
                        <th width="15%">Type</th>
                        <th width="15%">Adjustment Qty</th>
                        <th width="5%"></th>
                    </tr>
                </thead>
                <tbody id="itemRows">
                    <tr>
                        <td>
                            <select name="spare_part_id[]" class="form-select select2 part-select" required>
                                <option value="">Select Part</option>
                                @foreach ($spareParts as $p)
                                    <option value="{{ $p->id }}" data-stock="{{ $p->current_stock }}">{{ $p->name }} ({{ $p->part_number }})</option>
                                @endforeach
                            </select>
                        </td>
                        <td><input type="text" class="form-control current-qty" readonly value="0"></td>
                        <td>
                            <select name="adjustment_type[]" class="form-select" required>
                                <option value="increase">Increase</option>
                                <option value="decrease">Decrease</option>
                            </select>
                        </td>
                        <td><input type="number" name="adjustment_quantity[]" min="1" required class="form-control"></td>
                        <td><button type="button" class="btn btn-sm btn-outline-danger remove-item"><i class="bi bi-trash"></i></button></td>
                    </tr>
                </tbody>
            </table>

            <button type="button" id="addItemRow" class="btn btn-outline-secondary btn-sm"><i class="bi bi-plus-lg"></i> Add Part</button>

            <div class="row mt-4">
                <div class="text-center col-12">
                    <button type="submit" class="btn btn-add text-white pr-4 pl-4"><i class="bi bi-save"></i> Submit for Approval</button>
                    <a href="{{ route('admin.stock-adjustments.index') }}" class="btn btn-outline-secondary pr-4 pl-4">Cancel</a>
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

    $(document).on('change', '.part-select', function () {
        const stock = $(this).find(':selected').data('stock') || 0;
        $(this).closest('tr').find('.current-qty').val(stock);
    });

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
