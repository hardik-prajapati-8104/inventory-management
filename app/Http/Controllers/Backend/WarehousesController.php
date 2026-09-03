<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Bin;
use App\Models\Rack;
use App\Models\Shelf;
use App\Models\Warehouse;
use App\Models\WarehouseZone;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WarehousesController extends Controller
{
    public $user;

    public function __construct()
    {
         $this->user = Auth::guard('admin')->user();
    }

    /**
     * Five tabs: Warehouses / Zones / Racks / Shelves / Bins — Section 19/20.
     */
    public function index()
    {
        if (is_null($this->user) || ! $this->user->can('warehouse.view')) {
            abort(403, 'Sorry !! You are Unauthorized to view Warehouses !');
        }

        $warehouses = Warehouse::withCount('stock')->orderBy('name')->get();
        $zones = WarehouseZone::with('warehouse')->orderBy('name')->get();
        $racks = Rack::with('warehouse', 'zone')->orderBy('name')->get();
        $shelves = Shelf::with('rack.warehouse')->orderBy('name')->get();
        $bins = Bin::with('shelf.rack.warehouse')->orderBy('name')->get();

        return view('backend.warehouses.index', compact('warehouses', 'zones', 'racks', 'shelves', 'bins'));
    }

    public function store(Request $request)
    {
        if (is_null($this->user) || ! $this->user->can('warehouse.create')) {
            abort(403);
        }

        $request->validate([
            'name' => 'required|max:100',
            'code' => 'required|max:20|unique:warehouses,code',
            'manager' => 'nullable|max:100',
            'contact_number' => 'nullable|max:30',
            'address' => 'nullable',
            'city' => 'nullable|max:60',
            'country' => 'nullable|max:60',
        ]);

        $warehouse = Warehouse::create($request->only('name', 'code', 'manager', 'contact_number', 'address', 'city', 'country'));

        // First warehouse created automatically becomes the default so
        // spare parts always have somewhere to seed opening stock into.
        if (Warehouse::count() === 1) {
            $warehouse->update(['is_default' => true]);
        }

        AuditLog::record('create', 'Warehouses', $warehouse, "Created warehouse \"{$warehouse->name}\"");

        session()->flash('success', 'Warehouse has been created !!');
        return back();
    }

    public function update(Request $request, int $id)
    {
        if (is_null($this->user) || ! $this->user->can('warehouse.edit')) {
            abort(403);
        }

        $warehouse = Warehouse::findOrFail($id);
        $request->validate([
            'name' => 'required|max:100',
            'code' => 'required|max:20|unique:warehouses,code,'.$id,
        ]);

        $original = $warehouse->only(['name', 'code']);
        $warehouse->update($request->only('name', 'code', 'manager', 'contact_number', 'address', 'city', 'country', 'status'));

        if ($request->boolean('is_default')) {
            Warehouse::where('id', '!=', $id)->update(['is_default' => false]);
            $warehouse->update(['is_default' => true]);
        }

        AuditLog::record('update', 'Warehouses', $warehouse, "Updated warehouse \"{$warehouse->name}\"", $original, $warehouse->only(['name', 'code']));

        session()->flash('success', 'Warehouse has been updated !!');
        return back();
    }

    public function destroy(int $id)
    {
        if (is_null($this->user) || ! $this->user->can('warehouse.delete')) {
            abort(403);
        }

        $warehouse = Warehouse::findOrFail($id);

        if ($warehouse->stock()->where('current_stock', '>', 0)->exists()) {
            session()->flash('error', 'Cannot delete a warehouse that still holds stock. Transfer it out first.');
            return back();
        }

        AuditLog::record('delete', 'Warehouses', $warehouse, "Deleted warehouse \"{$warehouse->name}\"");
        $warehouse->delete();

        session()->flash('success', 'Warehouse has been deleted !!');
        return back();
    }

    // ---- Zones ----

    public function storeZone(Request $request)
    {
        if (is_null($this->user) || ! $this->user->can('warehouse.create')) {
            abort(403);
        }

        $request->validate(['warehouse_id' => 'required|exists:warehouses,id', 'name' => 'required|max:60']);
        $zone = WarehouseZone::create($request->only('warehouse_id', 'name'));
        AuditLog::record('create', 'Warehouses', $zone, "Created zone \"{$zone->name}\"");

        session()->flash('success', 'Zone has been created !!');
        return back();
    }

    // ---- Racks ----

    public function storeRack(Request $request)
    {
        if (is_null($this->user) || ! $this->user->can('warehouse.create')) {
            abort(403);
        }

        $request->validate([
            'warehouse_id' => 'required|exists:warehouses,id',
            'warehouse_zone_id' => 'nullable|exists:warehouse_zones,id',
            'name' => 'required|max:40',
        ]);

        $rack = Rack::create($request->only('warehouse_id', 'warehouse_zone_id', 'name'));
        AuditLog::record('create', 'Warehouses', $rack, "Created rack \"{$rack->name}\"");

        session()->flash('success', 'Rack has been created !!');
        return back();
    }

    // ---- Shelves ----

    public function storeShelf(Request $request)
    {
        if (is_null($this->user) || ! $this->user->can('warehouse.create')) {
            abort(403);
        }

        $request->validate(['rack_id' => 'required|exists:racks,id', 'name' => 'required|max:40']);
        $shelf = Shelf::create($request->only('rack_id', 'name'));
        AuditLog::record('create', 'Warehouses', $shelf, "Created shelf \"{$shelf->name}\"");

        session()->flash('success', 'Shelf has been created !!');
        return back();
    }

    // ---- Bins ----

    public function storeBin(Request $request)
    {
        if (is_null($this->user) || ! $this->user->can('warehouse.create')) {
            abort(403);
        }

        $request->validate(['shelf_id' => 'required|exists:shelves,id', 'name' => 'required|max:40']);
        $bin = Bin::create($request->only('shelf_id', 'name'));
        AuditLog::record('create', 'Warehouses', $bin, "Created bin \"{$bin->name}\"");

        session()->flash('success', 'Bin has been created !!');
        return back();
    }

    // ---- Cascading AJAX (shared by this page and the Spare Part form) ----

    public function racksForWarehouse(int $warehouseId)
    {
        return response()->json(Rack::where('warehouse_id', $warehouseId)->orderBy('name')->get(['id', 'name']));
    }

    public function shelvesForRack(int $rackId)
    {
        return response()->json(Shelf::where('rack_id', $rackId)->orderBy('name')->get(['id', 'name']));
    }

    public function binsForShelf(int $shelfId)
    {
        return response()->json(Bin::where('shelf_id', $shelfId)->orderBy('name')->get(['id', 'name']));
    }
}
