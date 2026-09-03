@extends('backend.layouts.master')

@section('title')
Suppliers - Vehicle Spare Parts Inventory
@endsection

@section('admin-content')

<div class="page-title-area">
    <div class="row align-items-center">
        <div class="col-md-7">
            <h4 class="page-title mb-1">Suppliers</h4>
            <ul class="breadcrumbs">
                <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li><span>Suppliers</span></li>
            </ul>
        </div>
        <div class="col-md-5 text-md-end mt-2 mt-md-0">
            @can('supplier.create')
            <a href="{{ route('admin.suppliers.create') }}" class="btn btn-add text-white"><i class="bi bi-plus-lg"></i> Add Supplier</a>
            @endcan
        </div>
    </div>
</div>

<div class="card mb-3">
    <div class="card-body">
        <form method="GET" class="row g-2">
            <div class="col-md-6">
                <input type="text" name="q" class="form-control" placeholder="Search by name, code, mobile, email…" value="{{ request('q') }}">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-add text-white w-100"><i class="bi bi-search"></i></button>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-striped align-middle">
                <thead>
                    <tr><th>Code</th><th>Company</th><th>Contact</th><th>City</th><th>Outstanding</th><th>Status</th><th width="12%">Action</th></tr>
                </thead>
                <tbody>
                    @forelse ($suppliers as $s)
                    <tr>
                        <td>{{ $s->supplier_code }}</td>
                        <td>{{ $s->company_name }}</td>
                        <td>{{ $s->contact_person ?? '-' }}<div class="small text-muted">{{ $s->mobile ?? $s->email ?? '' }}</div></td>
                        <td>{{ $s->city ?? '-' }}</td>
                        <td>₹{{ number_format($s->outstanding_balance, 2) }}</td>
                        <td>@if ($s->status) <span class="badge bg-success">Active</span> @else <span class="badge bg-secondary">Inactive</span> @endif</td>
                        <td>
                            <a href="{{ route('admin.suppliers.ledger', $s->id) }}" class="btn btn-sm btn-outline-primary" title="Ledger"><i class="bi bi-journal-text"></i></a>
                            @can('supplier.edit')
                            <a href="{{ route('admin.suppliers.edit', $s->id) }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-pencil"></i></a>
                            @endcan
                            @can('supplier.delete')
                            <form action="{{ route('admin.suppliers.destroy', $s->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this supplier?');">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                            </form>
                            @endcan
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="text-center text-muted py-4">No suppliers yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $suppliers->links() }}
    </div>
</div>

@endsection
