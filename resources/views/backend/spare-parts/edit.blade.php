@extends('backend.layouts.master')

@section('title')
Edit Spare Part - Vehicle Spare Parts Inventory
@endsection

@section('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/css/select2.min.css" rel="stylesheet" />
@endsection

@section('admin-content')

<div class="page-title-area">
    <div class="row align-items-center">
        <div class="col-md-8">
            <h4 class="page-title mb-1">Edit Spare Part — {{ $sparePart->name }}</h4>
            <ul class="breadcrumbs">
                <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li><a href="{{ route('admin.spare-parts.index') }}">Spare Parts</a></li>
                <li><span>Edit</span></li>
            </ul>
        </div>
        <div class="col-md-4 text-md-end mt-2 mt-md-0">
            <a href="{{ route('admin.spare-parts.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i> Back</a>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <form action="{{ route('admin.spare-parts.update', $sparePart->id) }}" method="POST" enctype="multipart/form-data" id="sparePartForm">
            @csrf
            @method('PUT')
            <input type="hidden" id="sparePartId" value="{{ $sparePart->id }}">

            @include('backend.spare-parts.partials.form')

            <div class="row mt-4">
                <div class="text-center col-12">
                    <button type="submit" class="btn btn-add text-white pr-4 pl-4">
                        <i class="bi bi-save"></i> Update
                    </button>
                    <a href="{{ route('admin.spare-parts.index') }}" class="btn btn-outline-secondary pr-4 pl-4">
                        <i class="bi bi-x-lg"></i> Cancel
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

@endsection

@section('scripts')
@include('backend.spare-parts.partials.scripts')
@endsection
