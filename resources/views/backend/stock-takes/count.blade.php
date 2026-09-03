@extends('backend.layouts.master')

@section('title')
Stock Take Count - Vehicle Spare Parts Inventory
@endsection

@section('admin-content')

<div class="page-title-area">
    <div class="row align-items-center">
        <div class="col-md-8">
            <h4 class="page-title mb-1">Stock Take — {{ $stockTake->stock_take_number }}</h4>
            <ul class="breadcrumbs">
                <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li><a href="{{ route('admin.stock-takes.index') }}">Stock Takes</a></li>
                <li><span>{{ $stockTake->warehouse->name }}</span></li>
            </ul>
        </div>
        <div class="col-md-4 text-md-end mt-2 mt-md-0">
            <a href="{{ route('admin.stock-takes.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i> Back</a>
        </div>
    </div>
</div>

@if ($stockTake->status === 'counting')
<div class="card">
    <div class="card-body">
        <p class="text-muted small">Enter the physically counted quantity for each part. Leave blank to skip a part (it will be excluded from the variance).</p>
        <form action="{{ route('admin.stock-takes.save-counts', $stockTake->id) }}" method="POST">
            @csrf
            <table class="table table-bordered align-middle">
                <thead><tr><th>Part</th><th width="15%">System Qty</th><th width="20%">Counted Qty</th></tr></thead>
                <tbody>
                    @foreach ($stockTake->items as $item)
                    <tr>
                        <td>{{ $item->sparePart->name ?? '-' }} <span class="small text-muted">({{ $item->sparePart->part_number ?? '' }})</span></td>
                        <td>{{ $item->system_quantity }}</td>
                        <td><input type="number" min="0" name="counted_quantity[{{ $item->id }}]" class="form-control" value="{{ $item->counted_quantity }}"></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <button type="submit" class="btn btn-add text-white"><i class="bi bi-save"></i> Save Counts</button>
        </form>
    </div>
</div>
@else
<div class="card">
    <div class="card-body">
        <p class="text-muted small">Review the variance below, then approve to automatically create and apply a Stock Adjustment for every part that differs.</p>
        <table class="table table-bordered align-middle">
            <thead><tr><th>Part</th><th width="15%">System</th><th width="15%">Counted</th><th width="15%">Difference</th></tr></thead>
            <tbody>
                @foreach ($stockTake->items as $item)
                <tr class="{{ $item->difference != 0 ? 'table-warning' : '' }}">
                    <td>{{ $item->sparePart->name ?? '-' }}</td>
                    <td>{{ $item->system_quantity }}</td>
                    <td>{{ $item->counted_quantity ?? '—' }}</td>
                    <td>
                        @if ($item->difference === null) <span class="text-muted">—</span>
                        @elseif ($item->difference == 0) <span class="text-success">0</span>
                        @else <span class="{{ $item->difference > 0 ? 'text-success' : 'text-danger' }}">{{ $item->difference > 0 ? '+' : '' }}{{ $item->difference }}</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        @if ($stockTake->status === 'pending_approval')
        @can('stock-adjustment.approve')
        <form action="{{ route('admin.stock-takes.approve', $stockTake->id) }}" method="POST" onsubmit="return confirm('Approve variance and apply stock adjustment now?');">
            @csrf
            <button type="submit" class="btn btn-add text-white"><i class="bi bi-check-lg"></i> Approve &amp; Apply Adjustment</button>
        </form>
        @endcan
        @else
        <span class="badge bg-success">Completed — adjustment applied</span>
        @endif
    </div>
</div>
@endif

@endsection
