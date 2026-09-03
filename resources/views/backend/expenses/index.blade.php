@extends('backend.layouts.master')

@section('title')
Expenses - Vehicle Spare Parts Inventory
@endsection

@section('admin-content')

<div class="page-title-area">
    <div class="row align-items-center">
        <div class="col-md-8">
            <h4 class="page-title mb-1">Expenses</h4>
            <ul class="breadcrumbs">
                <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li><span>Expenses</span></li>
            </ul>
        </div>
        <div class="col-md-4 text-md-end mt-2 mt-md-0">
            @can('expense.create')
            <a href="{{ route('admin.expenses.create') }}" class="btn btn-add text-white"><i class="bi bi-plus-lg"></i> Add Expense</a>
            @endcan
        </div>
    </div>
</div>

<div class="card mb-3">
    <div class="card-body">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-3">
                <label class="small mb-1">Category</label>
                <select name="category" class="form-select">
                    <option value="">All Categories</option>
                    @foreach (['Transport','Delivery','Warehouse','Salary','Electricity','Packaging','Maintenance','Other'] as $cat)
                        <option value="{{ $cat }}" {{ request('category') == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="small mb-1">From</label>
                <input type="date" name="from" class="form-control" value="{{ request('from') }}">
            </div>
            <div class="col-md-3">
                <label class="small mb-1">To</label>
                <input type="date" name="to" class="form-control" value="{{ request('to') }}">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-add text-white w-100">Filter</button>
            </div>
        </form>
    </div>
</div>

<div class="row g-3 mb-3">
    <div class="col-md-4">
        <div class="vsp-kpi"><div class="vsp-kpi__icon"><i class="bi bi-receipt"></i></div><div><div class="vsp-kpi__value">₹{{ number_format($total, 2) }}</div><div class="vsp-kpi__label">Total (filtered)</div></div></div>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-striped align-middle">
                <thead><tr><th>Expense #</th><th>Category</th><th>Date</th><th>Amount</th><th>Method</th><th>Description</th><th width="10%">Action</th></tr></thead>
                <tbody>
                    @forelse ($expenses as $e)
                    <tr>
                        <td>{{ $e->expense_number }}</td>
                        <td><span class="badge badge-info">{{ $e->category }}</span></td>
                        <td class="small">{{ $e->expense_date->format('Y-m-d') }}</td>
                        <td>₹{{ number_format($e->amount, 2) }}</td>
                        <td>{{ ucfirst(str_replace('_',' ',$e->payment_method)) }}</td>
                        <td class="small">{{ \Illuminate\Support\Str::limit($e->description, 40) }}</td>
                        <td>
                            @can('expense.edit')
                            <a href="{{ route('admin.expenses.edit', $e->id) }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-pencil"></i></a>
                            @endcan
                            @can('expense.delete')
                            <form action="{{ route('admin.expenses.destroy', $e->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this expense?');">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                            </form>
                            @endcan
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="text-center text-muted py-4">No expenses recorded yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $expenses->links() }}
    </div>
</div>

@endsection
