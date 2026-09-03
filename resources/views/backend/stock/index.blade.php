@extends('backend.layouts.master')

@section('title')
Current Stock - Vehicle Spare Parts Inventory
@endsection

@section('admin-content')

<div class="page-title-area">
    <div class="row align-items-center">
        <div class="col-md-8">
            <h4 class="page-title mb-1">Current Stock</h4>
            <ul class="breadcrumbs">
                <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li><span>Current Stock</span></li>
            </ul>
        </div>
        <div class="col-md-4 text-md-end mt-2 mt-md-0">
            <a href="{{ route('admin.stock.movements') }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-clock-history"></i> Movement Ledger</a>
        </div>
    </div>
</div>

<div class="card mb-3">
    <div class="card-body">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-6">
                <label class="small mb-1">Search</label>
                <input type="text" name="q" class="form-control" placeholder="Name, part number, SKU…" value="{{ request('q') }}">
            </div>
            <div class="col-md-4">
                <label class="small mb-1">Warehouse</label>
                <select name="warehouse_id" class="form-select">
                    <option value="">All Warehouses</option>
                    @foreach ($warehouses as $wh)
                        <option value="{{ $wh->id }}" {{ request('warehouse_id') == $wh->id ? 'selected' : '' }}>{{ $wh->name }}</option>
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
                        <th>Part</th>
                        <th>Current Stock</th>
                        <th>Minimum Stock</th>
                        <th>Status</th>
                        <th>By Warehouse</th>
                        <th width="10%">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($spareParts as $part)
                    <tr>
                        <td>
                            <div class="fw-medium">{{ $part->name }}</div>
                            <div class="small text-muted">{{ $part->part_number }} &middot; {{ $part->sku }}</div>
                        </td>
                        <td>
                            @if ($part->current_stock <= 0)
                                <span class="badge bg-danger">0</span>
                            @elseif ($part->current_stock <= $part->minimum_stock)
                                <span class="badge" style="background:var(--vsp-warning)">{{ $part->current_stock }}</span>
                            @else
                                <span class="badge bg-success">{{ $part->current_stock }}</span>
                            @endif
                            <span class="small text-muted">{{ $part->unit->short_code ?? '' }}</span>
                        </td>
                        <td>{{ $part->minimum_stock }}</td>
                        <td>
                            @if ($part->current_stock <= 0)
                                <span class="badge bg-danger">⚠ OUT OF STOCK</span>
                            @elseif ($part->current_stock <= $part->minimum_stock)
                                <span class="badge" style="background:var(--vsp-warning)">⚠ LOW STOCK</span>
                            @else
                                <span class="badge bg-success">✓ In Stock</span>
                            @endif
                        </td>
                        <td>
                            @forelse ($part->stock as $s)
                                <span class="badge bg-light text-dark border me-1">{{ $s->warehouse->name ?? '-' }}: {{ $s->current_stock }}</span>
                            @empty
                                <span class="text-muted small">No warehouse assigned yet</span>
                            @endforelse
                        </td>
                        <td>
                            @can('spare-part.edit')
                            <a href="{{ route('admin.spare-parts.edit', $part->id) }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-pencil"></i></a>
                            @endcan
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="text-center text-muted py-4">No spare parts found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mt-3 gap-2">
            <div class="text-muted small">
                Showing {{ $spareParts->firstItem() ?? 0 }}
                to {{ $spareParts->lastItem() ?? 0 }}
                of {{ $spareParts->total() }} results
            </div>

            <div>
                {{ $spareParts->withQueryString()->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
</div>

@endsection
