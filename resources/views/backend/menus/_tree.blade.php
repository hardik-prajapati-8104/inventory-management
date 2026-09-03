@php
    /** @var \Illuminate\Support\Collection $menus */
    /** @var int|string $parentId empty string for the top level */
@endphp
<ul class="vsp-menu-tree {{ $parentId === '' ? 'vsp-menu-tree--root' : '' }}" data-parent-id="{{ $parentId }}">
    @foreach ($menus as $menu)
    <li class="vsp-menu-tree__item" data-id="{{ $menu->id }}" data-type="{{ $menu->type }}">
        <div class="vsp-menu-tree__row {{ $menu->type === 'heading' ? 'vsp-menu-tree__row--heading' : '' }}">
            <span class="vsp-menu-tree__handle" title="Drag to reorder / re-nest"><i class="bi bi-grip-vertical"></i></span>

            <i class="bi {{ $menu->icon ?: ($menu->type === 'heading' ? 'bi-dash-lg' : 'bi-link-45deg') }} vsp-menu-tree__icon"></i>

            <span class="vsp-menu-tree__name">{{ $menu->name }}</span>

            @if ($menu->type === 'heading')
                <span class="badge bg-secondary">Heading</span>
            @else
                <span class="badge badge-info">Link</span>
                <span class="small text-muted">{{ $menu->route_name ?: ($menu->url ?: '—') }}</span>
                @if ($menu->permission)
                    <span class="badge bg-warning text-dark"><i class="bi bi-shield-lock"></i> {{ $menu->permission }}</span>
                @endif
                @if ($menu->target === '_blank')
                    <span class="badge bg-light text-dark border"><i class="bi bi-box-arrow-up-right"></i> New tab</span>
                @endif
            @endif

            @if (! $menu->status)
                <span class="badge bg-secondary">Inactive</span>
            @endif

            <span class="vsp-menu-tree__actions ms-auto">
                @can('menu.edit')
                <a href="{{ route('admin.menus.edit', $menu->id) }}" class="btn btn-sm btn-outline-secondary" title="Edit"><i class="bi bi-pencil"></i></a>
                <button type="button"
                        class="btn btn-sm btn-outline-secondary vsp-menu-toggle-status"
                        data-id="{{ $menu->id }}"
                        data-status="{{ $menu->status ? 1 : 0 }}"
                        title="{{ $menu->status ? 'Deactivate' : 'Activate' }}">
                    <i class="bi {{ $menu->status ? 'bi-eye' : 'bi-eye-slash' }}"></i>
                </button>
                @endcan
                @can('menu.delete')
                <form action="{{ route('admin.menus.destroy', $menu->id) }}" method="POST" class="d-inline"
                      onsubmit="return confirm('Delete \'{{ $menu->name }}\'{{ $menu->childrenRecursive->count() ? ' and its '.$menu->childrenRecursive->count().' sub-item(s)' : '' }}?');">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete"><i class="bi bi-trash"></i></button>
                </form>
                @endcan
            </span>
        </div>

        @if ($menu->type === 'link')
            @include('backend.menus._tree', ['menus' => $menu->childrenRecursive, 'parentId' => $menu->id])
        @endif
    </li>
    @endforeach
</ul>
