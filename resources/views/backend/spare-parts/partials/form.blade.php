@php
    $sparePart = $sparePart ?? ($cloneFrom ?? null);
    $isEdit = isset($sparePart) && $sparePart->exists;
    $existingVehicles = $isEdit ? $sparePart->vehicles : collect();
@endphp

<ul class="nav nav-tabs" id="sparePartTabs" role="tablist">
    <li class="nav-item"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tab-basic" type="button"><i class="bi bi-info-circle me-1"></i>Basic Info</button></li>
    <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-vehicles" type="button"><i class="bi bi-car-front me-1"></i>Vehicle Compatibility</button></li>
    <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-pricing" type="button"><i class="bi bi-currency-rupee me-1"></i>Pricing</button></li>
    <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-inventory" type="button"><i class="bi bi-box-seam me-1"></i>Inventory</button></li>
    <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-media" type="button"><i class="bi bi-image me-1"></i>Media</button></li>
    <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-seo" type="button"><i class="bi bi-search me-1"></i>SEO</button></li>
</ul>

<div class="tab-content pt-4">

    {{-- ============ TAB 1: BASIC INFORMATION ============ --}}
    <div class="tab-pane fade show active" id="tab-basic">
        <div class="row g-3">
            <div class="col-md-4">
                <label>Part Number <span class="small text-muted">(auto-generated if left blank)</span></label>
                <input type="text" name="part_number" id="part_number" class="form-control" data-dup-field="part_number"
                       value="{{ old('part_number', $sparePart->part_number ?? '') }}" placeholder="Auto-generated">
                <div class="dup-warning small text-danger mt-1 d-none"></div>
                @error('part_number') <div class="text-error">{{ $message }}</div> @enderror
            </div>
            <div class="col-md-4">
                <label>SKU <span class="small text-muted">(auto-generated if left blank)</span></label>
                <input type="text" name="sku" id="sku" class="form-control" data-dup-field="sku"
                       value="{{ old('sku', $sparePart->sku ?? '') }}" placeholder="Auto-generated">
                <div class="dup-warning small text-danger mt-1 d-none"></div>
            </div>
            <div class="col-md-4">
                <label>Barcode</label>
                <div class="input-group">
                    <input type="text" name="barcode" id="barcode" class="form-control" data-dup-field="barcode"
                           value="{{ old('barcode', $sparePart->barcode ?? '') }}" placeholder="Scan or enter barcode">
                    <button type="button" class="btn btn-outline-secondary" title="Scan with camera (browser barcode API)"><i class="bi bi-upc-scan"></i></button>
                    @if ($isEdit)
                        <a href="{{ route('admin.spare-parts.barcode', $sparePart->id) }}" target="_blank" class="btn btn-outline-secondary" title="Print Barcode Label"><i class="bi bi-printer"></i></a>
                    @endif
                </div>
                <div class="dup-warning small text-danger mt-1 d-none"></div>
            </div>

            <div class="col-md-6">
                <label>Part Name<span class="text-error">*</span></label>
                <input type="text" name="name" required class="form-control" value="{{ old('name', $sparePart->name ?? '') }}" placeholder="e.g. Front Brake Pad">
                @error('name') <div class="text-error">{{ $message }}</div> @enderror
            </div>
            <div class="col-md-3">
                <label>OEM Number</label>
                <input type="text" name="oem_number" class="form-control" data-dup-field="oem_number" value="{{ old('oem_number', $sparePart->oem_number ?? '') }}">
                <div class="dup-warning small text-danger mt-1 d-none"></div>
            </div>
            <div class="col-md-3">
                <label>Alternate Part Number</label>
                <input type="text" name="alternate_number" class="form-control" value="{{ old('alternate_number', $sparePart->alternate_number ?? '') }}">
            </div>

            <div class="col-12">
                <label>Short Description</label>
                <input type="text" name="short_description" maxlength="255" class="form-control" value="{{ old('short_description', $sparePart->short_description ?? '') }}">
            </div>
            <div class="col-12">
                <label>Detailed Description</label>
                <textarea name="description" rows="3" class="form-control">{{ old('description', $sparePart->description ?? '') }}</textarea>
            </div>

            <div class="col-md-3">
                <label class="d-flex justify-content-between">Category <a href="#" data-bs-toggle="modal" data-bs-target="#quickAddCategory" class="small">+ Quick Add</a></label>
                <select name="category_id" id="category_id" class="form-select select2">
                    <option value="">Select Category</option>
                    @foreach ($categories->whereNull('parent_id') as $cat)
                        <option value="{{ $cat->id }}" {{ old('category_id', $sparePart->category_id ?? '') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label>Sub Category</label>
                <select name="sub_category_id" class="form-select select2">
                    <option value="">Select Sub Category</option>
                    @foreach ($categories->whereNotNull('parent_id') as $cat)
                        <option value="{{ $cat->id }}" {{ old('sub_category_id', $sparePart->sub_category_id ?? '') == $cat->id ? 'selected' : '' }}>{{ $cat->name }} ({{ $cat->parent->name ?? '' }})</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="d-flex justify-content-between">Brand <a href="#" data-bs-toggle="modal" data-bs-target="#quickAddBrand" class="small">+ Quick Add</a></label>
                <select name="brand_id" id="brand_id" class="form-select select2">
                    <option value="">Select Brand</option>
                    @foreach ($brands as $brand)
                        <option value="{{ $brand->id }}" {{ old('brand_id', $sparePart->brand_id ?? '') == $brand->id ? 'selected' : '' }}>{{ $brand->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label>Manufacturer</label>
                <select name="manufacturer_id" class="form-select select2">
                    <option value="">Select Manufacturer</option>
                    @foreach ($manufacturers as $mfr)
                        <option value="{{ $mfr->id }}" {{ old('manufacturer_id', $sparePart->manufacturer_id ?? '') == $mfr->id ? 'selected' : '' }}>{{ $mfr->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-3">
                <label>Unit</label>
                <select name="unit_id" class="form-select select2">
                    <option value="">Select Unit</option>
                    @foreach ($units as $unit)
                        <option value="{{ $unit->id }}" {{ old('unit_id', $sparePart->unit_id ?? '') == $unit->id ? 'selected' : '' }}>{{ $unit->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label>Part Type</label>
                <input type="text" name="part_type" class="form-control" value="{{ old('part_type', $sparePart->part_type ?? '') }}" placeholder="e.g. OEM, Aftermarket">
            </div>
            <div class="col-md-3">
                <label>Status</label>
                <select name="status" class="form-select">
                    <option value="active" {{ old('status', $sparePart->status ?? 'active') == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ old('status', $sparePart->status ?? '') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    <option value="discontinued" {{ old('status', $sparePart->status ?? '') == 'discontinued' ? 'selected' : '' }}>Discontinued</option>
                </select>
            </div>
        </div>
    </div>

    {{-- ============ TAB 2: VEHICLE COMPATIBILITY ============ --}}
    <div class="tab-pane fade" id="tab-vehicles">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <p class="text-muted small mb-0">Search and add every vehicle (make, model, variant, year range) this part fits. A part can be compatible with many vehicles.</p>
            <div>
                <a href="#" data-bs-toggle="modal" data-bs-target="#quickAddVehicle" class="small">+ Quick Add Vehicle</a>
            </div>
        </div>

        <table class="table table-bordered align-middle" id="compatibilityTable">
            <thead>
                <tr>
                    <th>Vehicle (Make / Model / Variant)</th>
                    <th width="18%">Position</th>
                    <th width="18%">OEM Number</th>
                    <th width="20%">Notes</th>
                    <th width="5%"></th>
                </tr>
            </thead>
            <tbody id="compatibilityRows">
                @forelse ($existingVehicles as $ev)
                <tr>
                    <td>
                        <select name="vehicle_variant_id[]" class="form-select vehicle-select">
                            <option value="{{ $ev->id }}" selected>{{ $ev->label }}</option>
                        </select>
                    </td>
                    <td>
                        <select name="position[]" class="form-select">
                            @foreach (['Universal','Front','Rear','Left','Right','Front Left','Front Right','Rear Left','Rear Right'] as $pos)
                                <option value="{{ $pos }}" {{ $ev->pivot->position == $pos ? 'selected' : '' }}>{{ $pos }}</option>
                            @endforeach
                        </select>
                    </td>
                    <td><input type="text" name="compat_oem_number[]" class="form-control" value="{{ $ev->pivot->oem_number }}"></td>
                    <td><input type="text" name="compat_notes[]" class="form-control" value="{{ $ev->pivot->notes }}"></td>
                    <td><button type="button" class="btn btn-sm btn-outline-danger remove-row"><i class="bi bi-trash"></i></button></td>
                </tr>
                @empty
                @endforelse
            </tbody>
        </table>

        <button type="button" id="addCompatRow" class="btn btn-outline-secondary btn-sm"><i class="bi bi-plus-lg"></i> Add Vehicle Row</button>

        {{-- Empty-state template row, cloned by JS --}}
        <template id="compatRowTemplate">
            <tr>
                <td>
                    <select name="vehicle_variant_id[]" class="form-select vehicle-select">
                        <option value="">Type to search vehicle…</option>
                    </select>
                </td>
                <td>
                    <select name="position[]" class="form-select">
                        @foreach (['Universal','Front','Rear','Left','Right','Front Left','Front Right','Rear Left','Rear Right'] as $pos)
                            <option value="{{ $pos }}">{{ $pos }}</option>
                        @endforeach
                    </select>
                </td>
                <td><input type="text" name="compat_oem_number[]" class="form-control"></td>
                <td><input type="text" name="compat_notes[]" class="form-control"></td>
                <td><button type="button" class="btn btn-sm btn-outline-danger remove-row"><i class="bi bi-trash"></i></button></td>
            </tr>
        </template>
    </div>

    {{-- ============ TAB 3: PRICING ============ --}}
    <div class="tab-pane fade" id="tab-pricing">
        <div class="row g-3">
            <div class="col-md-3">
                <label>Purchase Price</label>
                <div class="input-group"><span class="input-group-text">₹</span>
                    <input type="number" step="0.01" min="0" name="purchase_price" id="purchase_price" class="form-control" value="{{ old('purchase_price', $sparePart->purchase_price ?? 0) }}">
                </div>
            </div>
            <div class="col-md-3">
                <label>Wholesale Price</label>
                <div class="input-group"><span class="input-group-text">₹</span>
                    <input type="number" step="0.01" min="0" name="wholesale_price" class="form-control" value="{{ old('wholesale_price', $sparePart->wholesale_price ?? 0) }}">
                </div>
            </div>
            <div class="col-md-3">
                <label>Retail Price</label>
                <div class="input-group"><span class="input-group-text">₹</span>
                    <input type="number" step="0.01" min="0" name="retail_price" id="retail_price" class="form-control" value="{{ old('retail_price', $sparePart->retail_price ?? 0) }}">
                </div>
            </div>
            <div class="col-md-3">
                <label>Minimum Selling Price</label>
                <div class="input-group"><span class="input-group-text">₹</span>
                    <input type="number" step="0.01" min="0" name="min_selling_price" class="form-control" value="{{ old('min_selling_price', $sparePart->min_selling_price ?? 0) }}">
                </div>
            </div>
            <div class="col-md-3">
                <label>Maximum Selling Price</label>
                <div class="input-group"><span class="input-group-text">₹</span>
                    <input type="number" step="0.01" min="0" name="max_selling_price" class="form-control" value="{{ old('max_selling_price', $sparePart->max_selling_price ?? '') }}">
                </div>
            </div>
            <div class="col-md-3">
                <label>Tax / VAT %</label>
                <input type="number" step="0.01" min="0" max="100" name="tax_percentage" class="form-control" value="{{ old('tax_percentage', $sparePart->tax_percentage ?? 0) }}">
            </div>
            <div class="col-md-3">
                <label>Discount %</label>
                <input type="number" step="0.01" min="0" max="100" name="discount_percentage" class="form-control" value="{{ old('discount_percentage', $sparePart->discount_percentage ?? 0) }}">
            </div>
            <div class="col-md-3">
                <label>Profit Margin</label>
                <input type="text" id="profitMarginDisplay" class="form-control" disabled placeholder="Calculated automatically">
            </div>
        </div>
    </div>

    {{-- ============ TAB 4: INVENTORY ============ --}}
    <div class="tab-pane fade" id="tab-inventory">
        <div class="row g-3">
            <div class="col-md-3">
                <label>Opening Stock</label>
                <input type="number" min="0" name="opening_stock" class="form-control" value="{{ old('opening_stock', $sparePart->opening_stock ?? 0) }}" {{ $isEdit ? 'readonly' : '' }}>
                @if ($isEdit)
                    <div class="form-text">Opening stock is locked after creation — use Stock Adjustment (Phase 3) to correct quantities.</div>
                @endif
            </div>
            <div class="col-md-3">
                <label>Minimum Stock</label>
                <input type="number" min="0" name="minimum_stock" class="form-control" value="{{ old('minimum_stock', $sparePart->minimum_stock ?? ($defaultMinimumStock ?? 0)) }}">
                <div class="form-text">Below this, the part shows as "Low Stock" on Current Stock, the Low Stock page, and in notifications.</div>
            </div>
            @if ($isEdit)
            <div class="col-md-3">
                <label>Current Stock</label>
                <div class="form-control-plaintext fw-medium pt-2">
                    {{ $sparePart->current_stock }} {{ $sparePart->unit->short_code ?? '' }}
                    @if ($sparePart->current_stock <= 0)
                        <span class="badge bg-danger ms-1">⚠ OUT OF STOCK</span>
                    @elseif ($sparePart->current_stock <= $sparePart->minimum_stock)
                        <span class="badge ms-1" style="background:var(--vsp-warning)">⚠ LOW STOCK</span>
                    @else
                        <span class="badge bg-success ms-1">✓ In Stock</span>
                    @endif
                </div>
                <div class="form-text">Read-only here — use Stock Adjustment/Transfer/Purchase to change it.</div>
            </div>
            @endif
            <div class="col-md-3">
                <label>Maximum Stock</label>
                <input type="number" min="0" name="maximum_stock" class="form-control" value="{{ old('maximum_stock', $sparePart->maximum_stock ?? '') }}">
            </div>
            <div class="col-md-3">
                <label>Reorder Level</label>
                <input type="number" min="0" name="reorder_level" class="form-control" value="{{ old('reorder_level', $sparePart->reorder_level ?? 0) }}">
            </div>

            <div class="col-12"><hr></div>

            <div class="col-md-3">
                <label>Warehouse</label>
                <select name="warehouse_id" id="warehouse_id" class="form-select select2">
                    <option value="">Select Warehouse</option>
                    @foreach ($warehouses as $wh)
                        <option value="{{ $wh->id }}" {{ old('warehouse_id', $sparePart->warehouse_id ?? '') == $wh->id ? 'selected' : '' }}>{{ $wh->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label>Rack</label>
                <select name="rack_id" id="rack_id" class="form-select" {{ ($sparePart->warehouse_id ?? old('warehouse_id')) ? '' : 'disabled' }}>
                    <option value="">Select Warehouse first</option>
                    @if ($isEdit && $sparePart->warehouse_id)
                        @foreach (\App\Models\Rack::where('warehouse_id', $sparePart->warehouse_id)->get() as $rack)
                            <option value="{{ $rack->id }}" {{ $sparePart->rack_id == $rack->id ? 'selected' : '' }}>{{ $rack->name }}</option>
                        @endforeach
                    @endif
                </select>
            </div>
            <div class="col-md-3">
                <label>Shelf</label>
                <select name="shelf_id" id="shelf_id" class="form-select" {{ ($sparePart->rack_id ?? '') ? '' : 'disabled' }}>
                    <option value="">Select Rack first</option>
                    @if ($isEdit && $sparePart->rack_id)
                        @foreach (\App\Models\Shelf::where('rack_id', $sparePart->rack_id)->get() as $shelf)
                            <option value="{{ $shelf->id }}" {{ $sparePart->shelf_id == $shelf->id ? 'selected' : '' }}>{{ $shelf->name }}</option>
                        @endforeach
                    @endif
                </select>
            </div>
            <div class="col-md-3">
                <label>Bin</label>
                <select name="bin_id" id="bin_id" class="form-select" {{ ($sparePart->shelf_id ?? '') ? '' : 'disabled' }}>
                    <option value="">Select Shelf first</option>
                    @if ($isEdit && $sparePart->shelf_id)
                        @foreach (\App\Models\Bin::where('shelf_id', $sparePart->shelf_id)->get() as $bin)
                            <option value="{{ $bin->id }}" {{ $sparePart->bin_id == $bin->id ? 'selected' : '' }}>{{ $bin->name }}</option>
                        @endforeach
                    @endif
                </select>
            </div>

            @if ($isEdit)
            <div class="col-12">
                <div class="alert alert-light border small text-muted mb-0">
                    Current Stock: <strong>{{ $sparePart->current_stock }}</strong> &middot;
                    Reserved: <strong>{{ $sparePart->reserved_stock }}</strong> &middot;
                    Damaged: <strong>{{ $sparePart->damaged_stock }}</strong>
                    — these move to <code>stock_movements</code> in Phase 3.
                </div>
            </div>
            @endif
        </div>
    </div>

    {{-- ============ TAB 5: MEDIA ============ --}}
    <div class="tab-pane fade" id="tab-media">
        <div class="row g-3">
            <div class="col-md-4">
                <label>Main Image</label>
                <input type="file" name="main_image" accept="image/*" class="form-control">
                @if ($isEdit && $sparePart->main_image)
                    <img src="{{ asset('storage/'.$sparePart->main_image) }}" class="mt-2 rounded border" width="120">
                @endif
            </div>
            <div class="col-md-8">
                <label>Additional Images</label>
                <input type="file" name="images[]" accept="image/*" multiple class="form-control">
                @if ($isEdit && $sparePart->images->count())
                    <div class="d-flex gap-2 mt-2 flex-wrap">
                        @foreach ($sparePart->images as $img)
                            <img src="{{ asset('storage/'.$img->path) }}" class="rounded border" width="80" height="80" style="object-fit:cover;">
                        @endforeach
                    </div>
                @endif
            </div>
            <div class="col-12">
                <label>Product Documents</label>
                <input type="file" name="documents[]" accept=".pdf,.doc,.docx" multiple class="form-control">
                <div class="form-text">Spec sheets, fitment guides, certificates (PDF/DOC).</div>
            </div>
        </div>
    </div>

    {{-- ============ TAB 6: SEO / ADDITIONAL INFO ============ --}}
    <div class="tab-pane fade" id="tab-seo">
        <div class="row g-3">
            <div class="col-12">
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" role="switch" id="is_published" name="is_published" value="1"
                           {{ old('is_published', $sparePart->is_published ?? false) ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_published">
                        <strong>Publish to Public Catalogue</strong>
                        @if ($isEdit && ($sparePart->is_published ?? false))
                            — <a href="{{ route('catalogue.show', $sparePart->slug) }}" target="_blank">View live page</a>
                        @endif
                    </label>
                    <div class="form-text">Off by default — only parts you explicitly publish appear on the public site.</div>
                </div>
            </div>
            <div class="col-md-6">
                <label>Slug</label>
                <input type="text" name="slug" class="form-control" value="{{ old('slug', $sparePart->slug ?? '') }}" placeholder="Auto-generated from part name">
            </div>
            <div class="col-md-6">
                <label>SEO Title</label>
                <input type="text" name="seo_title" class="form-control" value="{{ old('seo_title', $sparePart->seo_title ?? '') }}">
            </div>
            <div class="col-12">
                <label>SEO Description</label>
                <textarea name="seo_description" rows="2" class="form-control">{{ old('seo_description', $sparePart->seo_description ?? '') }}</textarea>
            </div>
            <div class="col-12">
                <label>Keywords</label>
                <input type="text" name="keywords" class="form-control" value="{{ old('keywords', $sparePart->keywords ?? '') }}" placeholder="Comma-separated">
            </div>
        </div>
        <div class="alert alert-light border small text-muted mt-3 mb-0">
            Powers the public catalogue page when "Publish to Public Catalogue" above is on.
        </div>
    </div>

</div>

{{-- ============ Quick Add Modals ============ --}}
<div class="modal fade" id="quickAddCategory" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header"><h6 class="modal-title">Quick Add Category</h6><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <div class="modal-body">
                <label>Name<span class="text-error">*</span></label>
                <input type="text" id="quickCategoryName" class="form-control" placeholder="e.g. Cooling System">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" id="quickAddCategoryBtn" class="btn btn-add text-white">Add &amp; Select</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="quickAddBrand" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header"><h6 class="modal-title">Quick Add Brand</h6><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <div class="modal-body">
                <label>Name<span class="text-error">*</span></label>
                <input type="text" id="quickBrandName" class="form-control" placeholder="e.g. Bosch">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" id="quickAddBrandBtn" class="btn btn-add text-white">Add &amp; Select</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="quickAddVehicle" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header"><h6 class="modal-title">Quick Add Vehicle Variant</h6><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <div class="modal-body">
                <p class="small text-muted">For makes/models/variants that don't exist yet. Full management is under <a href="{{ route('admin.vehicles.index') }}" target="_blank">Vehicle Management</a>.</p>
                <div class="mb-2">
                    <label>Make</label>
                    <select id="quickVehicleMake" class="form-select">
                        <option value="">Select Make</option>
                        @foreach ($vehicleMakes as $make)
                            <option value="{{ $make->id }}">{{ $make->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-2">
                    <label>Model</label>
                    <select id="quickVehicleModel" class="form-select" disabled>
                        <option value="">Select Make first</option>
                    </select>
                </div>
                <div class="mb-2">
                    <label>Variant Name<span class="text-error">*</span></label>
                    <input type="text" id="quickVehicleVariantName" class="form-control" placeholder="e.g. 1.8L">
                </div>
                <div class="row g-2">
                    <div class="col-6"><label>Start Year</label><input type="number" id="quickVehicleStartYear" class="form-control"></div>
                    <div class="col-6"><label>End Year</label><input type="number" id="quickVehicleEndYear" class="form-control"></div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" id="quickAddVehicleBtn" class="btn btn-add text-white">Add &amp; Select</button>
            </div>
        </div>
    </div>
</div>
