@extends('backend.layouts.master')

@section('title')
Low Stock - Vehicle Spare Parts Inventory
@endsection

@section('admin-content')

<div class="page-title-area">
    <div class="row align-items-center">
        <div class="col-12">
            <h4 class="page-title mb-1">Low Stock</h4>
            <ul class="breadcrumbs">
                <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li><a href="{{ route('admin.stock.index') }}">Inventory</a></li>
                <li><span>Low Stock</span></li>
            </ul>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body">
        @php $mode = 'low'; $emptyMessage = 'Nothing is running low right now.'; @endphp
        @if ($spareParts->total() > 0)
            <p class="mb-3">
                <span class="badge" style="background:var(--vsp-warning); font-size: .9rem;">
                    ⚠ {{ $spareParts->total() }} {{ $spareParts->total() === 1 ? 'Product Is' : 'Products Are' }} Low in Stock
                </span>
            </p>
        @endif
        @include('backend.stock.partials.part-list')
    </div>
</div>

@endsection
