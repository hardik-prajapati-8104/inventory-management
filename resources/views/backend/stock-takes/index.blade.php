@extends('backend.layouts.master')

@section('title')
Stock Takes - Vehicle Spare Parts Inventory
@endsection

@section('admin-content')

<div class="page-title-area">
    <div class="row align-items-center">
        <div class="col-md-8">
            <h4 class="page-title mb-1">Stock Takes</h4>
            <ul class="breadcrumbs">
                <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li><span>Stock Takes</span></li>
            </ul>
        </div>
        <div class="col-md-4 text-md-end mt-2 mt-md-0">
            @can('stock-adjustment.create')
            <a href="{{ route('admin.stock-takes.create') }}" class="btn btn-add text-white"><i class="bi bi-plus-lg"></i> New Stock Take</a>
            @endcan
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-striped align-middle">
                <thead>
                    <tr><th>Stock Take #</th><th>Warehouse</th><th>Items</th><th>Status</th><th>Created By</th><th>Date</th><th width="14%">Action</th></tr>
                </thead>
                <tbody>
                    @forelse ($stockTakes as $st)
                    <tr>
                        <td>{{ $st->stock_take_number }}</td>
                        <td>{{ $st->warehouse->name ?? '-' }}</td>
                        <td>{{ $st->items->count() }}</td>
                        <td>
                            @if ($st->status === 'counting') <span class="badge" style="background:var(--vsp-info)">Counting</span>
                            @elseif ($st->status === 'pending_approval') <span class="badge" style="background:var(--vsp-warning)">Pending Approval</span>
                            @elseif ($st->status === 'completed') <span class="badge bg-success">Completed</span>
                            @else <span class="badge bg-secondary">Draft</span> @endif
                        </td>
                        <td>{{ $st->createdBy->name ?? '-' }}</td>
                        <td class="small">{{ $st->created_at->format('Y-m-d H:i') }}</td>
                        <td>
                            @if (in_array($st->status, ['counting', 'pending_approval']))
                                <a href="{{ route('admin.stock-takes.count', $st->id) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-clipboard-check"></i> {{ $st->status === 'counting' ? 'Count' : 'Review' }}</a>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="text-center text-muted py-4">No stock takes yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $stockTakes->links() }}
    </div>
</div>

@endsection
