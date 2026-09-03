@extends('backend.layouts.master')

@section('title')
Add Customer - Vehicle Spare Parts Inventory
@endsection

@section('admin-content')

<div class="page-title-area">
    <div class="row align-items-center">
        <div class="col-md-8">
            <h4 class="page-title mb-1">Add Customer</h4>
            <ul class="breadcrumbs">
                <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li><a href="{{ route('admin.customers.index') }}">Customers</a></li>
                <li><span>Add</span></li>
            </ul>
        </div>
        <div class="col-md-4 text-md-end mt-2 mt-md-0">
            <a href="{{ route('admin.customers.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i> Back</a>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <form action="{{ route('admin.customers.store') }}" method="POST">
            @csrf
            @include('backend.customers.partials.form')
            <div class="row mt-4">
                <div class="text-center col-12">
                    <button type="submit" class="btn btn-add text-white pr-4 pl-4"><i class="bi bi-save"></i> Save Customer</button>
                    <a href="{{ route('admin.customers.index') }}" class="btn btn-outline-secondary pr-4 pl-4">Cancel</a>
                </div>
            </div>
        </form>
    </div>
</div>

@endsection
