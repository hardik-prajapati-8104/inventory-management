<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BrandsController extends Controller
{
    public $user;

    public function __construct()
    {
         $this->user = Auth::guard('admin')->user();
    }

    public function index()
    {
        if (is_null($this->user) || ! $this->user->can('spare-part.view')) {
            abort(403, 'Sorry !! You are Unauthorized to view Brands !');
        }

        $brands = Brand::orderBy('name')->get();

        return view('backend.brands.index', compact('brands'));
    }

    public function store(Request $request)
    {
        if (is_null($this->user) || ! $this->user->can('spare-part.create')) {
            abort(403, 'Sorry !! You are Unauthorized to create Brands !');
        }

        $request->validate(['name' => 'required|max:100|unique:brands,name']);

        $brand = Brand::create($request->only('name'));

        AuditLog::record('create', 'Brands', $brand, "Created brand \"{$brand->name}\"");

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'brand' => $brand]);
        }

        session()->flash('success', 'Brand has been created !!');
        return back();
    }

    public function update(Request $request, int $id)
    {
        if (is_null($this->user) || ! $this->user->can('spare-part.edit')) {
            abort(403, 'Sorry !! You are Unauthorized to edit Brands !');
        }

        $brand = Brand::findOrFail($id);
        $request->validate(['name' => 'required|max:100|unique:brands,name,'.$id]);

        $original = $brand->only('name');
        $brand->update($request->only('name', 'status'));

        AuditLog::record('update', 'Brands', $brand, "Updated brand \"{$brand->name}\"", $original, $brand->only('name'));

        session()->flash('success', 'Brand has been updated !!');
        return back();
    }

    public function destroy(int $id)
    {
        if (is_null($this->user) || ! $this->user->can('spare-part.delete')) {
            abort(403, 'Sorry !! You are Unauthorized to delete Brands !');
        }

        $brand = Brand::findOrFail($id);
        AuditLog::record('delete', 'Brands', $brand, "Deleted brand \"{$brand->name}\"");
        $brand->delete();

        session()->flash('success', 'Brand has been deleted !!');
        return back();
    }
}
