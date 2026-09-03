<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Menu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route as RouteFacade;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Permission;

class MenusController extends Controller
{
    public $user;

    public function __construct()
    {
         $this->user = Auth::guard('admin')->user();
    }

    /**
     * Tree view (heading/link nesting, drag-and-drop reorder handles) rather
     * than a flat table — the whole point of this screen is to see and edit
     * the sidebar's actual shape.
     */
    public function index()
    {
        if (is_null($this->user) || ! $this->user->can('menu.view')) {
            abort(403, 'Sorry !! You are Unauthorized to view Menus !');
        }

        $menus = Menu::with('childrenRecursive')->topLevel()->ordered()->get();

        return view('backend.menus.index', compact('menus'));
    }

    public function create()
    {
        if (is_null($this->user) || ! $this->user->can('menu.create')) {
            abort(403, 'Sorry !! You are Unauthorized to create Menus !');
        }

        // ONLY top-level LINK menus can be selected as parents.
        $parents = Menu::query()
            ->whereNull('parent_id')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        $permissions = Permission::where('guard_name', 'admin')
            ->orderBy('name')
            ->pluck('name');

        return view('backend.menus.create', compact(
            'parents',
            'permissions'
        ));
    }

    public function store(Request $request)
    {
        if (is_null($this->user) || ! $this->user->can('menu.create')) {
            abort(403, 'Sorry !! You are Unauthorized to create Menus !');
        }

        $data = $this->validated($request);

        $menu = Menu::create($data);

        AuditLog::record('create', 'Menus', $menu, "Created menu \"{$menu->name}\"");

        session()->flash('success', 'Menu has been created !!');
        return redirect()->route('admin.menus.index');
    }

    public function edit(int $id)
    {
        if (is_null($this->user) || ! $this->user->can('menu.edit')) {
            abort(403, 'Sorry !! You are Unauthorized to edit Menus !');
        }

        $menu = Menu::findOrFail($id);

        /*
        |--------------------------------------------------------------------------
        | Parent Menu List
        |--------------------------------------------------------------------------
        | Only TOP-LEVEL LINK menus can be parents.
        |
        | Exclude:
        | 1. The current menu itself
        | 2. All descendants of the current menu
        |
        | This prevents circular nesting.
        |--------------------------------------------------------------------------
        */

        $excludedIds = $this->descendantIds($menu);

        // Current menu cannot be its own parent.
        $excludedIds[] = $menu->id;

          $parents = Menu::query()
            ->whereNull('parent_id')
            ->where('id', '!=', $menu->id)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        $permissions = Permission::where('guard_name', 'admin')
            ->orderBy('name')
            ->pluck('name');

        return view('backend.menus.edit', compact(
            'menu',
            'parents',
            'permissions'
        ));
    }

    public function update(Request $request, int $id)
    {
        if (is_null($this->user) || ! $this->user->can('menu.edit')) {
            abort(403, 'Sorry !! You are Unauthorized to edit Menus !');
        }

        $menu = Menu::findOrFail($id);

        $excludedIds = $this->descendantIds($menu);
        $excludedIds[] = $menu->id;

        if ($request->filled('parent_id') && in_array((int) $request->input('parent_id'), $excludedIds, true)) {
            return back()->withErrors(['parent_id' => 'A menu cannot be moved under itself or one of its own sub-items.'])->withInput();
        }

        $data = $this->validated($request);
        $original = $menu->only(['name', 'parent_id', 'status', 'sort_order']);

        $menu->update($data);

        AuditLog::record('update', 'Menus', $menu, "Updated menu \"{$menu->name}\"", $original, $menu->only(['name', 'parent_id', 'status', 'sort_order']));

        session()->flash('success', 'Menu has been updated !!');
        return redirect()->route('admin.menus.index');
    }

    public function destroy(int $id)
    {
        if (is_null($this->user) || ! $this->user->can('menu.delete')) {
            abort(403, 'Sorry !! You are Unauthorized to delete Menus !');
        }

        $menu = Menu::findOrFail($id);
        $childCount = $menu->children()->count();

        AuditLog::record('delete', 'Menus', $menu, "Deleted menu \"{$menu->name}\"".($childCount ? " (and {$childCount} sub-item(s))" : ''));

        // The parent_id foreign key cascades, so this also removes any
        // sub-items — the audit note above says so up front rather than
        // leaving that as a silent side effect.
        $menu->delete();

        session()->flash('success', 'Menu has been deleted !!');
        return back();
    }

    /**
     * Drag-and-drop reorder, called once per drop from index.blade.php.
     * Accepts the moved item's new parent and the full ordered id list of
     * its new sibling group (not just the one item) so sort_order stays
     * gap-free and consistent for everyone in that list, not just the item
     * that moved.
     */
    public function reorder(Request $request)
    {
        if (is_null($this->user) || ! $this->user->can('menu.edit')) {
            abort(403);
        }

        $request->validate([
            'parent_id' => 'nullable|integer|exists:menus,id',
            'ordered_ids' => 'required|array|min:1',
            'ordered_ids.*' => 'integer|exists:menus,id',
        ]);

        $parentId = $request->input('parent_id');

        // A parent must be a 'link' item (headings can't have dropdown
        // children) — if the drop target isn't one, treat this as a
        // top-level move instead of silently failing.

        // if ($parentId && optional(Menu::find($parentId))->type !== 'link') {
        //     $parentId = null;
        // }
        if ($parentId) {

            $parent = Menu::find($parentId);

            // Parent must exist and be a Link.
            if (!$parent || $parent->type !== 'link') {
                $parentId = null;
            }

            // Only TOP-LEVEL menus can have children.
            elseif (!is_null($parent->parent_id)) {
                $parentId = null;
            }
        }

        DB::transaction(function () use ($request, $parentId) {
            foreach ($request->input('ordered_ids') as $index => $menuId) {
                $menu = Menu::find($menuId);
                if (! $menu) {
                    continue;
                }

                // Headings are section labels, not nestable items — always top-level regardless of drop target.
                $effectiveParentId = $menu->type === 'heading' ? null : $parentId;

                // Refuse the loop case: dropping a menu onto itself or one of its own descendants.
                if ($effectiveParentId && ($effectiveParentId == $menu->id || in_array($effectiveParentId, $this->descendantIds($menu), true))) {
                    continue;
                }

                $menu->parent_id = $effectiveParentId;
                $menu->sort_order = $index;
                $menu->save();
            }
        });

        return response()->json(['success' => true]);
    }

    /**
     * Live-checks a route name while the admin types it into the form, so a
     * typo is caught before saving rather than surfacing as a dead "#" link
     * in the sidebar later.
     */
    public function checkRoute(Request $request)
    {
        if (is_null($this->user) || (! $this->user->can('menu.create') && ! $this->user->can('menu.edit'))) {
            abort(403);
        }

        $name = (string) $request->query('route_name');
        $exists = $name !== '' && RouteFacade::has($name);

        return response()->json([
            'exists' => $exists,
            'url' => $exists ? route($name, [], false) : null,
        ]);
    }

    private function validated(Request $request): array
    {
        $rules = [
            'parent_id' => [
                'nullable',
                'integer',
                Rule::exists('menus', 'id')->where(function ($query) {
                    $query->whereNull('parent_id')
                        ->where('status', 1);
                }),
            ],
            'type' => 'required|in:heading,link',
            'name' => 'required|max:100',
            'icon' => 'nullable|max:50',
            'url' => 'nullable|max:255',
            'route_name' => 'nullable|max:150',
            'active_pattern' => 'nullable|max:150',
            'permission' => 'nullable|max:100|exists:permissions,name',
            'sort_order' => 'nullable|integer|min:0',
            'target' => 'required|in:_self,_blank',
            'status' => 'nullable|boolean',
        ];

        $validated = $request->validate($rules);

        // A heading is a section label, not a link — strip anything
        // link-only so an accidental leftover value can't half-work.
        if ($validated['type'] === 'heading') {
            $validated['url'] = null;
            $validated['route_name'] = null;
            $validated['active_pattern'] = null;
            $validated['parent_id'] = null;
        }

        $validated['status'] = $request->boolean('status', true);
        $validated['sort_order'] = $validated['sort_order'] ?? 0;

        return $validated;
    }

    /**
     * @return array<int, int> every id in the subtree under $menu, not
     *   including $menu itself.
     */
    private function descendantIds(Menu $menu): array
    {
        $ids = [];
        $queue = Menu::where('parent_id', $menu->id)->pluck('id')->all();

        while ($queue) {
            $id = array_shift($queue);
            $ids[] = $id;
            $more = Menu::where('parent_id', $id)->pluck('id')->all();
            $queue = array_merge($queue, $more);
        }

        return $ids;
    }
}
