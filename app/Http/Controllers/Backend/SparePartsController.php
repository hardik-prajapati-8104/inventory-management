<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Bin;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Manufacturer;
use App\Models\Rack;
use App\Models\Shelf;
use App\Models\SparePart;
use App\Models\SparePartImage;
use App\Models\Unit;
use App\Models\VehicleMake;
use App\Models\VehicleType;
use App\Models\VehicleVariant;
use App\Models\Warehouse;
use App\Services\StockService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SparePartsController extends Controller
{
    public $user;
    private StockService $stockService;

    public function __construct(StockService $stockService)
    {
        $this->stockService = $stockService;

         $this->user = Auth::guard('admin')->user();
    }

    /**
     * Listing with the multi-parameter search from Section 8: name, part
     * number, SKU, barcode, OEM number, brand, manufacturer, category,
     * vehicle make/model/year, warehouse, price range, stock availability.
     */
    public function index(Request $request)
    {
        if (is_null($this->user) || ! $this->user->can('spare-part.view')) {
            abort(403, 'Sorry !! You are Unauthorized to view Spare Parts !');
        }

        $query = SparePart::with(['category', 'brand', 'manufacturer', 'unit', 'vehicles.model_.make']);

        if ($term = $request->get('q')) {
            $query->where(function ($q) use ($term) {
                $q->where('name', 'like', "%$term%")
                    ->orWhere('part_number', 'like', "%$term%")
                    ->orWhere('sku', 'like', "%$term%")
                    ->orWhere('barcode', 'like', "%$term%")
                    ->orWhere('oem_number', 'like', "%$term%")
                    ->orWhereHas('vehicles.model_.make', fn ($vq) => $vq->where('name', 'like', "%$term%"))
                    ->orWhereHas('vehicles.model_', fn ($vq) => $vq->where('name', 'like', "%$term%"));
            });
        }

        if ($categoryId = $request->get('category_id')) {
            $query->where('category_id', $categoryId);
        }

        if ($brandId = $request->get('brand_id')) {
            $query->where('brand_id', $brandId);
        }

        if ($stockFilter = $request->get('stock')) {
            match ($stockFilter) {
                'low' => $query->lowStock(),
                'out' => $query->outOfStock(),
                'in' => $query->where('current_stock', '>', 0),
                default => null,
            };
        }

        $spareParts = $query->orderByDesc('id')->paginate(25)->withQueryString();
        $categories = Category::topLevel()->orderBy('name')->get();
        $brands = Brand::orderBy('name')->get();

        return view('backend.spare-parts.index', compact('spareParts', 'categories', 'brands'));
    }

    public function create()
    {
        if (is_null($this->user) || ! $this->user->can('spare-part.create')) {
            abort(403, 'Sorry !! You are Unauthorized to create Spare Parts !');
        }

        return view('backend.spare-parts.create', $this->formData());
    }

    public function store(Request $request)
    {
        if (is_null($this->user) || ! $this->user->can('spare-part.create')) {
            abort(403, 'Sorry !! You are Unauthorized to create Spare Parts !');
        }

        $validated = $this->validateSparePart($request);

        $sparePart = new SparePart($validated);
        $sparePart->is_published = $request->boolean('is_published');
        $sparePart->part_number = $request->filled('part_number') ? $request->part_number : $this->generatePartNumber();
        $sparePart->sku = $request->filled('sku') ? $request->sku : $this->generateSku($request->category_id, $sparePart->part_number);
        $sparePart->created_by = $this->user->id;

        if ($request->hasFile('main_image')) {
            $sparePart->main_image = $request->file('main_image')->store('spare-parts', 'public');
        }

        $sparePart->save();

        $this->syncGalleryImages($request, $sparePart);
        $this->syncCompatibility($request, $sparePart);

        // Section 42 / architectural principle: opening stock is never written
        // directly to current_stock — it goes through StockService so it
        // creates the OPENING_STOCK ledger row that everything downstream
        // (reports, audits, stock take variance) reconciles against.
        if ($sparePart->opening_stock > 0) {
            $warehouse = Warehouse::find($request->warehouse_id) ?? Warehouse::default();

            if ($warehouse) {
                $this->stockService->openingStock($sparePart, $warehouse, (int) $sparePart->opening_stock);
            }
        }

        AuditLog::record(
            action: 'create',
            module: 'Spare Parts',
            subject: $sparePart,
            description: "Created spare part \"{$sparePart->name}\" ({$sparePart->part_number})",
            new: $sparePart->only(['name', 'part_number', 'sku', 'retail_price']),
        );

        session()->flash('success', $sparePart->name.' has been created !!');

        // Section 36: "Save & New" keeps the data-entry operator on the create
        // form for the next part instead of bouncing to the index.
        if ($request->input('save_action') === 'save_and_new') {
            return redirect()->route('admin.spare-parts.create')->with('success', $sparePart->name.' saved. Ready for the next part.');
        }

        return redirect()->route('admin.spare-parts.index');
    }

    public function edit(int $id)
    {
        if (is_null($this->user) || ! $this->user->can('spare-part.edit')) {
            abort(403, 'Sorry !! You are Unauthorized to edit Spare Parts !');
        }

        $sparePart = SparePart::with(['images', 'vehicles.model_.make'])->findOrFail($id);

        return view('backend.spare-parts.edit', array_merge($this->formData(), compact('sparePart')));
    }

    public function update(Request $request, int $id)
    {
        if (is_null($this->user) || ! $this->user->can('spare-part.edit')) {
            abort(403, 'Sorry !! You are Unauthorized to edit Spare Parts !');
        }

        $sparePart = SparePart::findOrFail($id);
        $original = $sparePart->only(['name', 'retail_price', 'purchase_price', 'status']);

        $validated = $this->validateSparePart($request, $sparePart->id);
        $sparePart->fill($validated);
        $sparePart->is_published = $request->boolean('is_published');

        if ($request->hasFile('main_image')) {
            if ($sparePart->main_image) {
                Storage::disk('public')->delete($sparePart->main_image);
            }
            $sparePart->main_image = $request->file('main_image')->store('spare-parts', 'public');
        }

        $sparePart->save();

        $this->syncGalleryImages($request, $sparePart);
        $this->syncCompatibility($request, $sparePart);

        $priceChanged = bccomp((string) $original['retail_price'], (string) $sparePart->retail_price, 2) !== 0;

        AuditLog::record(
            action: 'update',
            module: 'Spare Parts',
            subject: $sparePart,
            description: $priceChanged
                ? "Updated \"{$sparePart->name}\" — price ₹{$original['retail_price']} -> ₹{$sparePart->retail_price}"
                : "Updated spare part \"{$sparePart->name}\"",
            old: $original,
            new: $sparePart->only(['name', 'retail_price', 'purchase_price', 'status']),
        );

        session()->flash('success', $sparePart->name.' has been updated !!');
        return redirect()->route('admin.spare-parts.index');
    }

    public function destroy(int $id)
    {
        if (is_null($this->user) || ! $this->user->can('spare-part.delete')) {
            abort(403, 'Sorry !! You are Unauthorized to delete Spare Parts !');
        }

        $sparePart = SparePart::findOrFail($id);
        AuditLog::record('delete', 'Spare Parts', $sparePart, "Deleted spare part \"{$sparePart->name}\" ({$sparePart->part_number})");
        $sparePart->delete();

        session()->flash('success', 'Spare part has been deleted !!');
        return back();
    }

    /**
     * "Clone existing product" (Section 36) — opens the create form pre-filled
     * from an existing spare part so the operator only edits what's different.
     */
    public function duplicate(int $id)
    {
        if (is_null($this->user) || ! $this->user->can('spare-part.create')) {
            abort(403);
        }

        $source = SparePart::with('vehicles')->findOrFail($id);

        return view('backend.spare-parts.create', array_merge($this->formData(), [
            'cloneFrom' => $source,
        ]));
    }

    /**
     * AJAX duplicate-detection (Section 37): checked on blur of Part Number /
     * SKU / OEM Number / Barcode fields before the form is ever submitted.
     */
    public function checkDuplicate(Request $request)
    {
        $field = $request->get('field'); // part_number | sku | oem_number | barcode
        $value = $request->get('value');
        $excludeId = $request->get('exclude_id');

        if (! in_array($field, ['part_number', 'sku', 'oem_number', 'barcode']) || ! $value) {
            return response()->json(['exists' => false]);
        }

        $existing = SparePart::where($field, $value)
            ->when($excludeId, fn ($q) => $q->where('id', '!=', $excludeId))
            ->first();

        if (! $existing) {
            return response()->json(['exists' => false]);
        }

        return response()->json([
            'exists' => true,
            'part' => [
                'id' => $existing->id,
                'name' => $existing->name,
                'sku' => $existing->sku,
                'current_stock' => $existing->current_stock,
                'edit_url' => route('admin.spare-parts.edit', $existing->id),
            ],
        ]);
    }

    /**
     * AJAX barcode/SKU lookup used by the barcode-scan-to-find workflow
     * (Section 9) and will double as the sell/add-by-barcode entry point
     * once Sales (Phase 5) exists.
     */
    public function lookupByCode(Request $request)
    {
        $code = $request->get('code');

        $part = SparePart::where('barcode', $code)
            ->orWhere('sku', $code)
            ->orWhere('part_number', $code)
            ->first();

        if (! $part) {
            return response()->json(['found' => false]);
        }

        return response()->json(['found' => true, 'part' => $part]);
    }

    // -----------------------------------------------------------------
    // Internal helpers
    // -----------------------------------------------------------------

    private function formData(): array
    {
        return [
            'categories' => Category::orderBy('name')->get(),
            'brands' => Brand::orderBy('name')->get(),
            'manufacturers' => Manufacturer::orderBy('name')->get(),
            'units' => Unit::orderBy('name')->get(),
            'vehicleMakes' => VehicleMake::orderBy('name')->get(),
            'vehicleTypes' => VehicleType::orderBy('name')->get(),
            'warehouses' => Warehouse::where('status', 1)->orderBy('name')->get(),
            // Section: Low Stock Notification settings — pre-fills Minimum
            // Stock on the "add new part" form so a freshly added part is
            // already covered by low-stock alerts, without forcing every
            // part to be edited individually first.
            'defaultMinimumStock' => (int) \App\Models\Setting::get('inventory', 'low_stock_default_minimum', 0),
        ];
    }

    private function validateSparePart(Request $request, ?int $ignoreId = null): array
    {
        return $request->validate([
            'name' => 'required|max:180',
            'part_number' => 'nullable|max:60|unique:spare_parts,part_number,'.($ignoreId ?? 'NULL').',id',
            'sku' => 'nullable|max:60|unique:spare_parts,sku,'.($ignoreId ?? 'NULL').',id',
            'barcode' => 'nullable|max:60|unique:spare_parts,barcode,'.($ignoreId ?? 'NULL').',id',
            'oem_number' => 'nullable|max:60',
            'alternate_number' => 'nullable|max:60',
            'short_description' => 'nullable|max:255',
            'description' => 'nullable|string',

            'category_id' => 'nullable|exists:categories,id',
            'sub_category_id' => 'nullable|exists:categories,id',
            'brand_id' => 'nullable|exists:brands,id',
            'manufacturer_id' => 'nullable|exists:manufacturers,id',
            'unit_id' => 'nullable|exists:units,id',
            'part_type' => 'nullable|max:60',

            'purchase_price' => 'nullable|numeric|min:0',
            'wholesale_price' => 'nullable|numeric|min:0',
            'retail_price' => 'nullable|numeric|min:0',
            'min_selling_price' => 'nullable|numeric|min:0',
            'max_selling_price' => 'nullable|numeric|min:0',
            'tax_percentage' => 'nullable|numeric|min:0|max:100',
            'discount_percentage' => 'nullable|numeric|min:0|max:100',

            'opening_stock' => 'nullable|integer|min:0',
            'minimum_stock' => 'nullable|integer|min:0',
            'maximum_stock' => 'nullable|integer|min:0',
            'reorder_level' => 'nullable|integer|min:0',

            'warehouse_id' => 'nullable|exists:warehouses,id',
            'rack_id' => 'nullable|exists:racks,id',
            'shelf_id' => 'nullable|exists:shelves,id',
            'bin_id' => 'nullable|exists:bins,id',

            'seo_title' => 'nullable|max:180',
            'seo_description' => 'nullable|max:255',
            'keywords' => 'nullable|max:255',

            'status' => 'nullable|in:active,inactive,discontinued',
            'is_published' => 'nullable|boolean',
            'main_image' => 'nullable|image|max:4096',
        ]);
    }

    /**
     * Auto-generated Part Number: PN-YYMM-XXXXX, sequential-looking but
     * collision-safe since it's derived from the next id.
     */
    private function generatePartNumber(): string
    {
        $next = (SparePart::withTrashed()->max('id') ?? 0) + 1;
        return 'PN-'.now()->format('ym').'-'.str_pad($next, 5, '0', STR_PAD_LEFT);
    }

    /**
     * Auto-generated SKU: first 3 letters of the category + part number tail.
     */
    private function generateSku(?int $categoryId, string $partNumber): string
    {
        $prefix = 'GEN';
        if ($categoryId && $category = Category::find($categoryId)) {
            $prefix = strtoupper(Str::substr(preg_replace('/[^A-Za-z]/', '', $category->name), 0, 3)) ?: 'GEN';
        }

        return $prefix.'-'.Str::upper(Str::afterLast($partNumber, '-'));
    }

    private function syncGalleryImages(Request $request, SparePart $sparePart): void
    {
        if (! $request->hasFile('images')) {
            return;
        }

        $nextOrder = $sparePart->images()->max('sort_order') + 1;

        foreach ($request->file('images') as $i => $file) {
            $path = $file->store('spare-parts/gallery', 'public');
            SparePartImage::create([
                'spare_part_id' => $sparePart->id,
                'path' => $path,
                'sort_order' => $nextOrder + $i,
            ]);
        }
    }

    /**
     * Section 7: sync the many-to-many compatibility rows. Expects arrays
     * `vehicle_variant_id[]`, `position[]`, `compat_oem_number[]`,
     * `compat_notes[]` submitted in parallel from the repeatable
     * compatibility-row UI on Tab 2.
     */
    private function syncCompatibility(Request $request, SparePart $sparePart): void
    {
        if (! $request->has('vehicle_variant_id')) {
            return;
        }

        $variantIds = $request->input('vehicle_variant_id', []);
        $positions = $request->input('position', []);
        $oemNumbers = $request->input('compat_oem_number', []);
        $notes = $request->input('compat_notes', []);

        $sync = [];
        foreach ($variantIds as $i => $variantId) {
            if (! $variantId) {
                continue;
            }
            $sync[$variantId] = [
                'position' => $positions[$i] ?? 'Universal',
                'oem_number' => $oemNumbers[$i] ?? null,
                'notes' => $notes[$i] ?? null,
            ];
        }

        $sparePart->vehicles()->sync($sync);
    }
}
