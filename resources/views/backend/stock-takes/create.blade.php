@extends('backend.layouts.master')

@section('title')
New Stock Take - Vehicle Spare Parts Inventory
@endsection

@section('admin-content')

<div class="page-title-area">
    <div class="row align-items-center">
        <div class="col-md-8">
            <h4 class="page-title mb-1">New Stock Take</h4>
            <ul class="breadcrumbs">
                <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li><a href="{{ route('admin.stock-takes.index') }}">Stock Takes</a></li>
                <li><span>New</span></li>
            </ul>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <p class="text-muted small">
            Select a warehouse to generate a count sheet — a snapshot of every spare
            part's current system quantity at this moment. Sales, purchases, or
            transfers that happen during the physical count won't affect what you're
            comparing against.
        </p>
        <form action="{{ route('admin.stock-takes.store') }}" method="POST" class="row g-3">
            @csrf
            <div class="col-md-6">
                <label>Warehouse<span class="text-error">*</span></label>
                <select name="warehouse_id" required class="form-select">
                    <option value="">Select Warehouse</option>
                    @foreach ($warehouses as $wh)<option value="{{ $wh->id }}">{{ $wh->name }}</option>@endforeach
                </select>
            </div>
            <div class="col-12">
                <button type="submit" class="btn btn-add text-white"><i class="bi bi-clipboard-check"></i> Generate Stock Sheet</button>
            </div>
        </form>
    </div>
</div>

@endsection
