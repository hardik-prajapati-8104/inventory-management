@extends('backend.layouts.master')

@section('title')
Admins - Vehicle Spare Parts Inventory
@endsection

@section('styles')
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">
@endsection

@section('admin-content')

<div class="page-title-area">
    <div class="row align-items-center">
        <div class="col-md-8">
            <h4 class="page-title mb-1">Admins</h4>
            <ul class="breadcrumbs">
                <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li><span>All Admins</span></li>
            </ul>
        </div>
        <div class="col-md-4 text-md-end mt-2 mt-md-0">
            @can('admin.create')
                <a class="btn btn-add text-white" href="{{ route('admin.admin.create') }}">
                    <i class="bi bi-plus-lg"></i> Add Admin
                </a>
            @endcan
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <table id="dataTable" class="table table-bordered table-striped display responsive nowrap w-100">
                    <thead>
                        <tr>
                            <th width="2%">#</th>
                            <th>Name</th>
                            <th>Username</th>
                            <th>Email</th>
                            <th>Contact No.</th>
                            <th>Role(s)</th>
                            <th>Status</th>
                            <th>Updated At</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($admins as $data)
                        <tr>
                            <td class="text-center">{{ $loop->index + 1 }}</td>
                            <td>{{ $data->name }}</td>
                            <td>{{ $data->username }}</td>
                            <td>{{ $data->email }}</td>
                            <td>{{ $data->mobile_number ?? '-' }}</td>
                            <td>
                                @foreach ($data->roles as $role)
                                    <span class="badge badge-info">{{ $role->name }}</span>
                                @endforeach
                            </td>
                            <td>
                                @if ($data->status)
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-secondary">Inactive</span>
                                @endif
                            </td>
                            <td>{{ $data->updated_at->format('Y-m-d H:i') }}</td>
                            <td>
                                <div class="dropdown">
                                    <button class="btn btn-secondary btn-sm dropdown-toggle" type="button" id="action_menu_{{ $data->id }}" data-bs-toggle="dropdown" aria-expanded="false">
                                        &#x22EE;
                                    </button>
                                    <ul class="dropdown-menu" aria-labelledby="action_menu_{{ $data->id }}">
                                        @can('admin.edit')
                                        <li>
                                            <a class="dropdown-item" href="{{ route('admin.admin.edit', $data->id) }}">
                                                <i class="bi bi-pencil me-1"></i> Edit
                                            </a>
                                        </li>
                                        @endcan
                                        @can('admin.delete')
                                        <li>
                                            <a class="dropdown-item text-danger" href="#"
                                                onclick="event.preventDefault(); document.getElementById('delete-form-{{ $data->id }}').submit();">
                                                <i class="bi bi-trash me-1"></i> Delete
                                            </a>
                                            <form id="delete-form-{{ $data->id }}" action="{{ route('admin.admin.destroy', $data->id) }}" method="POST" class="d-none">
                                                @method('DELETE')
                                                @csrf
                                            </form>
                                        </li>
                                        @endcan
                                    </ul>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js"></script>
<script>
    if ($('#dataTable').length) {
        $('#dataTable').DataTable({
            responsive: true,
            columnDefs: [
                { responsivePriority: 1, targets: 0 },
                { responsivePriority: 2, targets: 1 },
                { responsivePriority: 3, targets: 8 },
            ]
        });
    }
</script>
@endsection
