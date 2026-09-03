@extends('backend.layouts.master')

@section('title')
Supplier Price Comparison - Vehicle Spare Parts Inventory
@endsection

@section('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/css/select2.min.css" rel="stylesheet" />
@endsection

@section('admin-content')

<div class="page-title-area">
    <div class="row align-items-center">
        <div class="col-12">
            <h4 class="page-title mb-1">Supplier Price Comparison</h4>
            <ul class="breadcrumbs">
                <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li><a href="{{ route('admin.reports.index') }}">Reports</a></li>
                <li><span>Supplier Price Comparison</span></li>
            </ul>
            <p class="small text-muted mb-0 mt-2">
                Pick a spare part to see every supplier who's sold it, built
                entirely from your purchase history — no separate price list to
                maintain.
            </p>
        </div>
    </div>
</div>

<div class="card mb-3">
    <div class="card-body">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-6">
                <label class="small mb-1">Spare Part</label>
                <select name="spare_part_id" class="form-select select2" onchange="this.form.submit()">
                    <option value="">Select a spare part…</option>
                    @foreach ($spareParts as $p)
                        <option value="{{ $p->id }}" {{ request('spare_part_id') == $p->id ? 'selected' : '' }}>{{ $p->name }} ({{ $p->part_number }})</option>
                    @endforeach
                </select>
            </div>
        </form>
    </div>
</div>

@if ($part)
<div class="card">
    <div class="card-body">
        <h6 class="mb-3">{{ $part->name }} <span class="small text-muted">({{ $part->part_number }})</span></h6>

        @if ($rows->isEmpty())
            <p class="text-muted text-center py-4 mb-0">No purchase history for this part yet — nothing to compare.</p>
        @else
            @php $cheapestId = $rows->sortBy('lowest_price')->first()->supplier_id ?? null; @endphp
            <div class="table-responsive">
                <table class="table table-bordered table-striped align-middle">
                    <thead>
                        <tr>
                            <th>Supplier</th>
                            <th>Purchases</th>
                            <th>Total Qty</th>
                            <th>Lowest Price</th>
                            <th>Average Price</th>
                            <th>Highest Price</th>
                            <th>Last Price Paid</th>
                            <th>Last Purchased</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($rows as $row)
                        <tr class="{{ $row->supplier_id == $cheapestId ? 'table-success' : '' }}">
                            <td>
                                <a href="{{ route('admin.suppliers.ledger', $row->supplier_id) }}">{{ $row->company_name }}</a>
                                @if ($row->supplier_id == $cheapestId)
                                    <span class="badge bg-success ms-1">Best Price</span>
                                @endif
                            </td>
                            <td>{{ $row->purchase_count }}</td>
                            <td>{{ $row->total_qty }}</td>
                            <td>₹{{ number_format($row->lowest_price, 2) }}</td>
                            <td>₹{{ number_format($row->avg_price, 2) }}</td>
                            <td>₹{{ number_format($row->highest_price, 2) }}</td>
                            <td>₹{{ number_format($row->last_price, 2) }}</td>
                            <td class="small">{{ \Carbon\Carbon::parse($row->last_purchase_date)->format('Y-m-d') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
@endif

@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js"></script>
<script>$('.select2').select2({ width: '100%' });</script>
@endsection
