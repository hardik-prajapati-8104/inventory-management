@extends('backend.layouts.master')

@section('title')
Stock Adjustments - Vehicle Spare Parts Inventory
@endsection

@section('admin-content')

<div class="page-title-area">
    <div class="row align-items-center">
        <div class="col-md-8">
            <h4 class="page-title mb-1">Stock Adjustments</h4>
            <ul class="breadcrumbs">
                <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li><span>Stock Adjustments</span></li>
            </ul>
        </div>
        <div class="col-md-4 text-md-end mt-2 mt-md-0">
            @can('stock-adjustment.create')
            <a href="{{ route('admin.stock-adjustments.create') }}" class="btn btn-add text-white"><i class="bi bi-plus-lg"></i> New Adjustment</a>
            @endcan
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-striped align-middle">
                <thead>
                    <tr><th>Adjustment #</th><th>Warehouse</th><th>Reason</th><th>Items</th><th>Status</th><th>Created By</th><th>Date</th><th width="12%">Action</th></tr>
                </thead>
                <tbody>
                    @forelse ($adjustments as $adj)
                    <tr>
                        <td>{{ $adj->adjustment_number }}</td>
                        <td>{{ $adj->warehouse->name ?? '-' }}</td>
                        <td>{{ $adj->reason }}</td>
                        <td>{{ $adj->items->count() }}</td>
                        <td>
                            @if ($adj->status === 'pending') <span class="badge" style="background:var(--vsp-warning)">Pending</span>
                            @elseif ($adj->status === 'approved') <span class="badge bg-success">Approved</span>
                            @else <span class="badge bg-secondary">Rejected</span> @endif
                        </td>
                        <td>{{ $adj->createdBy->name ?? '-' }}</td>
                        <td class="small">{{ $adj->created_at->format('Y-m-d H:i') }}</td>
                        <td>
                            @if ($adj->status === 'pending')
                                @can('stock-adjustment.approve')
                                <form action="{{ route('admin.stock-adjustments.approve', $adj->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Approve and apply this adjustment to stock?');">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-outline-success"><i class="bi bi-check-lg"></i> Approve</button>
                                </form>
                                <form action="{{ route('admin.stock-adjustments.reject', $adj->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Reject this adjustment?');">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-outline-danger"><i class="bi bi-x-lg"></i></button>
                                </form>
                                @endcan
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="8" class="text-center text-muted py-4">No stock adjustments yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mt-3 gap-2">
            <div class="text-muted small">
                Showing {{ $adjustments->firstItem() ?? 0 }}
                to {{ $adjustments->lastItem() ?? 0 }}
                of {{ $adjustments->total() }} results
            </div>

            <div>
                {{ $adjustments->withQueryString()->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
</div>

@endsection
