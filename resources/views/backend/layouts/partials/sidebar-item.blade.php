@php
    $hasChildren = $item->children->isNotEmpty();
    $isActive = $item->isActive();
@endphp
<style>
    .vsp-sidebar__heading-wrapper {
        list-style: none;
    }

    .vsp-sidebar__heading-toggle {
        display: flex;
        align-items: center;
        justify-content: space-between;
        width: 100%;
        padding: 8px 16px;
        text-decoration: none;
        cursor: pointer;
    }

    .vsp-sidebar__heading-toggle .vsp-sidebar__heading {
        padding: 0;
    }

    .vsp-sidebar__heading-chevron {
        font-size: 11px;
        transition: transform 0.25s ease;
    }

    .vsp-sidebar__heading-toggle[aria-expanded="true"]
    .vsp-sidebar__heading-chevron {
        transform: rotate(180deg);
    }

    .vsp-sidebar__heading-submenu {
        margin: 0;
        padding: 0;
    }

    .vsp-sidebar__heading-submenu > li > a {
        padding-left: 24px;
    }
</style>
@if ($item->isHeading())

    {{-- Heading with Dropdown --}}
    @if ($hasChildren)

        <li class="vsp-sidebar__heading-wrapper">

            <a href="#vspHeading{{ $item->id }}"
               data-bs-toggle="collapse"
               role="button"
               aria-expanded="{{ $isActive ? 'true' : 'false' }}"
               aria-controls="vspHeading{{ $item->id }}"
               class="vsp-sidebar__heading-toggle">

                <span class="vsp-sidebar__heading">
                    {{ $item->name }}
                </span>

                <i class="bi bi-chevron-down vsp-sidebar__heading-chevron"></i>

            </a>

            <ul id="vspHeading{{ $item->id }}"
                class="collapse {{ $isActive ? 'show' : '' }} vsp-sidebar__heading-submenu list-unstyled">

                @foreach ($item->children as $child)
                    @include('backend.layouts.partials.sidebar-item', [
                        'item' => $child
                    ])
                @endforeach

            </ul>

        </li>

    @else

        {{-- Normal Heading --}}
        <li class="vsp-sidebar__heading">
            {{ $item->name }}
        </li>

    @endif

@elseif ($hasChildren)

    {{-- Normal Parent Menu --}}
    <li>

        <a href="#vspSubmenu{{ $item->id }}"
           data-bs-toggle="collapse"
           role="button"
           aria-expanded="{{ $isActive ? 'true' : 'false' }}"
           aria-controls="vspSubmenu{{ $item->id }}"
           class="vsp-sidebar__parent-link {{ $isActive ? 'active' : '' }}">

            <i class="bi {{ $item->icon ?: 'bi-dot' }}"></i>

            <span>{{ $item->name }}</span>

            <i class="bi bi-chevron-down vsp-sidebar__chevron ms-auto"></i>

        </a>

        <ul id="vspSubmenu{{ $item->id }}"
            class="collapse {{ $isActive ? 'show' : '' }} vsp-sidebar__submenu list-unstyled">

            @foreach ($item->children as $child)

                @include('backend.layouts.partials.sidebar-item', [
                    'item' => $child
                ])

            @endforeach

        </ul>

    </li>

@else

    {{-- Normal Menu Item --}}
    <li>

        <a href="{{ $item->resolveUrl() }}"
           @if ($item->target === '_blank')
               target="_blank"
               rel="noopener"
           @endif
           class="{{ $isActive ? 'active' : '' }}">

            <i class="bi {{ $item->icon ?: 'bi-dot' }}"></i>

            <span>{{ $item->name }}</span>

        </a>

    </li>

@endif