@php
    // Dynamic sidebar (Menu Management): the tree below already contains
    // only active items the current admin has permission to see — see
    // Menu::sidebarTree(). Nothing here re-checks permissions; that keeps
    // this partial dumb and the visibility rule defined in exactly one
    // place (the model).
    $menuTree = \App\Models\Menu::sidebarTree();
@endphp

<div class="vsp-sidebar__brand">
    <i class="bi bi-boxes"></i>
    <span class="vsp-sidebar__brand-text">VSP Inventory</span>
</div>

<nav class="vsp-sidebar__nav">
    <ul class="list-unstyled">
        @forelse ($menuTree as $item)
            @include('backend.layouts.partials.sidebar-item', ['item' => $item])
        @empty
            {{-- Nothing seeded yet (fresh install before `php artisan db:seed --class=MenuSeeder`), or every item has been deactivated. --}}
            <li class="px-3 py-2 small text-muted">
                No menu items configured.
                @can('menu.create')
                    <a href="{{ route('admin.menus.create') }}" class="text-decoration-underline">Add one</a>.
                @endcan
            </li>
        @endforelse
    </ul>
</nav>
