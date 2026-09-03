@extends('backend.layouts.master')

@section('title')
Add Menu - Vehicle Spare Parts Inventory
@endsection

@section('admin-content')

<div class="page-title-area">
    <div class="row align-items-center">
        <div class="col-12">
            <h4 class="page-title mb-1">Add Menu</h4>
            <ul class="breadcrumbs">
                <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li><a href="{{ route('admin.menus.index') }}">Menu Management</a></li>
                <li><span>Add Menu</span></li>
            </ul>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <form action="{{ route('admin.menus.store') }}" method="POST">
            @csrf
            @include('backend.menus._form')

            <div class="mt-4">
                <button type="submit" class="btn btn-add text-white pr-4 pl-4"><i class="bi bi-check-lg"></i> Save Menu</button>
                <a href="{{ route('admin.menus.index') }}" class="btn btn-outline-secondary pr-4 pl-4">Cancel</a>
            </div>
        </form>
    </div>
</div>

@endsection
