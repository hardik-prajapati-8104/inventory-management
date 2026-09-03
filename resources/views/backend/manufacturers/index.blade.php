@extends('backend.layouts.master')

@section('title')
Manufacturers - Vehicle Spare Parts Inventory
@endsection

@section('admin-content')

<div class="page-title-area">
    <div class="row align-items-center">
        <div class="col-md-8">
            <h4 class="page-title mb-1">Manufacturers</h4>
            <ul class="breadcrumbs">
                <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li><span>Manufacturers</span></li>
            </ul>
        </div>
        <div class="col-md-4 text-md-end mt-2 mt-md-0">
            @can('spare-part.create')
            <button type="button" class="btn btn-add text-white" data-bs-toggle="modal" data-bs-target="#createManufacturerModal">
                <i class="bi bi-plus-lg"></i> Add Manufacturer
            </button>
            @endcan
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-striped align-middle">
                <thead><tr><th width="3%">#</th><th>Name</th><th>Country</th><th>Status</th><th width="10%">Action</th></tr></thead>
                <tbody>
                    @forelse ($manufacturers as $m)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $m->name }}</td>
                        <td>{{ $m->country ?? '-' }}</td>
                        <td>
                            @if ($m->status) <span class="badge bg-success">Active</span>
                            @else <span class="badge bg-secondary">Inactive</span> @endif
                        </td>
                        <td>
                            @can('spare-part.edit')
                            <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#editManufacturerModal{{ $m->id }}"><i class="bi bi-pencil"></i></button>
                            @endcan
                            @can('spare-part.delete')
                            <form action="{{ route('admin.manufacturers.destroy', $m->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this manufacturer?');">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                            </form>
                            @endcan
                        </td>
                    </tr>
                    <div class="modal fade" id="editManufacturerModal{{ $m->id }}" tabindex="-1">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <form action="{{ route('admin.manufacturers.update', $m->id) }}" method="POST">
                                    @csrf @method('PUT')
                                    <div class="modal-header"><h6 class="modal-title">Edit Manufacturer</h6><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                                    <div class="modal-body">
                                        <div class="mb-3">
                                            <label>Name<span class="text-error">*</span></label>
                                            <input type="text" name="name" required class="form-control" value="{{ $m->name }}">
                                        </div>
                                        <div class="mb-0">
                                            <label>Country</label>
                                            <input type="text" name="country" class="form-control" value="{{ $m->country }}">
                                        </div>
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
                    <tr><td colspan="5" class="text-center text-muted py-4">No manufacturers yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="createManufacturerModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.manufacturers.store') }}" method="POST">
                @csrf
                <div class="modal-header"><h6 class="modal-title">Add Manufacturer</h6><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label>Name<span class="text-error">*</span></label>
                        <input type="text" name="name" required class="form-control" placeholder="e.g. Denso">
                    </div>
                    <div class="mb-0">
                        <label>Country</label>
                        <input type="text" name="country" class="form-control" placeholder="e.g. Japan">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-add text-white">Save Manufacturer</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
