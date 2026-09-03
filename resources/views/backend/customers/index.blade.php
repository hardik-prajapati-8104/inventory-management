@extends('backend.layouts.master')

@section('title')
Customers - Vehicle Spare Parts Inventory
@endsection

@section('admin-content')

<div class="page-title-area">
    <div class="row align-items-center">
        <div class="col-md-7">
            <h4 class="page-title mb-1">Customers</h4>
            <ul class="breadcrumbs">
                <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li><span>Customers</span></li>
            </ul>
        </div>
        <div class="col-md-5 text-md-end mt-2 mt-md-0">
            @can('customer.create')
            <a href="{{ route('admin.customers.create') }}" class="btn btn-add text-white"><i class="bi bi-plus-lg"></i> Add Customer</a>
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
                    <tr><th>Code</th><th>Name</th><th>Contact</th><th>City</th><th>Outstanding</th><th>Status</th><th width="12%">Action</th></tr>
                </thead>
                <tbody>
                    @forelse ($customers as $c)
                    <tr>
                        <td>{{ $c->customer_code }}</td>
                        <td>{{ $c->customer_name }}{{ $c->company_name ? ' — '.$c->company_name : '' }}</td>
                        <td>{{ $c->mobile ?? $c->email ?? '-' }}</td>
                        <td>{{ $c->city ?? '-' }}</td>
                        <td>₹{{ number_format($c->outstanding_balance, 2) }}</td>
                        <td>@if ($c->status) <span class="badge bg-success">Active</span> @else <span class="badge bg-secondary">Inactive</span> @endif</td>
                        <td>
                            <a href="{{ route('admin.customers.ledger', $c->id) }}" class="btn btn-sm btn-outline-primary" title="Ledger"><i class="bi bi-journal-text"></i></a>
                            @can('customer.edit')
                            <a href="{{ route('admin.customers.edit', $c->id) }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-pencil"></i></a>
                            @endcan
                            @can('customer.delete')
                            <form action="{{ route('admin.customers.destroy', $c->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this customer?');">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                            </form>
                            @endcan
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="text-center text-muted py-4">No customers yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mt-3 gap-2">
            <div class="text-muted small">
                Showing {{ $customers->firstItem() ?? 0 }}
                to {{ $customers->lastItem() ?? 0 }}
                of {{ $customers->total() }} results
            </div>

            <div>
                {{ $customers->withQueryString()->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
</div>

@endsection
