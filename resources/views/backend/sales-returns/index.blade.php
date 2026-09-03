@extends('backend.layouts.master')

@section('title')
Sales Returns - Vehicle Spare Parts Inventory
@endsection

@section('admin-content')

<div class="page-title-area">
    <div class="row align-items-center">
        <div class="col-md-8">
            <h4 class="page-title mb-1">Sales Returns</h4>
            <ul class="breadcrumbs">
                <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li><span>Sales Returns</span></li>
            </ul>
        </div>
        <div class="col-md-4 text-md-end mt-2 mt-md-0">
            @can('sale-return.create')
            <a href="{{ route('admin.sales-returns.create') }}" class="btn btn-add text-white"><i class="bi bi-plus-lg"></i> New Return</a>
            @endcan
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-striped align-middle">
                <thead><tr><th>Return #</th><th>Customer</th><th>Warehouse</th><th>Items</th><th>Date</th><th>Status</th><th width="12%">Action</th></tr></thead>
                <tbody>
                    @forelse ($returns as $r)
                    <tr>
                        <td>{{ $r->return_number }}</td>
                        <td>{{ $r->customer->customer_name ?? '-' }}</td>
                        <td>{{ $r->warehouse->name ?? '-' }}</td>
                        <td>{{ $r->items->count() }}</td>
                        <td class="small">{{ $r->return_date->format('Y-m-d') }}</td>
                        <td>
                            @if ($r->status === 'pending') <span class="badge" style="background:var(--vsp-warning)">Pending</span>
                            @else <span class="badge bg-success">Approved</span> @endif
                        </td>
                        <td>
                            @if ($r->status === 'pending')
                                @can('sale-return.approve')
                                <form action="{{ route('admin.sales-returns.approve', $r->id) }}" method="POST" onsubmit="return confirm('Approve this return? Resalable items go back to available stock, damaged/defective items go to damaged stock.');">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-outline-success"><i class="bi bi-check-lg"></i> Approve</button>
                                </form>
                                @endcan
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="text-center text-muted py-4">No sales returns yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $returns->links() }}
    </div>
</div>

@endsection
