@extends('backend.layouts.master')

@section('title')
Import Preview - Vehicle Spare Parts Inventory
@endsection

@section('admin-content')

<div class="page-title-area">
    <div class="row align-items-center">
        <div class="col-12">
            <h4 class="page-title mb-1">
                Import Preview — {{ $filename }}
                <span class="badge {{ $sourceType === 'pdf' ? 'bg-danger text-white' : 'badge-info' }} align-middle">{{ strtoupper($sourceType) }}</span>
            </h4>
            <ul class="breadcrumbs">
                <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li><a href="{{ route('admin.spare-parts.import.create') }}">Import Spare Parts</a></li>
                <li><span>Preview</span></li>
            </ul>
        </div>
    </div>
</div>

<div class="row g-3 mb-3">
    <div class="col-md-3">
        <div class="vsp-kpi"><div class="vsp-kpi__icon"><i class="bi bi-file-earmark-spreadsheet"></i></div><div><div class="vsp-kpi__value">{{ $total }}</div><div class="vsp-kpi__label">Total Rows</div></div></div>
    </div>
    <div class="col-md-3">
        <div class="vsp-kpi"><div class="vsp-kpi__icon" style="color:var(--vsp-success)"><i class="bi bi-plus-circle"></i></div><div><div class="vsp-kpi__value">{{ $createCount }}</div><div class="vsp-kpi__label">New Parts to Create</div></div></div>
    </div>
    <div class="col-md-3">
        <div class="vsp-kpi"><div class="vsp-kpi__icon" style="color:var(--vsp-primary,#3b7ddd)"><i class="bi bi-arrow-repeat"></i></div><div><div class="vsp-kpi__value">{{ $restockCount }}</div><div class="vsp-kpi__label">Existing Parts to Restock</div></div></div>
    </div>
    <div class="col-md-3">
        <div class="vsp-kpi"><div class="vsp-kpi__icon" style="color:var(--vsp-danger)"><i class="bi bi-exclamation-triangle"></i></div><div><div class="vsp-kpi__value">{{ count($errorRows) }}</div><div class="vsp-kpi__label">Will Be Skipped</div></div></div>
    </div>
</div>

@if (count($errorRows) > 0)
<div class="card mb-3">
    <div class="card-body">
        <h6 class="mb-3 text-danger"><i class="bi bi-exclamation-triangle"></i> Rows That Will Be Skipped</h6>
        <div class="table-responsive" style="max-height: 320px; overflow-y: auto;">
            <table class="table table-bordered table-sm">
                <thead><tr><th width="6%">Row</th><th>Part Name</th><th>Part Number</th><th>Reason</th></tr></thead>
                <tbody>
                    @foreach ($errorRows as $row)
                    <tr class="table-danger">
                        <td>{{ $row['row'] }}</td>
                        <td>{{ $row['part_name'] ?: '-' }}</td>
                        <td>{{ $row['part_number'] ?: '-' }}</td>
                        <td class="small">{{ implode(' ', $row['errors']) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endif

<div class="card mb-3">
    <div class="card-body">
        <h6 class="mb-3 text-success"><i class="bi bi-check-circle"></i> Rows Ready to Import</h6>
        <p class="small text-muted mb-2">
            <span class="badge badge-info">CREATE</span> adds a brand-new spare part with the row's quantity as opening stock.
            <span class="badge" style="background:var(--vsp-primary,#3b7ddd);color:#fff;">RESTOCK</span> adds the row's quantity to an existing part's current stock instead of creating a duplicate.
        </p>
        <div class="table-responsive" style="max-height: 420px; overflow-y: auto;">
            <table class="table table-bordered table-sm table-striped">
                <thead><tr><th width="5%">Row</th><th width="9%">Action</th><th>Part Name</th><th>Part Number</th><th>Category</th><th>Brand</th><th>Purchase Price</th><th>Qty</th><th>Stock After</th><th>Warehouse</th></tr></thead>
                <tbody>
                    @forelse ($validRows as $row)
                    <tr>
                        <td>{{ $row['row'] }}</td>
                        <td>
                            @if ($row['action'] === 'restock')
                                <span class="badge" style="background:var(--vsp-primary,#3b7ddd);color:#fff;"><i class="bi bi-arrow-repeat"></i> Restock</span>
                            @else
                                <span class="badge badge-info"><i class="bi bi-plus-circle"></i> Create</span>
                            @endif
                        </td>
                        <td>
                            {{ $row['part_name'] }}
                            @if (!empty($row['note']))
                                <div class="small text-muted">{{ $row['note'] }}</div>
                            @endif
                        </td>
                        <td>{{ $row['part_number'] ?: 'auto' }}</td>
                        <td>{{ $row['category'] ?: '-' }}</td>
                        <td>{{ $row['brand'] ?: '-' }}</td>
                        <td>₹{{ number_format($row['purchase_price'], 2) }}</td>
                        <td>{{ $row['opening_stock'] }}</td>
                        <td>
                            @if ($row['action'] === 'restock')
                                {{ $row['matched_current_stock'] ?? 0 }} &rarr; <strong>{{ ($row['matched_current_stock'] ?? 0) + $row['opening_stock'] }}</strong>
                            @else
                                <strong>{{ $row['opening_stock'] }}</strong> (new)
                            @endif
                        </td>
                        <td>{{ $row['warehouse'] ?: 'Default' }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="10" class="text-center text-muted py-3">Nothing to import — every row had an error.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<form action="{{ route('admin.spare-parts.import.confirm') }}" method="POST">
    @csrf
    <input type="hidden" name="token" value="{{ $token }}">
    <div class="text-center">
        <button type="submit" class="btn btn-add text-white pr-4 pl-4" {{ count($validRows) === 0 ? 'disabled' : '' }}>
            <i class="bi bi-check-lg"></i> Confirm Import — Create {{ $createCount }} &amp; Restock {{ $restockCount }} Spare Part(s)
        </button>
        <a href="{{ route('admin.spare-parts.import.create') }}" class="btn btn-outline-secondary pr-4 pl-4">Cancel</a>
    </div>
</form>

@endsection
