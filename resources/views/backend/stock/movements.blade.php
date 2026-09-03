@extends('backend.layouts.master')

@section('title')
Stock Movement - Vehicle Spare Parts Inventory
@endsection

@section('admin-content')

<div class="page-title-area">
    <div class="row align-items-center">
        <div class="col-12">
            <h4 class="page-title mb-1">Stock Movement Ledger</h4>
            <ul class="breadcrumbs">
                <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li><a href="{{ route('admin.stock.index') }}">Inventory</a></li>
                <li><span>Stock Movement</span></li>
            </ul>
            <p class="small text-muted mb-0 mt-2">
                The complete, append-only history every current-stock figure in the
                system is derived from — nothing here is ever edited, only added to.
            </p>
        </div>
    </div>
</div>

<div class="card mb-3">
    <div class="card-body">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-4">
                <label class="small mb-1">Movement Type</label>
                <select name="type" class="form-select">
                    <option value="">All Types</option>
                    @foreach (['OPENING_STOCK','PURCHASE','SALE','PURCHASE_RETURN','SALES_RETURN','TRANSFER_IN','TRANSFER_OUT','ADJUSTMENT_IN','ADJUSTMENT_OUT','DAMAGE'] as $type)
                        <option value="{{ $type }}" {{ request('type') == $type ? 'selected' : '' }}>{{ ucwords(str_replace('_', ' ', strtolower($type))) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-add text-white w-100"><i class="bi bi-search"></i> Filter</button>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-striped align-middle">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Part</th>
                        <th>Warehouse</th>
                        <th>Type</th>
                        <th>Qty</th>
                        <th>Before → After</th>
                        <th>Reference</th>
                        <th>By</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($movements as $m)
                    <tr>
                        <td class="small">{{ $m->created_at->format('Y-m-d H:i') }}</td>
                        <td>{{ $m->sparePart->name ?? '-' }}</td>
                        <td>{{ $m->warehouse->name ?? '-' }}</td>
                        <td>
                            <span class="badge {{ $m->quantity >= 0 ? 'bg-success' : 'bg-danger' }}">
                                {{ ucwords(str_replace('_', ' ', strtolower($m->type))) }}
                            </span>
                        </td>
                        <td class="{{ $m->quantity >= 0 ? 'text-success' : 'text-danger' }}">{{ $m->quantity >= 0 ? '+' : '' }}{{ $m->quantity }}</td>
                        <td class="small">{{ $m->stock_before }} → {{ $m->stock_after }}</td>
                        <td class="small text-muted">{{ $m->notes ?? class_basename($m->reference_type ?? '') }}</td>
                        <td class="small">{{ $m->createdBy->name ?? 'System' }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="8" class="text-center text-muted py-4">No stock movements recorded yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $movements->links() }}
    </div>
</div>

@endsection
