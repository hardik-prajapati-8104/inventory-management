@extends('backend.layouts.master')

@section('title')
Damaged Stock - Vehicle Spare Parts Inventory
@endsection

@section('admin-content')

<div class="page-title-area">
    <div class="row align-items-center">
        <div class="col-12">
            <h4 class="page-title mb-1">Damaged Stock</h4>
            <ul class="breadcrumbs">
                <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li><a href="{{ route('admin.stock.index') }}">Inventory</a></li>
                <li><span>Damaged Stock</span></li>
            </ul>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body">
        @php $mode = 'damaged'; $emptyMessage = 'No damaged stock recorded.'; @endphp
        @include('backend.stock.partials.part-list')
    </div>
</div>

@endsection
