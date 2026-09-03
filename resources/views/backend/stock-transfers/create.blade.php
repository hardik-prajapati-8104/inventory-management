@extends('backend.layouts.master')

@section('title')
New Stock Transfer - Vehicle Spare Parts Inventory
@endsection

@section('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/css/select2.min.css" rel="stylesheet" />
@endsection

@section('admin-content')

<div class="page-title-area">
    <div class="row align-items-center">
        <div class="col-md-8">
            <h4 class="page-title mb-1">New Stock Transfer</h4>
            <ul class="breadcrumbs">
                <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li><a href="{{ route('admin.stock-transfers.index') }}">Stock Transfers</a></li>
                <li><span>New</span></li>
            </ul>
        </div>
        <div class="col-md-4 text-md-end mt-2 mt-md-0">
            <a href="{{ route('admin.stock-transfers.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i> Back</a>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <form action="{{ route('admin.stock-transfers.store') }}" method="POST">
            @csrf

            <div class="row g-3 mb-3">
                <div class="col-md-4">
                    <label>From Warehouse<span class="text-error">*</span></label>
                    <select name="from_warehouse_id" required class="form-select">
                        <option value="">Select Warehouse</option>
                        @foreach ($warehouses as $wh)<option value="{{ $wh->id }}">{{ $wh->name }}</option>@endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label>To Warehouse<span class="text-error">*</span></label>
                    <select name="to_warehouse_id" required class="form-select">
                        <option value="">Select Warehouse</option>
                        @foreach ($warehouses as $wh)<option value="{{ $wh->id }}">{{ $wh->name }}</option>@endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label>Transfer Date<span class="text-error">*</span></label>
                    <input type="date" name="transfer_date" required class="form-control" value="{{ date('Y-m-d') }}">
                </div>
                <div class="col-12">
                    <label>Remarks</label>
                    <input type="text" name="remarks" class="form-control">
                </div>
            </div>

            <hr>

            <table class="table table-bordered align-middle" id="itemsTable">
                <thead>
                    <tr><th>Spare Part</th><th width="20%">Quantity</th><th width="5%"></th></tr>
                </thead>
                <tbody id="itemRows">
                    <tr>
                        <td>
                            <select name="spare_part_id[]" class="form-select select2" required>
                                <option value="">Select Part</option>
                                @foreach ($spareParts as $p)
                                    <option value="{{ $p->id }}">{{ $p->name }} ({{ $p->part_number }}) — {{ $p->current_stock }} in stock</option>
                                @endforeach
                            </select>
                        </td>
                        <td><input type="number" name="quantity[]" min="1" required class="form-control"></td>
                        <td><button type="button" class="btn btn-sm btn-outline-danger remove-item"><i class="bi bi-trash"></i></button></td>
                    </tr>
                </tbody>
            </table>

            <button type="button" id="addItemRow" class="btn btn-outline-secondary btn-sm"><i class="bi bi-plus-lg"></i> Add Part</button>

            <div class="row mt-4">
                <div class="text-center col-12">
                    <button type="submit" class="btn btn-add text-white pr-4 pl-4"><i class="bi bi-save"></i> Submit Transfer Request</button>
                    <a href="{{ route('admin.stock-transfers.index') }}" class="btn btn-outline-secondary pr-4 pl-4">Cancel</a>
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
