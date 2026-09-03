@extends('backend.layouts.master')

@section('title')
Stock Transfers - Vehicle Spare Parts Inventory
@endsection

@section('admin-content')

<div class="page-title-area">
    <div class="row align-items-center">
        <div class="col-md-8">
            <h4 class="page-title mb-1">Stock Transfers</h4>
            <ul class="breadcrumbs">
                <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li><span>Stock Transfers</span></li>
            </ul>
        </div>
        <div class="col-md-4 text-md-end mt-2 mt-md-0">
            @can('stock-transfer.create')
            <a href="{{ route('admin.stock-transfers.create') }}" class="btn btn-add text-white"><i class="bi bi-plus-lg"></i> New Transfer</a>
            @endcan
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-striped align-middle">
                <thead>
                    <tr><th>Transfer #</th><th>From</th><th>To</th><th>Items</th><th>Status</th><th>Requested By</th><th>Date</th><th width="14%">Action</th></tr>
                </thead>
                <tbody>
                    @forelse ($transfers as $t)
                    <tr>
                        <td>{{ $t->transfer_number }}</td>
                        <td>{{ $t->fromWarehouse->name ?? '-' }}</td>
                        <td>{{ $t->toWarehouse->name ?? '-' }}</td>
                        <td>{{ $t->items->count() }}</td>
                        <td>
                            @if ($t->status === 'pending') <span class="badge" style="background:var(--vsp-warning)">Pending</span>
                            @elseif ($t->status === 'received') <span class="badge bg-success">Received</span>
                            @else <span class="badge bg-secondary">{{ ucfirst($t->status) }}</span> @endif
                        </td>
                        <td>{{ $t->requestedBy->name ?? '-' }}</td>
                        <td class="small">{{ $t->transfer_date->format('Y-m-d') }}</td>
                        <td>
                            @if ($t->status === 'pending')
                                @can('stock-transfer.approve')
                                <form action="{{ route('admin.stock-transfers.approve', $t->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Approve and move this stock now?');">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-outline-success"><i class="bi bi-check-lg"></i> Approve &amp; Move</button>
                                </form>
                                <form action="{{ route('admin.stock-transfers.cancel', $t->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Cancel this transfer request?');">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-outline-danger"><i class="bi bi-x-lg"></i></button>
                                </form>
                                @endcan
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="8" class="text-center text-muted py-4">No stock transfers yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mt-3 gap-2">
            <div class="text-muted small">
                Showing {{ $transfers->firstItem() ?? 0 }}
                to {{ $transfers->lastItem() ?? 0 }}
                of {{ $transfers->total() }} results
            </div>

            <div>
                {{ $transfers->withQueryString()->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
</div>

@endsection
