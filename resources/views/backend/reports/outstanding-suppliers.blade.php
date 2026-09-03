@extends('backend.layouts.master')

@section('title')
Supplier Outstanding - Vehicle Spare Parts Inventory
@endsection

@section('admin-content')

<div class="page-title-area">
    <div class="row align-items-center">
        <div class="col-12">
            <h4 class="page-title mb-1">Supplier Outstanding Balances</h4>
            <ul class="breadcrumbs">
                <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li><a href="{{ route('admin.reports.index') }}">Reports</a></li>
                <li><span>Supplier Outstanding</span></li>
            </ul>
        </div>
    </div>
</div>

<div class="row g-3 mb-3">
    <div class="col-md-4">
        <div class="vsp-kpi"><div class="vsp-kpi__icon"><i class="bi bi-cash-coin"></i></div><div><div class="vsp-kpi__value">₹{{ number_format($total, 2) }}</div><div class="vsp-kpi__label">Total Outstanding</div></div></div>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-striped align-middle">
                <thead><tr><th>Supplier</th><th>Contact</th><th>Outstanding</th><th width="10%">Action</th></tr></thead>
                <tbody>
                    @forelse ($suppliers as $s)
                    <tr>
                        <td>{{ $s->company_name }}</td>
                        <td>{{ $s->mobile ?? $s->email ?? '-' }}</td>
                        <td class="text-danger">₹{{ number_format($s->due_sum, 2) }}</td>
                        <td><a href="{{ route('admin.suppliers.ledger', $s->id) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-journal-text"></i></a></td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="text-center text-muted py-4">No outstanding supplier balances — everyone's paid up.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection
