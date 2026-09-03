@extends('backend.layouts.master')

@section('title')
Brands - Vehicle Spare Parts Inventory
@endsection

@section('admin-content')

<div class="page-title-area">
    <div class="row align-items-center">
        <div class="col-md-8">
            <h4 class="page-title mb-1">Brands</h4>
            <ul class="breadcrumbs">
                <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li><span>Brands</span></li>
            </ul>
        </div>
        <div class="col-md-4 text-md-end mt-2 mt-md-0">
            @can('spare-part.create')
            <button type="button" class="btn btn-add text-white" data-bs-toggle="modal" data-bs-target="#createBrandModal">
                <i class="bi bi-plus-lg"></i> Add Brand
            </button>
            @endcan
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-striped align-middle">
                <thead>
                    <tr><th width="3%">#</th><th>Name</th><th>Status</th><th width="10%">Action</th></tr>
                </thead>
                <tbody>
                    @forelse ($brands as $brand)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $brand->name }}</td>
                        <td>
                            @if ($brand->status) <span class="badge bg-success">Active</span>
                            @else <span class="badge bg-secondary">Inactive</span> @endif
                        </td>
                        <td>
                            @can('spare-part.edit')
                            <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#editBrandModal{{ $brand->id }}"><i class="bi bi-pencil"></i></button>
                            @endcan
                            @can('spare-part.delete')
                            <form action="{{ route('admin.brands.destroy', $brand->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this brand?');">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                            </form>
                            @endcan
                        </td>
                    </tr>
                    <div class="modal fade" id="editBrandModal{{ $brand->id }}" tabindex="-1">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <form action="{{ route('admin.brands.update', $brand->id) }}" method="POST">
                                    @csrf @method('PUT')
                                    <div class="modal-header"><h6 class="modal-title">Edit Brand</h6><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                                    <div class="modal-body">
                                        <label>Name<span class="text-error">*</span></label>
                                        <input type="text" name="name" required class="form-control" value="{{ $brand->name }}">
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                                        <button type="submit" class="btn btn-add text-white">Save Changes</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    @empty
                    <tr><td colspan="4" class="text-center text-muted py-4">No brands yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="createBrandModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.brands.store') }}" method="POST">
                @csrf
                <div class="modal-header"><h6 class="modal-title">Add Brand</h6><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <div class="modal-body">
                    <label>Name<span class="text-error">*</span></label>
                    <input type="text" name="name" required class="form-control" placeholder="e.g. Bosch">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-add text-white">Save Brand</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
