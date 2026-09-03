@extends('backend.layouts.master')

@section('title')
Units - Vehicle Spare Parts Inventory
@endsection

@section('admin-content')

<div class="page-title-area">
    <div class="row align-items-center">
        <div class="col-md-8">
            <h4 class="page-title mb-1">Units</h4>
            <ul class="breadcrumbs">
                <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li><span>Units</span></li>
            </ul>
        </div>
        <div class="col-md-4 text-md-end mt-2 mt-md-0">
            @can('spare-part.create')
            <button type="button" class="btn btn-add text-white" data-bs-toggle="modal" data-bs-target="#createUnitModal">
                <i class="bi bi-plus-lg"></i> Add Unit
            </button>
            @endcan
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-striped align-middle">
                <thead><tr><th width="3%">#</th><th>Name</th><th>Short Code</th><th>Status</th><th width="10%">Action</th></tr></thead>
                <tbody>
                    @forelse ($units as $unit)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $unit->name }}</td>
                        <td>{{ $unit->short_code ?? '-' }}</td>
                        <td>
                            @if ($unit->status) <span class="badge bg-success">Active</span>
                            @else <span class="badge bg-secondary">Inactive</span> @endif
                        </td>
                        <td>
                            @can('spare-part.edit')
                            <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#editUnitModal{{ $unit->id }}"><i class="bi bi-pencil"></i></button>
                            @endcan
                            @can('spare-part.delete')
                            <form action="{{ route('admin.units.destroy', $unit->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this unit?');">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                            </form>
                            @endcan
                        </td>
                    </tr>
                    <div class="modal fade" id="editUnitModal{{ $unit->id }}" tabindex="-1">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <form action="{{ route('admin.units.update', $unit->id) }}" method="POST">
                                    @csrf @method('PUT')
                                    <div class="modal-header"><h6 class="modal-title">Edit Unit</h6><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                                    <div class="modal-body">
                                        <div class="mb-3">
                                            <label>Name<span class="text-error">*</span></label>
                                            <input type="text" name="name" required class="form-control" value="{{ $unit->name }}">
                                        </div>
                                        <div class="mb-0">
                                            <label>Short Code</label>
                                            <input type="text" name="short_code" class="form-control" value="{{ $unit->short_code }}">
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
                    <tr><td colspan="5" class="text-center text-muted py-4">No units yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="createUnitModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.units.store') }}" method="POST">
                @csrf
                <div class="modal-header"><h6 class="modal-title">Add Unit</h6><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label>Name<span class="text-error">*</span></label>
                        <input type="text" name="name" required class="form-control" placeholder="e.g. Piece">
                    </div>
                    <div class="mb-0">
                        <label>Short Code</label>
                        <input type="text" name="short_code" class="form-control" placeholder="e.g. pcs">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-add text-white">Save Unit</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
