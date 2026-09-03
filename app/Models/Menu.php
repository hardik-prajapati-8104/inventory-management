<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route as RouteFacade;

class Menu extends Model
{
    protected $fillable = [
        'parent_id', 'type', 'name', 'icon', 'url', 'route_name',
        'active_pattern', 'permission', 'sort_order', 'target', 'status',
    ];

    protected $casts = [
        'status' => 'boolean',
        'sort_order' => 'integer',
    ];

    protected static function booted()
    {
        static::saving(function (Menu $menu) {
            // A heading is a section label, not a link — belt-and-braces
            // version of the same rule MenusController::validated() applies
            // before it ever gets here: no parent, no route/url fields.
            if ($menu->type === 'heading') {
                $menu->parent_id = null;
                $menu->url = null;
                $menu->route_name = null;
                $menu->active_pattern = null;

                return;
            }

            // Auto-derive an active-state wildcard from route_name when the
            // admin didn't set one explicitly: "admin.spare-parts.index" ->
            // "admin.spare-parts.*", so editing/creating/deleting sub-pages
            // of a section still keeps that section's menu item highlighted.
            if (empty($menu->active_pattern) && ! empty($menu->route_name)) {
                $segments = explode('.', $menu->route_name);
                array_pop($segments);
                if ($segments) {
                    $menu->active_pattern = implode('.', $segments).'.*';
                }
            }
        });
    }

    public function parent()
    {
        return $this->belongsTo(Menu::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Menu::class, 'parent_id')->orderBy('sort_order');
    }

    /**
     * Full recursive tree, eager-loaded to a sane depth (5 levels — the UI
     * only ever really uses 2, but the schema/model don't hard-stop deeper
     * nesting if someone builds it).
     */
    public function childrenRecursive()
    {
        return $this->children()->with('childrenRecursive');
    }

    public function scopeTopLevel($query)
    {
        return $query->whereNull('parent_id');
    }

    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('id');
    }

    public function isHeading(): bool
    {
        return $this->type === 'heading';
    }

    /**
     * True when nobody needs a specific permission to see this item
     * (permission is null/blank) or the given admin has it — Super Admins
     * bypass entirely via Gate::before, same as everywhere else in the app.
     */
    public function isVisibleTo(?Admin $admin): bool
    {
        if (! $this->status) {
            return false;
        }

        if (empty($this->permission)) {
            return true;
        }

        return $admin instanceof Admin && $admin->can($this->permission);
    }

    /**
     * Resolves the href for a link item. route_name wins when present and
     * actually exists; falls back to the plain url field, then to '#' so a
     * misconfigured menu item never breaks the page it's rendered on.
     */
    public function resolveUrl(): string
    {
        if ($this->type === 'heading') {
            return '#';
        }

        if (! empty($this->route_name) && RouteFacade::has($this->route_name)) {
            try {
                return route($this->route_name);
            } catch (\Throwable) {
                // Named route exists but needs parameters this menu item
                // can't supply (e.g. an {id}) — fall through to url/#.
            }
        }

        return $this->url ?: '#';
    }

    /**
     * Is this item (or one of its descendants) the current page? Used both
     * to highlight the item itself and to auto-expand a parent dropdown
     * when a child route is active.
     */
    public function isActive(): bool
    {
        if (! empty($this->active_pattern) && request()->routeIs($this->active_pattern)) {
            return true;
        }

        if (! empty($this->route_name) && request()->routeIs($this->route_name)) {
            return true;
        }

        foreach ($this->children as $child) {
            if ($child->isActive()) {
                return true;
            }
        }

        return false;
    }

    /**
     * The tree used to render the live sidebar: active top-level items
     * (headings + links) with active, visible-to-the-current-admin children
     * attached recursively. Nothing in here bypasses status/permission —
     * an item a role can't see never even reaches the Blade partial.
     */
    public static function sidebarTree(): \Illuminate\Support\Collection
    {
        $admin = Auth::guard('admin')->user();

        $all = static::active()->ordered()->get();

        $visible = $all->filter(fn (Menu $menu) => $menu->isVisibleTo($admin));
        $byParent = $visible->groupBy('parent_id');

        $attach = function (Menu $menu) use (&$attach, $byParent) {
            $menu->setRelation(
                'children',
                ($byParent->get($menu->id) ?? collect())->map($attach)->values()
            );

            return $menu;
        };

        return $visible
            ->whereNull('parent_id')
            ->map($attach)
            ->values();
    }
}
