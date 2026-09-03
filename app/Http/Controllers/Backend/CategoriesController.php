<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CategoriesController extends Controller
{
    public $user;

    public function __construct()
    {
         $this->user = Auth::guard('admin')->user();
    }

    public function index()
    {
        if (is_null($this->user) || ! $this->user->can('spare-part.view')) {
            abort(403, 'Sorry !! You are Unauthorized to view Categories !');
        }

        $categories = Category::with('parent')->orderBy('name')->get();

        return view('backend.categories.index', compact('categories'));
    }

    public function store(Request $request)
    {
        if (is_null($this->user) || ! $this->user->can('spare-part.create')) {
            abort(403, 'Sorry !! You are Unauthorized to create Categories !');
        }

        $request->validate([
            'name' => 'required|max:100',
            'parent_id' => 'nullable|exists:categories,id',
            'icon' => 'nullable|max:50',
        ]);

        $category = Category::create($request->only('name', 'parent_id', 'icon', 'description'));

        AuditLog::record('create', 'Categories', $category, "Created category \"{$category->name}\"");

        // AJAX quick-add from the Spare Part form returns JSON so the new
        // category can be pushed straight into the open Select2 without a
        // page reload (Section 36: Quick Add Category).
        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'category' => $category]);
        }

        session()->flash('success', 'Category has been created !!');
        return back();
    }

    public function update(Request $request, int $id)
    {
        if (is_null($this->user) || ! $this->user->can('spare-part.edit')) {
            abort(403, 'Sorry !! You are Unauthorized to edit Categories !');
        }

        $category = Category::findOrFail($id);

        $request->validate([
            'name' => 'required|max:100',
            'parent_id' => 'nullable|exists:categories,id|not_in:'.$id,
            'icon' => 'nullable|max:50',
        ]);

        $original = $category->only(['name', 'parent_id']);
        $category->update($request->only('name', 'parent_id', 'icon', 'description', 'status'));

        AuditLog::record('update', 'Categories', $category, "Updated category \"{$category->name}\"", $original, $category->only(['name', 'parent_id']));

        session()->flash('success', 'Category has been updated !!');
        return back();
    }

    public function destroy(int $id)
    {
        if (is_null($this->user) || ! $this->user->can('spare-part.delete')) {
            abort(403, 'Sorry !! You are Unauthorized to delete Categories !');
        }

        $category = Category::findOrFail($id);

        if ($category->children()->exists()) {
            session()->flash('error', 'Cannot delete a category that has sub-categories. Remove or reassign them first.');
            return back();
        }

        AuditLog::record('delete', 'Categories', $category, "Deleted category \"{$category->name}\"");
        $category->delete();

        session()->flash('success', 'Category has been deleted !!');
        return back();
    }
}
