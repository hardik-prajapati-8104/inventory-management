<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UnitsController extends Controller
{
    public $user;

    public function __construct()
    {
         $this->user = Auth::guard('admin')->user();
    }

    public function index()
    {
        if (is_null($this->user) || ! $this->user->can('spare-part.view')) {
            abort(403, 'Sorry !! You are Unauthorized to view Units !');
        }

        $units = Unit::orderBy('name')->get();

        return view('backend.units.index', compact('units'));
    }

    public function store(Request $request)
    {
        if (is_null($this->user) || ! $this->user->can('spare-part.create')) {
            abort(403, 'Sorry !! You are Unauthorized to create Units !');
        }

        $request->validate([
            'name' => 'required|max:50|unique:units,name',
            'short_code' => 'nullable|max:10',
        ]);

        $unit = Unit::create($request->only('name', 'short_code'));

        AuditLog::record('create', 'Units', $unit, "Created unit \"{$unit->name}\"");

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'unit' => $unit]);
        }

        session()->flash('success', 'Unit has been created !!');
        return back();
    }

    public function update(Request $request, int $id)
    {
        if (is_null($this->user) || ! $this->user->can('spare-part.edit')) {
            abort(403, 'Sorry !! You are Unauthorized to edit Units !');
        }

        $unit = Unit::findOrFail($id);
        $request->validate([
            'name' => 'required|max:50|unique:units,name,'.$id,
            'short_code' => 'nullable|max:10',
        ]);

        $original = $unit->only(['name', 'short_code']);
        $unit->update($request->only('name', 'short_code', 'status'));

        AuditLog::record('update', 'Units', $unit, "Updated unit \"{$unit->name}\"", $original, $unit->only(['name', 'short_code']));

        session()->flash('success', 'Unit has been updated !!');
        return back();
    }

    public function destroy(int $id)
    {
        if (is_null($this->user) || ! $this->user->can('spare-part.delete')) {
            abort(403, 'Sorry !! You are Unauthorized to delete Units !');
        }

        $unit = Unit::findOrFail($id);
        AuditLog::record('delete', 'Units', $unit, "Deleted unit \"{$unit->name}\"");
        $unit->delete();

        session()->flash('success', 'Unit has been deleted !!');
        return back();
    }
}
