@extends('backend.layouts.master')

@section('title')
Admin Edit - Vehicle Spare Parts Inventory
@endsection

@section('admin-content')

@php $assignedRoles = $admin->roles->pluck('name')->toArray(); @endphp

<div class="page-title-area">
    <div class="row align-items-center">
        <div class="col-md-8">
            <h4 class="page-title mb-1">Edit Admin — {{ $admin->name }}</h4>
            <ul class="breadcrumbs">
                <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li><a href="{{ route('admin.admin.index') }}">All Admins</a></li>
                <li><span>Edit Admin</span></li>
            </ul>
        </div>
        <div class="col-md-4 text-md-end mt-2 mt-md-0">
            <button type="button" class="btn btn-add text-white" onclick="$('#submitForm').click();">
                <i class="bi bi-save"></i> Update
            </button>
            <a href="{{ route('admin.admin.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Back
            </a>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <form action="{{ route('admin.admin.update', $admin->id) }}" method="POST" autocomplete="off">
            @method('PUT')
            @csrf

            <div class="row g-3">

                <div class="col-md-4 col-sm-12">
                    <label for="first_name">First Name<span class="text-error">*</span></label>
                    <input type="text" required class="form-control" id="first_name" name="first_name" value="{{ old('first_name', $admin->first_name) }}">
                    @error('first_name') <div class="text-error">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-4 col-sm-12">
                    <label for="last_name">Last Name<span class="text-error">*</span></label>
                    <input type="text" required class="form-control" id="last_name" name="last_name" value="{{ old('last_name', $admin->last_name) }}">
                    @error('last_name') <div class="text-error">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-4 col-sm-12">
                    <label for="username">Username<span class="text-error">*</span></label>
                    <input type="text" required class="form-control" id="username" name="username" value="{{ old('username', $admin->username) }}">
                    @error('username') <div class="text-error">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-4 col-sm-12">
                    <label for="email">Email<span class="text-error">*</span></label>
                    <input type="email" required class="form-control" id="email" name="email" value="{{ old('email', $admin->email) }}">
                    @error('email') <div class="text-error">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-4 col-sm-12">
                    <label for="mobile_number">Mobile Number</label>
                    <input type="text" class="form-control" id="mobile_number" name="mobile_number" value="{{ old('mobile_number', $admin->mobile_number) }}">
                    @error('mobile_number') <div class="text-error">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-4 col-sm-12">
                    <label for="roles">Assign Role<span class="text-error">*</span></label>
                    <select name="roles[]" required id="roles" class="form-select" multiple>
                        @foreach ($roles as $role)
                            <option value="{{ $role->name }}" {{ in_array($role->name, $assignedRoles) ? 'selected' : '' }}>{{ $role->name }}</option>
                        @endforeach
                    </select>
                    @error('roles') <div class="text-error">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-4 col-sm-12">
                    <label for="password">Password</label>
                    <input type="password" class="form-control" id="password" name="password" placeholder="Leave blank to keep current password">
                    @error('password') <div class="text-error">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-4 col-sm-12">
                    <label for="password_confirmation">Confirm Password</label>
                    <input type="password" class="form-control" id="password_confirmation" name="password_confirmation">
                </div>

                <div class="col-md-4 col-sm-12">
                    <label for="status">Status</label>
                    <select class="form-select" id="status" name="status">
                        <option value="1" {{ $admin->status ? 'selected' : '' }}>Active</option>
                        <option value="0" {{ ! $admin->status ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>

                <div class="col-md-4 col-sm-12">
                    <label for="login">Login Allowed</label>
                    <select class="form-select" id="login" name="login">
                        <option value="1" {{ $admin->login ? 'selected' : '' }}>Active</option>
                        <option value="0" {{ ! $admin->login ? 'selected' : '' }}>Blocked</option>
                    </select>
                </div>

                <div class="col-md-4 col-sm-12">
                    <label for="email_notifications">Email Notifications</label>
                    <select class="form-select" id="email_notifications" name="email_notifications">
                        <option value="1" {{ $admin->email_notifications ? 'selected' : '' }}>Enabled</option>
                        <option value="0" {{ ! $admin->email_notifications ? 'selected' : '' }}>Disabled</option>
                    </select>
                    <div class="form-text">Includes the daily low-stock / overdue-balance / pending-approval digest.</div>
                </div>
            </div>

            <div class="row mt-4">
                <div class="text-center col-md-12">
                    <button type="submit" class="btn btn-add text-white pr-4 pl-4" id="submitForm">
                        <i class="bi bi-save"></i> Update
                    </button>
                    <a href="{{ route('admin.admin.index') }}" class="btn btn-outline-secondary pr-4 pl-4">
                        <i class="bi bi-arrow-left"></i> Back
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

@endsection
