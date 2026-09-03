@extends('backend.layouts.master')

@section('title')
Spare Parts - Vehicle Spare Parts Inventory
@endsection

@section('admin-content')

<div class="page-title-area">
    <div class="row align-items-center">
        <div class="col-md-7">
            <h4 class="page-title mb-1">Spare Parts</h4>
            <ul class="breadcrumbs">
                <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li><span>Spare Parts</span></li>
            </ul>
        </div>
        <div class="col-md-5 text-md-end mt-2 mt-md-0">
            <a href="{{ route('catalogue.index') }}" target="_blank" class="btn btn-outline-secondary"><i class="bi bi-globe"></i> View Public Catalogue</a>
            <button type="button" id="printSelectedBtn" class="btn btn-outline-secondary" disabled><i class="bi bi-upc-scan"></i> Print Selected Barcodes</button>
            @can('spare-part.import')
            <a href="{{ route('admin.spare-parts.import.create') }}" class="btn btn-outline-secondary">
                <i class="bi bi-upload"></i> Import
            </a>
            @endcan
            @can('spare-part.create')
            <a href="{{ route('admin.spare-parts.create') }}" class="btn btn-add text-white">
                <i class="bi bi-plus-lg"></i> Add Spare Part
            </a>
            @endcan
        </div>
    </div>
</div>

{{-- Multi-parameter search (Section 8) --}}
<div class="card mb-3">
    <div class="card-body">
        <form method="GET" action="{{ route('admin.spare-parts.index') }}" class="row g-2 align-items-end">
            <div class="col-md-4">
                <label class="small mb-1">Search</label>
                <input type="text" name="q" class="form-control" placeholder="Name, part number, SKU, barcode, OEM, vehicle…" value="{{ request('q') }}">
            </div>
            <div class="col-md-3">
                <label class="small mb-1">Category</label>
                <select name="category_id" class="form-select">
                    <option value="">All Categories</option>
                    @foreach ($categories as $cat)
                        <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="small mb-1">Brand</label>
                <select name="brand_id" class="form-select">
                    <option value="">All Brands</option>
                    @foreach ($brands as $brand)
                        <option value="{{ $brand->id }}" {{ request('brand_id') == $brand->id ? 'selected' : '' }}>{{ $brand->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="small mb-1">Stock</label>
                <select name="stock" class="form-select">
                    <option value="">Any</option>
                    <option value="in" {{ request('stock') == 'in' ? 'selected' : '' }}>In Stock</option>
                    <option value="low" {{ request('stock') == 'low' ? 'selected' : '' }}>Low Stock</option>
                    <option value="out" {{ request('stock') == 'out' ? 'selected' : '' }}>Out of Stock</option>
                </select>
            </div>
            <div class="col-12 mt-2">
                <button type="submit" class="btn btn-add text-white btn-sm"><i class="bi bi-search"></i> Search</button>
                <a href="{{ route('admin.spare-parts.index') }}" class="btn btn-outline-secondary btn-sm">Clear</a>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-striped align-middle">
                <thead>
                    <tr>
                        <th width="2%"><input type="checkbox" id="selectAllParts"></th>
                        <th>Part</th>
                        <th>Part No. / SKU</th>
                        {{-- <th>Vehicle</th>
                        <th>Category</th>
                        <th>Brand</th> --}}
                        <th>Purchase Price</th>
                        <th>Retail Price</th>
                        <th>Stock</th>
                        <th>Stock Value</th>
                        <th>Status</th>
                        <th width="10%">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($spareParts as $part)
                    <tr>
                        <td><input type="checkbox" class="part-check" value="{{ $part->id }}"></td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                @if ($part->main_image)
                                    <img src="{{ asset('storage/app/public/'.$part->main_image) }}" class="rounded" width="36" height="36" style="object-fit:cover;">
                                @else
                                    <span class="vsp-avatar" style="width:36px;height:36px;"><i class="bi bi-gear-wide-connected"></i></span>
                                @endif
                                <div>
                                    <div class="fw-medium">{{ $part->name }}</div>
                                    <div class="small text-muted">{{ $part->oem_number ?? '—' }}</div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div>{{ $part->part_number }}</div>
                            <div class="small text-muted">{{ $part->sku }}</div>
                        </td>
                        {{-- <td>
                            @forelse ($part->vehicles as $variant)
                                <span class="badge bg-light text-dark border mb-1">{{ $variant->label }}</span>
                            @empty
                                <span class="text-muted small">—</span>
                            @endforelse
                        </td>
                        <td>{{ $part->category->name ?? '-' }}</td>
                        <td>{{ $part->brand->name ?? '-' }}</td> --}}
                        <td>₹{{ number_format($part->purchase_price, 2) }}</td>
                        <td>₹{{ number_format($part->retail_price, 2) }}</td>
                        <td>
                            @if ($part->current_stock <= 0)
                                <span class="badge bg-danger">⚠ OUT OF STOCK</span>
                            @elseif ($part->current_stock <= $part->minimum_stock)
                                <span class="badge" style="background:var(--vsp-warning)">⚠ LOW STOCK ({{ $part->current_stock }})</span>
                            @else
                                <span class="badge bg-success">{{ $part->current_stock }} {{ $part->unit->short_code ?? '' }}</span>
                            @endif
                            @if ($part->minimum_stock > 0)
                                <div class="small text-muted mt-1">Min: {{ $part->minimum_stock }}</div>
                            @endif
                        </td>
                        <td>₹{{ number_format($part->current_stock * $part->purchase_price, 2) }}</td>
                        <td>
                            @if ($part->status === 'active') <span class="badge bg-success">Active</span>
                            @elseif ($part->status === 'inactive') <span class="badge bg-secondary">Inactive</span>
                            @else <span class="badge bg-danger">Discontinued</span> @endif
                        </td>
                        <td>
                            <div class="dropdown">
                                <button class="btn btn-secondary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">&#x22EE;</button>
                                <ul class="dropdown-menu">
                                    @can('spare-part.edit')
                                    <li><a class="dropdown-item" href="{{ route('admin.spare-parts.edit', $part->id) }}"><i class="bi bi-pencil me-1"></i> Edit</a></li>
                                    @endcan
                                    <li><a class="dropdown-item" href="{{ route('admin.spare-parts.barcode', $part->id) }}" target="_blank"><i class="bi bi-upc-scan me-1"></i> Print Barcode</a></li>
                                    @can('spare-part.create')
                                    <li><a class="dropdown-item" href="{{ route('admin.spare-parts.duplicate', $part->id) }}"><i class="bi bi-copy me-1"></i> Clone</a></li>
                                    @endcan
                                    @can('spare-part.delete')
                                    <li>
                                        <a class="dropdown-item text-danger" href="#" onclick="event.preventDefault(); document.getElementById('del-{{ $part->id }}').submit();"><i class="bi bi-trash me-1"></i> Delete</a>
                                        <form id="del-{{ $part->id }}" action="{{ route('admin.spare-parts.destroy', $part->id) }}" method="POST" class="d-none">@csrf @method('DELETE')</form>
                                    </li>
                                    @endcan
                                </ul>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="12" class="text-center text-muted py-4">No spare parts found. Try adjusting your search or <a href="{{ route('admin.spare-parts.create') }}">add a new one</a>.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
       <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mt-3 gap-2">
            <div class="text-muted small">
                Showing {{ $spareParts->firstItem() ?? 0 }}
                to {{ $spareParts->lastItem() ?? 0 }}
                of {{ $spareParts->total() }} results
            </div>

            <div>
                {{ $spareParts->withQueryString()->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    function updatePrintButton() {
        const checked = document.querySelectorAll('.part-check:checked').length;
        const btn = document.getElementById('printSelectedBtn');
        btn.disabled = checked === 0;
        btn.textContent = checked ? `🖨 Print ${checked} Barcode(s)` : 'Print Selected Barcodes';
    }

    document.getElementById('selectAllParts')?.addEventListener('change', function () {
        document.querySelectorAll('.part-check').forEach(cb => cb.checked = this.checked);
        updatePrintButton();
    });

    document.querySelectorAll('.part-check').forEach(cb => cb.addEventListener('change', updatePrintButton));

    document.getElementById('printSelectedBtn')?.addEventListener('click', function () {
        const ids = Array.from(document.querySelectorAll('.part-check:checked')).map(cb => cb.value);
        if (! ids.length) return;
        const params = ids.map(id => `ids[]=${id}`).join('&');
        window.open(`{{ route('admin.spare-parts.barcodes.bulk') }}?${params}`, '_blank');
    });
</script>
@endsection
