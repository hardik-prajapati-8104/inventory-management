@extends('backend.layouts.master')

@section('title')
Purchase Returns - Vehicle Spare Parts Inventory
@endsection

@section('admin-content')

<div class="page-title-area">
    <div class="row align-items-center">
        <div class="col-md-8">
            <h4 class="page-title mb-1">Purchase Returns</h4>
            <ul class="breadcrumbs">
                <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li><span>Purchase Returns</span></li>
            </ul>
        </div>
        <div class="col-md-4 text-md-end mt-2 mt-md-0">
            @can('purchase-return.create')
            <a href="{{ route('admin.purchase-returns.create') }}" class="btn btn-add text-white"><i class="bi bi-plus-lg"></i> New Return</a>
            @endcan
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-striped align-middle">
                <thead><tr><th>Return #</th><th>Supplier</th><th>Warehouse</th><th>Items</th><th>Date</th><th>Status</th><th width="12%">Action</th></tr></thead>
                <tbody>
                    @forelse ($returns as $r)
                    <tr>
                        <td>{{ $r->return_number }}</td>
                        <td>{{ $r->supplier->company_name ?? '-' }}</td>
                        <td>{{ $r->warehouse->name ?? '-' }}</td>
                        <td>{{ $r->items->count() }}</td>
                        <td class="small">{{ $r->return_date->format('Y-m-d') }}</td>
                        <td>
                            @if ($r->status === 'pending') <span class="badge" style="background:var(--vsp-warning)">Pending</span>
                            @else <span class="badge bg-success">Approved</span> @endif
                        </td>
                        <td>
                            @if ($r->status === 'pending')
                                @can('purchase-return.approve')
                                <form action="{{ route('admin.purchase-returns.approve', $r->id) }}" method="POST" onsubmit="return confirm('Approve this return and decrease stock now?');">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-outline-success"><i class="bi bi-check-lg"></i> Approve</button>
                                </form>
                                @endcan
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="text-center text-muted py-4">No purchase returns yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $returns->links() }}
    </div>
</div>

@endsection
