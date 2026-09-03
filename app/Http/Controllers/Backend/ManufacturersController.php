<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Manufacturer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ManufacturersController extends Controller
{
    public $user;

    public function __construct()
    {
         $this->user = Auth::guard('admin')->user();
    }

    public function index()
    {
        if (is_null($this->user) || ! $this->user->can('spare-part.view')) {
            abort(403, 'Sorry !! You are Unauthorized to view Manufacturers !');
        }

        $manufacturers = Manufacturer::orderBy('name')->get();

        return view('backend.manufacturers.index', compact('manufacturers'));
    }

    public function store(Request $request)
    {
        if (is_null($this->user) || ! $this->user->can('spare-part.create')) {
            abort(403, 'Sorry !! You are Unauthorized to create Manufacturers !');
        }

        $request->validate([
            'name' => 'required|max:100|unique:manufacturers,name',
            'country' => 'nullable|max:60',
        ]);

        $manufacturer = Manufacturer::create($request->only('name', 'country'));

        AuditLog::record('create', 'Manufacturers', $manufacturer, "Created manufacturer \"{$manufacturer->name}\"");

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'manufacturer' => $manufacturer]);
        }

        session()->flash('success', 'Manufacturer has been created !!');
        return back();
    }

    public function update(Request $request, int $id)
    {
        if (is_null($this->user) || ! $this->user->can('spare-part.edit')) {
            abort(403, 'Sorry !! You are Unauthorized to edit Manufacturers !');
        }

        $manufacturer = Manufacturer::findOrFail($id);
        $request->validate([
            'name' => 'required|max:100|unique:manufacturers,name,'.$id,
            'country' => 'nullable|max:60',
        ]);

        $original = $manufacturer->only(['name', 'country']);
        $manufacturer->update($request->only('name', 'country', 'status'));

        AuditLog::record('update', 'Manufacturers', $manufacturer, "Updated manufacturer \"{$manufacturer->name}\"", $original, $manufacturer->only(['name', 'country']));

        session()->flash('success', 'Manufacturer has been updated !!');
        return back();
    }

    public function destroy(int $id)
    {
        if (is_null($this->user) || ! $this->user->can('spare-part.delete')) {
            abort(403, 'Sorry !! You are Unauthorized to delete Manufacturers !');
        }

        $manufacturer = Manufacturer::findOrFail($id);
        AuditLog::record('delete', 'Manufacturers', $manufacturer, "Deleted manufacturer \"{$manufacturer->name}\"");
        $manufacturer->delete();

        session()->flash('success', 'Manufacturer has been deleted !!');
        return back();
    }
}
