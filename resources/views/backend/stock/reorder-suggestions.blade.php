@extends('backend.layouts.master')

@section('title')
Reorder Suggestions - Vehicle Spare Parts Inventory
@endsection

@section('admin-content')

<div class="page-title-area">
    <div class="row align-items-center">
        <div class="col-12">
            <h4 class="page-title mb-1">Automatic Reorder Suggestions</h4>
            <ul class="breadcrumbs">
                <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li><a href="{{ route('admin.stock.index') }}">Inventory</a></li>
                <li><span>Reorder Suggestions</span></li>
            </ul>
            <p class="small text-muted mb-0 mt-2">
                Every part at or below its minimum stock level, with a suggested
                reorder quantity (Maximum Stock − Current Stock, or double the
                minimum when no maximum is set). Check the ones you want, then
                convert them straight into a Purchase Order.
            </p>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body">
        @can('purchase-order.create')
        <form action="{{ route('admin.stock.reorder-suggestions.convert') }}" method="POST" id="reorderForm">
            @csrf
        @endcan

        <div class="table-responsive">
            <table class="table table-bordered table-striped align-middle">
                <thead>
                    <tr>
                        @can('purchase-order.create')<th width="3%"><input type="checkbox" id="selectAll"></th>@endcan
                        <th>Part</th><th>Category</th><th>Current Stock</th><th>Minimum</th><th>Maximum</th><th width="14%">Suggested Qty</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($parts as $p)
                    <tr>
                        @can('purchase-order.create')
                        <td><input type="checkbox" class="row-check" data-target="qty-{{ $p->id }}"></td>
                        @endcan
                        <td>{{ $p->name }} <span class="small text-muted">({{ $p->part_number }})</span></td>
                        <td>{{ $p->category->name ?? '-' }}</td>
                        <td>
                            @if ($p->current_stock <= 0) <span class="badge bg-danger">{{ $p->current_stock }}</span>
                            @else <span class="badge" style="background:var(--vsp-warning)">{{ $p->current_stock }}</span> @endif
                        </td>
                        <td>{{ $p->minimum_stock }}</td>
                        <td>{{ $p->maximum_stock ?? '-' }}</td>
                        <td>
                            <input type="hidden" class="part-id-input" value="{{ $p->id }}" disabled>
                            <input type="number" id="qty-{{ $p->id }}" name="__suggested_quantity" min="1" class="form-control form-control-sm" value="{{ $p->suggested_quantity }}" disabled>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="text-center text-muted py-4">Nothing needs reordering right now.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @can('purchase-order.create')
        @if ($parts->count())
        <button type="submit" class="btn btn-add text-white"><i class="bi bi-file-earmark-plus"></i> Convert Selected to Purchase Order</button>
        @endif
        </form>
        @endcan
    </div>
</div>

@endsection

@section('scripts')
<script>
// Checking a row enables (and correctly *names*) its spare_part_id + quantity
// inputs so only checked rows are ever submitted, with matching array indices.
document.querySelectorAll('.row-check').forEach(function (cb) {
    cb.addEventListener('change', function () {
        const row = this.closest('tr');
        const idInput = row.querySelector('.part-id-input');
        const qtyInput = row.querySelector('input[type=number]');

        idInput.disabled = ! this.checked;
        qtyInput.disabled = ! this.checked;
        idInput.name = this.checked ? 'spare_part_id[]' : '';
        qtyInput.name = this.checked ? 'suggested_quantity[]' : '__suggested_quantity';
    });
});

document.getElementById('selectAll')?.addEventListener('change', function () {
    const checked = this.checked;
    document.querySelectorAll('.row-check').forEach(cb => {
        cb.checked = checked;
        cb.dispatchEvent(new Event('change'));
    });
});
</script>
@endsection
