@extends('backend.layouts.master')

@section('title')
Warehouses - Vehicle Spare Parts Inventory
@endsection

@section('admin-content')

<div class="page-title-area">
    <div class="row align-items-center">
        <div class="col-12">
            <h4 class="page-title mb-1">Warehouses</h4>
            <ul class="breadcrumbs">
                <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li><span>Warehouses</span></li>
            </ul>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <ul class="nav nav-tabs" role="tablist">
            <li class="nav-item"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#whs" type="button">Warehouses ({{ $warehouses->count() }})</button></li>
            <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#zones" type="button">Zones ({{ $zones->count() }})</button></li>
            <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#racks" type="button">Racks ({{ $racks->count() }})</button></li>
            <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#shelves" type="button">Shelves ({{ $shelves->count() }})</button></li>
            <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#bins" type="button">Bins ({{ $bins->count() }})</button></li>
        </ul>

        <div class="tab-content pt-4">

            {{-- Warehouses --}}
            <div class="tab-pane fade show active" id="whs">
                <div class="row">
                    <div class="col-md-4">
                        <h6 class="mb-3">Add Warehouse</h6>
                        <form action="{{ route('admin.warehouses.store') }}" method="POST">
                            @csrf
                            <div class="mb-2"><label>Name<span class="text-error">*</span></label><input type="text" name="name" required class="form-control" placeholder="e.g. Main Warehouse"></div>
                            <div class="mb-2"><label>Code<span class="text-error">*</span></label><input type="text" name="code" required class="form-control" placeholder="e.g. WH-A"></div>
                            <div class="mb-2"><label>Manager</label><input type="text" name="manager" class="form-control"></div>
                            <div class="mb-2"><label>Contact Number</label><input type="text" name="contact_number" class="form-control"></div>
                            <div class="mb-2"><label>City</label><input type="text" name="city" class="form-control"></div>
                            <div class="mb-3"><label>Address</label><textarea name="address" class="form-control"></textarea></div>
                            <button type="submit" class="btn btn-add text-white w-100"><i class="bi bi-plus-lg"></i> Add Warehouse</button>
                        </form>
                    </div>
                    <div class="col-md-8">
                        <table class="table table-bordered table-striped">
                            <thead><tr><th>#</th><th>Name</th><th>Code</th><th>Manager</th><th>Parts Stocked</th><th>Default</th><th>Status</th></tr></thead>
                            <tbody>
                                @forelse ($warehouses as $wh)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $wh->name }}</td>
                                    <td>{{ $wh->code }}</td>
                                    <td>{{ $wh->manager ?? '-' }}</td>
                                    <td>{{ $wh->stock_count }}</td>
                                    <td>{{ $wh->is_default ? '⭐' : '' }}</td>
                                    <td>{{ $wh->status ? 'Active' : 'Inactive' }}</td>
                                </tr>
                                @empty
                                <tr><td colspan="7" class="text-center text-muted py-3">No warehouses yet — add one to start receiving stock.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- Zones --}}
            <div class="tab-pane fade" id="zones">
                <div class="row">
                    <div class="col-md-4">
                        <h6 class="mb-3">Add Zone</h6>
                        <form action="{{ route('admin.warehouses.zones.store') }}" method="POST">
                            @csrf
                            <div class="mb-2">
                                <label>Warehouse<span class="text-error">*</span></label>
                                <select name="warehouse_id" required class="form-select">
                                    <option value="">Select Warehouse</option>
                                    @foreach ($warehouses as $wh)<option value="{{ $wh->id }}">{{ $wh->name }}</option>@endforeach
                                </select>
                            </div>
                            <div class="mb-3"><label>Zone Name<span class="text-error">*</span></label><input type="text" name="name" required class="form-control" placeholder="e.g. Engine Zone"></div>
                            <button type="submit" class="btn btn-add text-white w-100"><i class="bi bi-plus-lg"></i> Add Zone</button>
                        </form>
                    </div>
                    <div class="col-md-8">
                        <table class="table table-bordered table-striped">
                            <thead><tr><th>#</th><th>Warehouse</th><th>Zone</th></tr></thead>
                            <tbody>
                                @forelse ($zones as $z)
                                    <tr><td>{{ $loop->iteration }}</td><td>{{ $z->warehouse->name ?? '-' }}</td><td>{{ $z->name }}</td></tr>
                                @empty
                                    <tr><td colspan="3" class="text-center text-muted py-3">No zones yet.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- Racks --}}
            <div class="tab-pane fade" id="racks">
                <div class="row">
                    <div class="col-md-4">
                        <h6 class="mb-3">Add Rack</h6>
                        <form action="{{ route('admin.warehouses.racks.store') }}" method="POST">
                            @csrf
                            <div class="mb-2">
                                <label>Warehouse<span class="text-error">*</span></label>
                                <select name="warehouse_id" id="rackWarehouse" required class="form-select">
                                    <option value="">Select Warehouse</option>
                                    @foreach ($warehouses as $wh)<option value="{{ $wh->id }}">{{ $wh->name }}</option>@endforeach
                                </select>
                            </div>
                            <div class="mb-2">
                                <label>Zone (optional)</label>
                                <select name="warehouse_zone_id" class="form-select">
                                    <option value="">None</option>
                                    @foreach ($zones as $z)<option value="{{ $z->id }}">{{ $z->warehouse->name }} — {{ $z->name }}</option>@endforeach
                                </select>
                            </div>
                            <div class="mb-3"><label>Rack Name<span class="text-error">*</span></label><input type="text" name="name" required class="form-control" placeholder="e.g. A-05"></div>
                            <button type="submit" class="btn btn-add text-white w-100"><i class="bi bi-plus-lg"></i> Add Rack</button>
                        </form>
                    </div>
                    <div class="col-md-8">
                        <table class="table table-bordered table-striped">
                            <thead><tr><th>#</th><th>Warehouse</th><th>Zone</th><th>Rack</th></tr></thead>
                            <tbody>
                                @forelse ($racks as $r)
                                    <tr><td>{{ $loop->iteration }}</td><td>{{ $r->warehouse->name ?? '-' }}</td><td>{{ $r->zone->name ?? '-' }}</td><td>{{ $r->name }}</td></tr>
                                @empty
                                    <tr><td colspan="4" class="text-center text-muted py-3">No racks yet.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- Shelves --}}
            <div class="tab-pane fade" id="shelves">
                <div class="row">
                    <div class="col-md-4">
                        <h6 class="mb-3">Add Shelf</h6>
                        <form action="{{ route('admin.warehouses.shelves.store') }}" method="POST">
                            @csrf
                            <div class="mb-2">
                                <label>Rack<span class="text-error">*</span></label>
                                <select name="rack_id" required class="form-select">
                                    <option value="">Select Rack</option>
                                    @foreach ($racks as $r)<option value="{{ $r->id }}">{{ $r->warehouse->name }} — {{ $r->name }}</option>@endforeach
                                </select>
                            </div>
                            <div class="mb-3"><label>Shelf Name<span class="text-error">*</span></label><input type="text" name="name" required class="form-control" placeholder="e.g. Shelf 03"></div>
                            <button type="submit" class="btn btn-add text-white w-100"><i class="bi bi-plus-lg"></i> Add Shelf</button>
                        </form>
                    </div>
                    <div class="col-md-8">
                        <table class="table table-bordered table-striped">
                            <thead><tr><th>#</th><th>Warehouse</th><th>Rack</th><th>Shelf</th></tr></thead>
                            <tbody>
                                @forelse ($shelves as $s)
                                    <tr><td>{{ $loop->iteration }}</td><td>{{ $s->rack->warehouse->name ?? '-' }}</td><td>{{ $s->rack->name ?? '-' }}</td><td>{{ $s->name }}</td></tr>
                                @empty
                                    <tr><td colspan="4" class="text-center text-muted py-3">No shelves yet.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- Bins --}}
            <div class="tab-pane fade" id="bins">
                <div class="row">
                    <div class="col-md-4">
                        <h6 class="mb-3">Add Bin</h6>
                        <form action="{{ route('admin.warehouses.bins.store') }}" method="POST">
                            @csrf
                            <div class="mb-2">
                                <label>Shelf<span class="text-error">*</span></label>
                                <select name="shelf_id" required class="form-select">
                                    <option value="">Select Shelf</option>
                                    @foreach ($shelves as $s)<option value="{{ $s->id }}">{{ $s->rack->warehouse->name ?? '' }} — {{ $s->rack->name ?? '' }} — {{ $s->name }}</option>@endforeach
                                </select>
                            </div>
                            <div class="mb-3"><label>Bin Name<span class="text-error">*</span></label><input type="text" name="name" required class="form-control" placeholder="e.g. Bin 12"></div>
                            <button type="submit" class="btn btn-add text-white w-100"><i class="bi bi-plus-lg"></i> Add Bin</button>
                        </form>
                    </div>
                    <div class="col-md-8">
                        <table class="table table-bordered table-striped">
                            <thead><tr><th>#</th><th>Warehouse</th><th>Rack</th><th>Shelf</th><th>Bin</th></tr></thead>
                            <tbody>
                                @forelse ($bins as $b)
                                    <tr><td>{{ $loop->iteration }}</td><td>{{ $b->shelf->rack->warehouse->name ?? '-' }}</td><td>{{ $b->shelf->rack->name ?? '-' }}</td><td>{{ $b->shelf->name ?? '-' }}</td><td>{{ $b->name }}</td></tr>
                                @empty
                                    <tr><td colspan="5" class="text-center text-muted py-3">No bins yet.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

@endsection
