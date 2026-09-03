@extends('backend.layouts.master')

@section('title')
Categories - Vehicle Spare Parts Inventory
@endsection

@section('admin-content')

<div class="page-title-area">
    <div class="row align-items-center">
        <div class="col-md-8">
            <h4 class="page-title mb-1">Categories</h4>
            <ul class="breadcrumbs">
                <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li><span>Categories</span></li>
            </ul>
        </div>
        <div class="col-md-4 text-md-end mt-2 mt-md-0">
            @can('spare-part.create')
            <button type="button" class="btn btn-add text-white" data-bs-toggle="modal" data-bs-target="#createCategoryModal">
                <i class="bi bi-plus-lg"></i> Add Category
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
                    <tr>
                        <th width="3%">#</th>
                        <th>Name</th>
                        <th>Parent Category</th>
                        <th>Status</th>
                        <th width="10%">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($categories as $cat)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td><i class="bi {{ $cat->icon ?: 'bi-tag' }} me-1 text-muted"></i>{{ $cat->name }}</td>
                        <td>{{ $cat->parent->name ?? '-' }}</td>
                        <td>
                            @if ($cat->status)
                                <span class="badge bg-success">Active</span>
                            @else
                                <span class="badge bg-secondary">Inactive</span>
                            @endif
                        </td>
                        <td>
                            @can('spare-part.edit')
                            <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#editCategoryModal{{ $cat->id }}">
                                <i class="bi bi-pencil"></i>
                            </button>
                            @endcan
                            @can('spare-part.delete')
                            <form action="{{ route('admin.categories.destroy', $cat->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this category?');">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                            </form>
                            @endcan
                        </td>
                    </tr>

                    {{-- Edit modal --}}
                    <div class="modal fade" id="editCategoryModal{{ $cat->id }}" tabindex="-1">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <form action="{{ route('admin.categories.update', $cat->id) }}" method="POST">
                                    @csrf @method('PUT')
                                    <div class="modal-header">
                                        <h6 class="modal-title">Edit Category</h6>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="mb-3">
                                            <label>Name<span class="text-error">*</span></label>
                                            <input type="text" name="name" required class="form-control" value="{{ $cat->name }}">
                                        </div>
                                        <div class="mb-3">
                                            <label>Parent Category</label>
                                            <select name="parent_id" class="form-select">
                                                <option value="">None (Top Level)</option>
                                                @foreach ($categories as $p)
                                                    @if ($p->id !== $cat->id)
                                                    <option value="{{ $p->id }}" {{ $cat->parent_id == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
                                                    @endif
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label>Icon (Bootstrap Icon class)</label>
                                            <input type="text" name="icon" class="form-control" value="{{ $cat->icon }}" placeholder="e.g. bi-gear">
                                        </div>
                                        <div class="mb-0">
                                            <label>Status</label>
                                            <select name="status" class="form-select">
                                                <option value="1" {{ $cat->status ? 'selected' : '' }}>Active</option>
                                                <option value="0" {{ ! $cat->status ? 'selected' : '' }}>Inactive</option>
                                            </select>
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
                    <tr><td colspan="5" class="text-center text-muted py-4">No categories yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Create modal --}}
<div class="modal fade" id="createCategoryModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.categories.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h6 class="modal-title">Add Category</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label>Name<span class="text-error">*</span></label>
                        <input type="text" name="name" required class="form-control" placeholder="e.g. Brake Parts">
                    </div>
                    <div class="mb-3">
                        <label>Parent Category</label>
                        <select name="parent_id" class="form-select">
                            <option value="">None (Top Level)</option>
                            @foreach ($categories as $p)
                                <option value="{{ $p->id }}">{{ $p->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-0">
                        <label>Icon (Bootstrap Icon class)</label>
                        <input type="text" name="icon" class="form-control" placeholder="e.g. bi-gear">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-add text-white">Save Category</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
