<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\VehicleMake;
use App\Models\VehicleModel;
use App\Models\VehicleType;
use App\Models\VehicleVariant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VehiclesController extends Controller
{
    public $user;

    public function __construct()
    {
         $this->user = Auth::guard('admin')->user();
    }

    /**
     * Single page, three tabs: Makes / Models / Variants — matches Section 6.
     */
    public function index()
    {
        if (is_null($this->user) || ! $this->user->can('vehicle.view')) {
            abort(403, 'Sorry !! You are Unauthorized to view Vehicle Management !');
        }

        $makes = VehicleMake::orderBy('name')->get();
        $types = VehicleType::orderBy('name')->get();
        $models = VehicleModel::with('make')->orderBy('name')->get();
        $variants = VehicleVariant::with('model_.make')->orderByDesc('id')->get();

        return view('backend.vehicles.index', compact('makes', 'types', 'models', 'variants'));
    }

    // ---- Makes ----

    public function storeMake(Request $request)
    {
        if (is_null($this->user) || ! $this->user->can('vehicle.create')) {
            abort(403);
        }

        $request->validate(['name' => 'required|max:80|unique:vehicle_makes,name']);
        $make = VehicleMake::create($request->only('name'));
        AuditLog::record('create', 'Vehicles', $make, "Created vehicle make \"{$make->name}\"");

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'make' => $make]);
        }

        session()->flash('success', 'Vehicle make has been created !!');
        return back();
    }

    // ---- Models ----

    public function storeModel(Request $request)
    {
        if (is_null($this->user) || ! $this->user->can('vehicle.create')) {
            abort(403);
        }

        $request->validate([
            'vehicle_make_id' => 'required|exists:vehicle_makes,id',
            'vehicle_type_id' => 'nullable|exists:vehicle_types,id',
            'name' => 'required|max:80',
        ]);

        $model = VehicleModel::create($request->only('vehicle_make_id', 'vehicle_type_id', 'name'));
        AuditLog::record('create', 'Vehicles', $model, "Created vehicle model \"{$model->name}\"");

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'model' => $model->load('make')]);
        }

        session()->flash('success', 'Vehicle model has been created !!');
        return back();
    }

    /**
     * AJAX: models for a given make — powers the cascading dropdown in both
     * the standalone Vehicle Management page and the Spare Part
     * compatibility tab.
     */
    public function modelsForMake(int $makeId)
    {
        $models = VehicleModel::where('vehicle_make_id', $makeId)->orderBy('name')->get(['id', 'name']);
        return response()->json($models);
    }

    // ---- Variants ----

    public function storeVariant(Request $request)
    {
        if (is_null($this->user) || ! $this->user->can('vehicle.create')) {
            abort(403);
        }

        $request->validate([
            'vehicle_model_id' => 'required|exists:vehicle_models,id',
            'name' => 'required|max:60',
            'generation' => 'nullable|max:40',
            'engine_type' => 'nullable|max:60',
            'engine_capacity' => 'nullable|max:20',
            'fuel_type' => 'nullable|in:Petrol,Diesel,Hybrid,Electric,CNG,LPG',
            'transmission' => 'nullable|in:Manual,Automatic,CVT',
            'drive_type' => 'nullable|in:FWD,RWD,AWD,4WD',
            'start_year' => 'nullable|integer|min:1950|max:2100',
            'end_year' => 'nullable|integer|min:1950|max:2100|gte:start_year',
        ]);

        $variant = VehicleVariant::create($request->only([
            'vehicle_model_id', 'name', 'generation', 'engine_type', 'engine_capacity',
            'fuel_type', 'transmission', 'drive_type', 'start_year', 'end_year',
        ]));

        AuditLog::record('create', 'Vehicles', $variant, "Created vehicle variant \"{$variant->name}\"");

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'variant' => $variant->load('model_.make')]);
        }

        session()->flash('success', 'Vehicle variant has been created !!');
        return back();
    }

    /**
     * AJAX search across make + model + variant used by the Spare Part
     * compatibility Select2 (e.g. typing "Corolla 2020" finds every
     * matching variant regardless of which field it matches).
     */
    public function searchVariants(Request $request)
    {
        $term = $request->get('q', '');

        $variants = VehicleVariant::with('model_.make')
            ->whereHas('model_.make', fn ($q) => $q->where('name', 'like', "%$term%"))
            ->orWhereHas('model_', fn ($q) => $q->where('name', 'like', "%$term%"))
            ->orWhere('name', 'like', "%$term%")
            ->orWhere('start_year', 'like', "%$term%")
            ->limit(30)
            ->get()
            ->map(fn ($v) => ['id' => $v->id, 'text' => $v->label]);

        return response()->json(['results' => $variants]);
    }

    public function destroyVariant(int $id)
    {
        if (is_null($this->user) || ! $this->user->can('vehicle.delete')) {
            abort(403);
        }

        $variant = VehicleVariant::findOrFail($id);
        AuditLog::record('delete', 'Vehicles', $variant, "Deleted vehicle variant \"{$variant->name}\"");
        $variant->delete();

        session()->flash('success', 'Vehicle variant has been deleted !!');
        return back();
    }
}
