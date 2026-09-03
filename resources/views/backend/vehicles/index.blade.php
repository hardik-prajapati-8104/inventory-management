@extends('backend.layouts.master')

@section('title')
Vehicle Management - Vehicle Spare Parts Inventory
@endsection

@section('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/css/select2.min.css" rel="stylesheet" />
@endsection

@section('admin-content')

<div class="page-title-area">
    <div class="row align-items-center">
        <div class="col-12">
            <h4 class="page-title mb-1">Vehicle Management</h4>
            <ul class="breadcrumbs">
                <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li><span>Vehicle Management</span></li>
            </ul>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <ul class="nav nav-tabs" role="tablist">
            <li class="nav-item"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#makes" type="button">Makes ({{ $makes->count() }})</button></li>
            <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#models" type="button">Models ({{ $models->count() }})</button></li>
            <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#variants" type="button">Variants ({{ $variants->count() }})</button></li>
        </ul>

        <div class="tab-content pt-4">

            {{-- Makes --}}
            <div class="tab-pane fade show active" id="makes">
                <div class="row">
                    <div class="col-md-4">
                        <h6 class="mb-3">Add Make</h6>
                        <form action="{{ route('admin.vehicles.makes.store') }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <input type="text" name="name" required class="form-control" placeholder="e.g. Toyota">
                            </div>
                            <button type="submit" class="btn btn-add text-white w-100"><i class="bi bi-plus-lg"></i> Add Make</button>
                        </form>
                    </div>
                    <div class="col-md-8">
                        <table class="table table-bordered table-striped">
                            <thead><tr><th width="5%">#</th><th>Make</th></tr></thead>
                            <tbody>
                                @forelse ($makes as $make)
                                    <tr><td>{{ $loop->iteration }}</td><td>{{ $make->name }}</td></tr>
                                @empty
                                    <tr><td colspan="2" class="text-center text-muted py-3">No makes yet.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- Models --}}
            <div class="tab-pane fade" id="models">
                <div class="row">
                    <div class="col-md-4">
                        <h6 class="mb-3">Add Model</h6>
                        <form action="{{ route('admin.vehicles.models.store') }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label>Make<span class="text-error">*</span></label>
                                <select name="vehicle_make_id" required class="form-select">
                                    <option value="">Select Make</option>
                                    @foreach ($makes as $make)
                                        <option value="{{ $make->id }}">{{ $make->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label>Vehicle Type</label>
                                <select name="vehicle_type_id" class="form-select">
                                    <option value="">Select Type</option>
                                    @foreach ($types as $type)
                                        <option value="{{ $type->id }}">{{ $type->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label>Model Name<span class="text-error">*</span></label>
                                <input type="text" name="name" required class="form-control" placeholder="e.g. Corolla">
                            </div>
                            <button type="submit" class="btn btn-add text-white w-100"><i class="bi bi-plus-lg"></i> Add Model</button>
                        </form>
                    </div>
                    <div class="col-md-8">
                        <table class="table table-bordered table-striped">
                            <thead><tr><th width="5%">#</th><th>Make</th><th>Model</th></tr></thead>
                            <tbody>
                                @forelse ($models as $model)
                                    <tr><td>{{ $loop->iteration }}</td><td>{{ $model->make->name ?? '-' }}</td><td>{{ $model->name }}</td></tr>
                                @empty
                                    <tr><td colspan="3" class="text-center text-muted py-3">No models yet.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- Variants --}}
            <div class="tab-pane fade" id="variants">
                <div class="row">
                    <div class="col-md-4">
                        <h6 class="mb-3">Add Variant</h6>
                        <form action="{{ route('admin.vehicles.variants.store') }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label>Make<span class="text-error">*</span></label>
                                <select id="variantMake" required class="form-select">
                                    <option value="">Select Make</option>
                                    @foreach ($makes as $make)
                                        <option value="{{ $make->id }}">{{ $make->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label>Model<span class="text-error">*</span></label>
                                <select name="vehicle_model_id" id="variantModel" required class="form-select" disabled>
                                    <option value="">Select Make first</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label>Variant Name<span class="text-error">*</span></label>
                                <input type="text" name="name" required class="form-control" placeholder="e.g. 1.8L">
                            </div>
                            <div class="row g-2">
                                <div class="col-6 mb-3">
                                    <label>Start Year</label>
                                    <input type="number" name="start_year" class="form-control" placeholder="2018">
                                </div>
                                <div class="col-6 mb-3">
                                    <label>End Year</label>
                                    <input type="number" name="end_year" class="form-control" placeholder="2022">
                                </div>
                            </div>
                            <div class="mb-3">
                                <label>Fuel Type</label>
                                <select name="fuel_type" class="form-select">
                                    <option value="">-</option>
                                    @foreach (['Petrol','Diesel','Hybrid','Electric','CNG','LPG'] as $f)
                                        <option value="{{ $f }}">{{ $f }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label>Transmission</label>
                                <select name="transmission" class="form-select">
                                    <option value="">-</option>
                                    @foreach (['Manual','Automatic','CVT'] as $t)
                                        <option value="{{ $t }}">{{ $t }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <button type="submit" class="btn btn-add text-white w-100"><i class="bi bi-plus-lg"></i> Add Variant</button>
                        </form>
                    </div>
                    <div class="col-md-8">
                        <table class="table table-bordered table-striped">
                            <thead><tr><th width="5%">#</th><th>Make</th><th>Model</th><th>Variant</th><th>Years</th><th>Fuel</th><th width="8%"></th></tr></thead>
                            <tbody>
                                @forelse ($variants as $v)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $v->model_->make->name ?? '-' }}</td>
                                    <td>{{ $v->model_->name ?? '-' }}</td>
                                    <td>{{ $v->name }}</td>
                                    <td>{{ $v->start_year }}{{ $v->end_year ? '-'.$v->end_year : ($v->start_year ? '+' : '') }}</td>
                                    <td>{{ $v->fuel_type ?? '-' }}</td>
                                    <td>
                                        <form action="{{ route('admin.vehicles.variants.destroy', $v->id) }}" method="POST" onsubmit="return confirm('Delete this variant?');">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr><td colspan="7" class="text-center text-muted py-3">No variants yet.</td></tr>
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

@section('scripts')
<script>
    // Cascading Make -> Model dropdown, shared logic with the Spare Part
    // compatibility tab (see spare-parts/partials/tab-vehicles.blade.php).
    document.getElementById('variantMake')?.addEventListener('change', function () {
        const modelSelect = document.getElementById('variantModel');
        const makeId = this.value;

        modelSelect.innerHTML = '<option value="">Loading…</option>';
        modelSelect.disabled = true;

        if (! makeId) {
            modelSelect.innerHTML = '<option value="">Select Make first</option>';
            return;
        }

        fetch(`{{ url('admin/vehicles/makes') }}/${makeId}/models`)
            .then(res => res.json())
            .then(models => {
                modelSelect.innerHTML = '<option value="">Select Model</option>' +
                    models.map(m => `<option value="${m.id}">${m.name}</option>`).join('');
                modelSelect.disabled = false;
            });
    });
</script>
@endsection
