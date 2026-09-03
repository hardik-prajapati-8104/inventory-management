@extends('backend.layouts.master')

@section('title')
    Menu Management - Vehicle Spare Parts Inventory
@endsection


{{-- =========================================================
    PAGE CSS
========================================================= --}}
@section('styles')

<style>

    /* =========================================================
       PAGE HEADER
    ========================================================= */

    .menu-page-title {
        font-size: 30px;
        font-weight: 700;
        color: #111827;
        margin-bottom: 3px;
    }

    .menu-page-breadcrumbs {
        margin: 0;
        padding: 0;
        list-style: none;
        display: flex;
        gap: 5px;
        align-items: center;
        font-size: 13px;
    }

    .menu-page-breadcrumbs li {
        list-style: none;
    }

    .menu-page-breadcrumbs li:not(:last-child)::after {
        content: "/";
        margin-left: 5px;
        color: #9ca3af;
    }

    .menu-page-breadcrumbs a {
        color: #64748b;
        text-decoration: none;
    }

    .menu-page-breadcrumbs span {
        color: #111827;
    }


    /* =========================================================
       HEADER BUTTONS
    ========================================================= */

    .menu-preview-btn,
    .menu-add-btn {
        height: 38px;
        padding: 0 15px;
        border-radius: 6px;

        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 7px;

        font-size: 14px;
        font-weight: 500;
    }

    .menu-preview-btn {
        background: #fff;
        border: 1px solid #d9dee5;
        color: #111827;
    }

    .menu-preview-btn:hover {
        background: #f8fafc;
        border-color: #cbd5e1;
        color: #111827;
    }

    .menu-add-btn {
        margin-left: 5px;

        background: #b38f51;
        border: 1px solid #b38f51;

        color: #fff !important;
    }

    .menu-add-btn:hover {
        background: #9e7d45;
        border-color: #9e7d45;
        color: #fff !important;
    }


    /* =========================================================
       MAIN CARD
    ========================================================= */

    .menu-management-card {
        margin-top: 28px;

        border: 0;
        border-radius: 16px;

        background: #fff;

        box-shadow: 0 2px 15px rgba(0, 0, 0, .04);

        overflow: visible;
    }

    .menu-management-card .card-body {
        padding: 24px;
    }


    /* =========================================================
       FILTER AREA
    ========================================================= */

    .menu-filter-area {
        margin-bottom: 24px;
    }

    .menu-search-wrapper {
        position: relative;
    }

    .menu-search-icon {
        position: absolute;

        left: 13px;
        top: 50%;

        transform: translateY(-50%);

        color: #111827;

        font-size: 17px;

        z-index: 2;
    }

    .menu-search-wrapper input {
        height: 38px;

        padding-left: 40px;

        border: 1px solid #d9dee5;
        border-radius: 6px;

        color: #334155;
        font-size: 14px;
    }

    .menu-search-wrapper input::placeholder {
        color: #64748b;
    }

    .menu-filter-select {
        height: 38px;

        border: 1px solid #d9dee5;
        border-radius: 6px;

        color: #334155;

        font-size: 14px;

        cursor: pointer;
    }

    .menu-search-wrapper input:focus,
    .menu-filter-select:focus {
        border-color: #b38f51;

        box-shadow: 0 0 0 3px rgba(179, 143, 81, .10);
    }

    .menu-filter-btn {
        width: 100%;
        height: 38px;

        border: 1px solid #b38f51;
        border-radius: 6px;

        background: #b38f51;
        color: #fff;

        font-size: 14px;
        font-weight: 600;
    }

    .menu-filter-btn:hover {
        background: #9e7d45;
        border-color: #9e7d45;
        color: #fff;
    }

    .menu-reset-btn {
        height: 38px;

        border: 1px solid #d9dee5;
        border-radius: 6px;

        background: #fff;
        color: #111827;

        font-size: 14px;
    }

    .menu-reset-btn:hover {
        background: #f8fafc;
        color: #111827;
    }


    /* =========================================================
       TABLE WRAPPER
    ========================================================= */

    .menu-table-wrapper {
        width: 100%;
        overflow-x: auto;

        scrollbar-width: thin;
    }

    .menu-table-wrapper::-webkit-scrollbar {
        height: 7px;
    }

    .menu-table-wrapper::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 10px;
    }


    /* =========================================================
       TABLE
    ========================================================= */

    .menu-management-table {
        width: 100%;

        min-width: 1450px;

        margin: 0;

        border-collapse: separate;
        border-spacing: 0;
    }

    .menu-management-table thead th {
        padding: 11px 8px;

        border-bottom: 1px solid #d9dee5;

        background: #fff;

        color: #111827;

        font-size: 12px;
        font-weight: 700;

        line-height: 1.3;

        white-space: nowrap;

        text-transform: uppercase;
    }

    .menu-management-table tbody td {
        padding: 9px 8px;

        border-bottom: 1px solid #e1e5ea;

        color: #475569;

        font-size: 13px;

        vertical-align: middle;

        white-space: nowrap;
    }

    .menu-management-table tbody tr {
        background: #fff;

        transition:
            background-color .15s ease,
            box-shadow .15s ease;
    }

    .menu-management-table tbody tr:hover {
        background: #fafafa;
    }

    .menu-management-table tbody tr:last-child td {
        border-bottom: 0;
    }


    /* =========================================================
       DRAG HANDLE
    ========================================================= */

    .menu-drag-cell {
        width: 25px;
        text-align: center;
    }

    .menu-drag-handle {
        display: inline-flex;

        align-items: center;
        justify-content: center;

        width: 22px;
        height: 28px;

        color: #9ca3af;

        cursor: grab;

        user-select: none;
    }

    .menu-drag-handle:hover {
        color: #475569;
    }

    .menu-drag-handle:active {
        cursor: grabbing;
    }


    /* =========================================================
       MENU NAME
    ========================================================= */

    .menu-name-cell {
        min-width: 190px;

        display: flex;

        align-items: center;

        gap: 3px;

        color: #111827;
    }

    .menu-name-cell strong {
        color: #111827;

        font-size: 14px;
        font-weight: 600;
    }

    .menu-submenu-prefix {
        color: #475569;

        font-size: 16px;

        margin-right: 2px;
    }

    .menu-heading-prefix {
        color: #94a3b8;

        font-size: 14px;

        margin-right: 3px;
    }


    /* =========================================================
       PARENT
    ========================================================= */

    .menu-parent {
        color: #64748b;

        font-size: 13px;
    }

    .menu-parent-empty {
        color: #64748b;
    }


    /* =========================================================
       ICON
    ========================================================= */

    .menu-icon-cell {
        display: inline-flex;

        align-items: center;

        gap: 6px;

        color: #334155;
    }

    .menu-icon-cell i {
        color: #111827;

        font-size: 15px;
    }

    .menu-icon-name {
        color: #64748b;

        font-size: 12px;
    }


    /* =========================================================
       ROUTE
    ========================================================= */

    .menu-route {
        color: #e83e8c;

        font-family: inherit;

        font-size: 12px;

        background: transparent;
    }

    .menu-url {
        color: #e83e8c;

        font-size: 12px;
    }


    /* =========================================================
       PERMISSION
    ========================================================= */

    .menu-permission {
        color: #64748b;

        font-size: 13px;
    }

    .menu-permission-badge {
        display: inline-flex;

        align-items: center;

        gap: 4px;

        padding: 3px 7px;

        border-radius: 5px;

        background: #fef3c7;

        color: #92400e;

        font-size: 11px;
        font-weight: 600;
    }


    /* =========================================================
       TYPE BADGES
    ========================================================= */

    .menu-type-badge {
        display: inline-flex;

        align-items: center;
        justify-content: center;

        padding: 4px 9px;

        border-radius: 6px;

        font-size: 11px;

        font-weight: 600;

        line-height: 1.2;

        white-space: nowrap;
    }

    .menu-type-main {
        background: #dbeafe;
        color: #2563eb;
    }

    .menu-type-sub {
        background: #cffafe;
        color: #0e7490;
    }

    .menu-type-heading {
        background: #e5e7eb;
        color: #475569;
    }


    /* =========================================================
       ORDER
    ========================================================= */

    .menu-order-value {
        color: #111827;

        font-size: 13px;
        font-weight: 500;
    }


    /* =========================================================
       NEW TAB
    ========================================================= */

    .menu-new-tab {
        display: inline-flex;

        align-items: center;
        justify-content: center;

        width: 20px;
        height: 20px;

        color: #64748b;

        font-size: 15px;
    }

    .menu-new-tab.active {
        color: #2563eb;
    }


    /* =========================================================
       STATUS TOGGLE
    ========================================================= */

    .menu-status-toggle {
        position: relative;

        display: inline-block;

        width: 30px;
        height: 17px;

        padding: 0;

        border: 0;
        border-radius: 20px;

        background: #cbd5e1;

        cursor: pointer;

        transition: background-color .2s ease;
    }

    .menu-status-toggle span {
        position: absolute;

        top: 2px;
        left: 2px;

        width: 13px;
        height: 13px;

        border-radius: 50%;

        background: #fff;

        box-shadow: 0 1px 2px rgba(0,0,0,.15);

        transition: left .2s ease;
    }

    .menu-status-toggle.active {
        background: #147ef5;
    }

    .menu-status-toggle.active span {
        left: 15px;
    }

    .menu-status-toggle:disabled {
        opacity: .6;
        cursor: wait;
    }


    /* =========================================================
       CREATED DATE
    ========================================================= */

    .menu-created-date {
        color: #64748b;

        font-size: 12px;
    }


    /* =========================================================
       ACTION BUTTON
    ========================================================= */

    .menu-action-btn {
        display: inline-flex;

        align-items: center;
        justify-content: center;

        width: 32px;
        height: 32px;

        padding: 0;

        border: 1px solid #d9dee5;

        border-radius: 5px;

        background: #fff;

        color: #111827;
    }

    .menu-action-btn:hover,
    .menu-action-btn:focus {
        background: #f3f4f6;

        border-color: #cbd5e1;

        color: #111827;
    }


    /* =========================================================
       ACTION DROPDOWN
    ========================================================= */

    .menu-action-dropdown {
        min-width: 160px;

        padding: 6px;

        border: 1px solid #d9dee5;

        border-radius: 6px;

        box-shadow: 0 5px 20px rgba(0,0,0,.12);
    }

    .menu-action-dropdown .dropdown-item {
        display: flex;

        align-items: center;

        gap: 10px;

        padding: 9px 10px;

        border-radius: 4px;

        color: #111827;

        font-size: 14px;
    }

    .menu-action-dropdown .dropdown-item:hover {
        background: #f5f6f8;
    }

    .menu-action-dropdown .dropdown-item i {
        width: 18px;

        font-size: 15px;
    }

    .menu-action-dropdown .dropdown-item.text-danger {
        color: #dc2626 !important;
    }


    /* =========================================================
       SORTABLE
    ========================================================= */

    .menu-row.sortable-ghost {
        opacity: .45;

        background: #fff8e8;
    }

    .menu-row.sortable-chosen {
        background: #fffdf7;
    }

    .menu-row.sortable-drag {
        background: #fff;

        box-shadow: 0 8px 25px rgba(0,0,0,.15);
    }


    /* =========================================================
       EMPTY STATE
    ========================================================= */

    .menu-empty-state {
        padding: 50px 20px;

        text-align: center;
    }

    .menu-empty-state i {
        display: block;

        margin-bottom: 12px;

        color: #cbd5e1;

        font-size: 45px;
    }

    .menu-empty-state h6 {
        margin-bottom: 6px;

        color: #374151;

        font-size: 15px;
    }

    .menu-empty-state p {
        margin-bottom: 18px;

        color: #94a3b8;

        font-size: 13px;
    }


    /* =========================================================
       HIDDEN FILTER ROW
    ========================================================= */

    .menu-row.menu-hidden {
        display: none !important;
    }


    /* =========================================================
       RESPONSIVE
    ========================================================= */

    @media (max-width: 768px) {

        .menu-page-title {
            font-size: 24px;
        }

        .menu-management-card {
            margin-top: 20px;
        }

        .menu-management-card .card-body {
            padding: 15px;
        }

        .menu-preview-btn,
        .menu-add-btn {
            margin-top: 5px;
        }

        .menu-add-btn {
            margin-left: 0;
        }

    }

</style>

@endsection


{{-- =========================================================
    CONTENT
========================================================= --}}
@section('admin-content')


{{-- =========================================================
    PAGE HEADER
========================================================= --}}

<div class="page-title-area">

    <div class="row align-items-center">

        <div class="col-md-7">

            <h4 class="menu-page-title">
                Menu Management
            </h4>

            <ul class="menu-page-breadcrumbs">

                <li>
                    <a href="{{ route('admin.dashboard') }}">
                        Dashboard
                    </a>
                </li>

                <li>
                    <span>
                        Menu Management
                    </span>
                </li>

            </ul>

        </div>


        <div class="col-md-5 text-md-end mt-2 mt-md-0">

            {{-- Preview Sidebar --}}
            <button type="button"
                    class="btn menu-preview-btn"
                    id="previewSidebarBtn">

                <i class="bi bi-diagram-3"></i>

                Preview Sidebar

            </button>


            {{-- Add Menu --}}
            @can('menu.create')

                <a href="{{ route('admin.menus.create') }}"
                   class="btn menu-add-btn text-white">

                    <i class="bi bi-plus-lg"></i>

                    Add Menu Item

                </a>

            @endcan

        </div>

    </div>

</div>



{{-- =========================================================
    MAIN CARD
========================================================= --}}

<div class="card menu-management-card">

    <div class="card-body">


        {{-- =================================================
            FILTER
        ================================================== --}}

        <div class="menu-filter-area">

            <div class="row g-2 align-items-center">


                {{-- Search --}}
                <div class="col-lg-4 col-md-6">

                    <div class="menu-search-wrapper">

                        <i class="bi bi-search menu-search-icon"></i>

                        <input type="text"
                               id="menuSearch"
                               class="form-control"
                               placeholder="Search menu name..."
                               autocomplete="off">

                    </div>

                </div>


                {{-- Type --}}
                <div class="col-lg-3 col-md-6">

                    <select id="menuTypeFilter"
                            class="form-select menu-filter-select">

                        <option value="">
                            All Types
                        </option>

                        <option value="main">
                            Main Menu
                        </option>

                        <option value="sub">
                            Sub Menu
                        </option>

                        <option value="heading">
                            Heading
                        </option>

                    </select>

                </div>


                {{-- Status --}}
                <div class="col-lg-3 col-md-6">

                    <select id="menuStatusFilter"
                            class="form-select menu-filter-select">

                        <option value="">
                            All Status
                        </option>

                        <option value="1">
                            Active
                        </option>

                        <option value="0">
                            Inactive
                        </option>

                    </select>

                </div>


                {{-- Filter --}}
                <div class="col-lg-1 col-md-3">

                    <button type="button"
                            id="menuFilterBtn"
                            class="btn menu-filter-btn">

                        Filter

                    </button>

                </div>


                {{-- Reset --}}
                <div class="col-lg-1 col-md-3">

                    <button type="button"
                            id="menuResetBtn"
                            class="btn menu-reset-btn w-100">

                        Reset

                    </button>

                </div>

            </div>

        </div>



        {{-- =================================================
            TABLE
        ================================================== --}}

        <div class="menu-table-wrapper">

            <table class="table menu-management-table"
                   id="menuManagementTable">


                {{-- =================================================
                    HEADER
                ================================================== --}}

                <thead>

                    <tr>

                        <th class="menu-drag-cell"></th>

                        <th>
                            Menu Name
                        </th>

                        <th>
                            Parent
                        </th>

                        <th>
                            Icon
                        </th>

                        <th>
                            Route / URL
                        </th>

                        <th>
                            Permission
                        </th>

                        <th>
                            Type
                        </th>

                        <th class="text-center">
                            Order
                        </th>

                        <th class="text-center">
                            New Tab
                        </th>

                        <th class="text-center">
                            Status
                        </th>

                        <th>
                            Created
                        </th>

                        <th class="text-center">
                            Actions
                        </th>

                    </tr>

                </thead>


                {{-- =================================================
                    BODY
                ================================================== --}}

                <tbody id="menuSortableBody">

                @php

                    /*
                    |--------------------------------------------------------------------------
                    | Flatten menu tree
                    |--------------------------------------------------------------------------
                    | This supports both:
                    | 1. $menus containing top-level menus with children
                    | 2. $menus containing a flat collection
                    */

                    $menuRows = collect();

                    $flattenMenus = function ($items, $level = 0) use (&$flattenMenus, &$menuRows) {

                        foreach ($items as $item) {

                            $item->_menu_level = $level;

                            $menuRows->push($item);

                            if (
                                isset($item->children) &&
                                $item->children instanceof \Illuminate\Support\Collection &&
                                $item->children->count()
                            ) {
                                $flattenMenus(
                                    $item->children,
                                    $level + 1
                                );
                            }

                        }

                    };

                    $flattenMenus($menus);

                    /*
                    |--------------------------------------------------------------------------
                    | If controller returned flat collection
                    |--------------------------------------------------------------------------
                    */

                    if ($menuRows->isEmpty() && $menus->count()) {
                        $menuRows = $menus;
                    }

                @endphp


                @forelse($menuRows as $menu)

                    @php

                        $isHeading = (bool) $menu->is_heading;

                        $isSubMenu = !is_null($menu->parent_id);

                        if ($isHeading) {
                            $menuType = 'heading';
                        } elseif ($isSubMenu) {
                            $menuType = 'sub';
                        } else {
                            $menuType = 'main';
                        }

                        $menuStatus = (int) ($menu->status ?? 0);

                        $menuTargetBlank =
                            isset($menu->target) &&
                            $menu->target === '_blank';

                        $menuLevel = (int) ($menu->_menu_level ?? 0);

                    @endphp


                    <tr class="menu-row"

                        data-id="{{ $menu->id }}"

                        data-parent-id="{{ $menu->parent_id ?? '' }}"

                        data-type="{{ $menuType }}"

                        data-status="{{ $menuStatus }}"

                        data-level="{{ $menuLevel }}">


                        {{-- =================================================
                            DRAG
                        ================================================== --}}

                        <td class="menu-drag-cell">

                            <span class="menu-drag-handle"
                                  title="Drag to reorder">

                                <i class="bi bi-grip-vertical"></i>

                            </span>

                        </td>


                        {{-- =================================================
                            MENU NAME
                        ================================================== --}}

                        <td>

                            <div class="menu-name-cell"
                                 style="padding-left: {{ $menuLevel * 20 }}px;">


                                @if($isHeading)

                                    <span class="menu-heading-prefix">
                                        <i class="bi bi-dash-lg"></i>
                                    </span>

                                @elseif($isSubMenu)

                                    <span class="menu-submenu-prefix">
                                        — 
                                    </span>

                                @endif


                                <strong>
                                    {{ $menu->name }}
                                </strong>

                            </div>

                        </td>


                        {{-- =================================================
                            PARENT
                        ================================================== --}}

                        <td>

                            @if($menu->parent)

                                <span class="menu-parent">
                                    {{ $menu->parent->name }}
                                </span>

                            @elseif($menu->parent_id)

                                <span class="menu-parent">
                                    —
                                </span>

                            @else

                                <span class="menu-parent-empty">
                                    —
                                </span>

                            @endif

                        </td>


                        {{-- =================================================
                            ICON
                        ================================================== --}}

                        <td>

                            @if($menu->icon)

                                <span class="menu-icon-cell">

                                    <i class="{{ $menu->icon }}"></i>

                                    <span class="menu-icon-name">
                                        {{ $menu->icon }}
                                    </span>

                                </span>

                            @else

                                <span class="text-muted">
                                    —
                                </span>

                            @endif

                        </td>


                        {{-- =================================================
                            ROUTE / URL
                        ================================================== --}}

                        <td>

                            @if($menu->route_name)

                                <code class="menu-route">
                                    {{ $menu->route_name }}
                                </code>

                            @elseif($menu->url)

                                <span class="menu-url">
                                    {{ $menu->url }}
                                </span>

                            @else

                                <span class="text-muted">
                                    —
                                </span>

                            @endif

                        </td>


                        {{-- =================================================
                            PERMISSION
                        ================================================== --}}

                        <td>

                            @if($menu->permission)

                                <span class="menu-permission">
                                    {{ $menu->permission }}
                                </span>

                            @else

                                <span class="menu-permission">
                                    Public
                                </span>

                            @endif

                        </td>


                        {{-- =================================================
                            TYPE
                        ================================================== --}}

                        <td>

                            @if($menuType === 'heading')

                                <span class="menu-type-badge menu-type-heading">
                                    Heading
                                </span>

                            @elseif($menuType === 'sub')

                                <span class="menu-type-badge menu-type-sub">
                                    Sub Menu
                                </span>

                            @else

                                <span class="menu-type-badge menu-type-main">
                                    Main Menu
                                </span>

                            @endif

                        </td>


                        {{-- =================================================
                            ORDER
                        ================================================== --}}

                        <td class="text-center">

                            <span class="menu-order-value">
                                {{ $menu->sort_order ?? 0 }}
                            </span>

                        </td>


                        {{-- =================================================
                            NEW TAB
                        ================================================== --}}

                        <td class="text-center">

                            @if($menuTargetBlank)

                                <span class="menu-new-tab active"
                                      title="Opens in new tab">

                                    <i class="bi bi-check-circle"></i>

                                </span>

                            @else

                                <span class="menu-new-tab"
                                      title="Opens in same tab">

                                    <i class="bi bi-dash-circle"></i>

                                </span>

                            @endif

                        </td>


                        {{-- =================================================
                            STATUS
                        ================================================== --}}

                        <td class="text-center">

                            <button type="button"

                                    class="menu-status-toggle {{ $menuStatus ? 'active' : '' }}"

                                    data-id="{{ $menu->id }}"

                                    data-status="{{ $menuStatus }}"

                                    title="{{ $menuStatus ? 'Active' : 'Inactive' }}">

                                <span></span>

                            </button>

                        </td>


                        {{-- =================================================
                            CREATED
                        ================================================== --}}

                        <td>

                            <span class="menu-created-date">

                                {{ optional($menu->created_at)->format('d M Y') }}

                            </span>

                        </td>


                        {{-- =================================================
                            ACTIONS
                        ================================================== --}}

                        <td class="text-center">

                            <div class="dropdown">

                                <button type="button"

                                        class="btn menu-action-btn"

                                        data-bs-toggle="dropdown"

                                        data-bs-boundary="viewport"

                                        aria-expanded="false"

                                        title="Actions">

                                    <i class="bi bi-three-dots"></i>

                                </button>


                                <ul class="dropdown-menu dropdown-menu-end menu-action-dropdown">


                                    {{-- Edit --}}
                                    @can('menu.edit')

                                        <li>

                                            <a class="dropdown-item"

                                               href="{{ route('admin.menus.edit', $menu) }}">

                                                <i class="bi bi-pencil"></i>

                                                <span>
                                                    Edit
                                                </span>

                                            </a>

                                        </li>

                                    @endcan


                                    {{-- Duplicate --}}
                                    @can('menu.create')

                                        <li>

                                            <a class="dropdown-item"

                                               href="{{ route('admin.menus.create', [
                                                   'parent_id' => $menu->parent_id
                                               ]) }}">

                                                <i class="bi bi-copy"></i>

                                                <span>
                                                    Duplicate
                                                </span>

                                            </a>

                                        </li>

                                    @endcan


                                    @can('menu.delete')

                                        <li>
                                            <hr class="dropdown-divider">
                                        </li>

                                        <li>

                                            <form method="POST"

                                                  action="{{ route('admin.menus.destroy', $menu) }}"

                                                  class="delete-menu-form">

                                                @csrf

                                                @method('DELETE')


                                                <button type="submit"
                                                        class="dropdown-item text-danger">

                                                    <i class="bi bi-trash"></i>

                                                    <span>
                                                        Delete
                                                    </span>

                                                </button>

                                            </form>

                                        </li>

                                    @endcan


                                </ul>

                            </div>

                        </td>

                    </tr>

                @empty

                    <tr>

                        <td colspan="12">

                            <div class="menu-empty-state">

                                <i class="bi bi-menu-button-wide"></i>

                                <h6>
                                    No menu items found
                                </h6>

                                <p>
                                    Create your first menu item to get started.
                                </p>

                                @can('menu.create')

                                    <a href="{{ route('admin.menus.create') }}"
                                       class="btn menu-add-btn text-white">

                                        <i class="bi bi-plus-lg"></i>

                                        Add Menu Item

                                    </a>

                                @endcan

                            </div>

                        </td>

                    </tr>

                @endforelse

                </tbody>

            </table>

        </div>

    </div>

</div>


@endsection



{{-- =========================================================
    JAVASCRIPT
========================================================= --}}
@section('scripts')

<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.2/Sortable.min.js"></script>

<script>

(function () {

    'use strict';


    /* =========================================================
       CONFIG
    ========================================================= */

    const reorderUrl = @json(route('admin.menus.reorder'));

    const csrfToken = @json(csrf_token());


    const tableBody =
        document.getElementById('menuSortableBody');

    const searchInput =
        document.getElementById('menuSearch');

    const typeFilter =
        document.getElementById('menuTypeFilter');

    const statusFilter =
        document.getElementById('menuStatusFilter');

    const filterButton =
        document.getElementById('menuFilterBtn');

    const resetButton =
        document.getElementById('menuResetBtn');


    /* =========================================================
       DRAG & DROP
    ========================================================= */

    if (tableBody) {

        Sortable.create(tableBody, {

            animation: 180,

            handle: '.menu-drag-handle',

            ghostClass: 'sortable-ghost',

            chosenClass: 'sortable-chosen',

            dragClass: 'sortable-drag',

            forceFallback: false,

            onEnd: function () {

                saveMenuOrder();

            }

        });

    }


    /* =========================================================
       SAVE MENU ORDER
    ========================================================= */

    function saveMenuOrder() {

        if (!tableBody) {
            return;
        }


        const rows =
            Array.from(
                tableBody.querySelectorAll(
                    '.menu-row:not(.menu-hidden)'
                )
            );


        const orderedIds =
            rows.map(function (row) {

                return row.dataset.id;

            });


        if (!orderedIds.length) {
            return;
        }


        fetch(reorderUrl, {

            method: 'POST',

            headers: {

                'Content-Type': 'application/json',

                'X-CSRF-TOKEN': csrfToken,

                'Accept': 'application/json'

            },

            body: JSON.stringify({

                parent_id: null,

                ordered_ids: orderedIds

            })

        })

        .then(function (response) {

            if (!response.ok) {

                throw new Error(
                    'Could not save menu order.'
                );

            }

            return response.json();

        })

        .then(function () {

            /*
             * Update displayed order numbers immediately.
             */

            rows.forEach(function (row, index) {

                const orderElement =
                    row.querySelector('.menu-order-value');

                if (orderElement) {

                    orderElement.textContent =
                        index;

                }

            });

        })

        .catch(function () {

            alert(
                'Could not save the new order. Please refresh and try again.'
            );

        });

    }


    /* =========================================================
       FILTER FUNCTION
    ========================================================= */

    function applyFilters() {

        if (!tableBody) {
            return;
        }


        const searchValue =
            searchInput
                ? searchInput.value.trim().toLowerCase()
                : '';


        const selectedType =
            typeFilter
                ? typeFilter.value
                : '';


        const selectedStatus =
            statusFilter
                ? statusFilter.value
                : '';


        const rows =
            tableBody.querySelectorAll('.menu-row');


        rows.forEach(function (row) {

            const nameElement =
                row.querySelector('.menu-name-cell');


            const menuName =
                nameElement
                    ? nameElement.textContent.trim().toLowerCase()
                    : '';


            const rowType =
                row.dataset.type || '';


            const rowStatus =
                row.dataset.status || '';


            const matchesSearch =
                !searchValue ||
                menuName.includes(searchValue);


            const matchesType =
                !selectedType ||
                rowType === selectedType;


            const matchesStatus =
                !selectedStatus ||
                rowStatus === selectedStatus;


            if (
                matchesSearch &&
                matchesType &&
                matchesStatus
            ) {

                row.classList.remove(
                    'menu-hidden'
                );

            } else {

                row.classList.add(
                    'menu-hidden'
                );

            }

        });

    }


    /* =========================================================
       FILTER BUTTON
    ========================================================= */

    if (filterButton) {

        filterButton.addEventListener(
            'click',
            function () {

                applyFilters();

            }
        );

    }


    /* =========================================================
       SEARCH ENTER
    ========================================================= */

    if (searchInput) {

        searchInput.addEventListener(
            'keyup',
            function (event) {

                if (event.key === 'Enter') {

                    applyFilters();

                }

            }
        );

    }


    /* =========================================================
       RESET
    ========================================================= */

    if (resetButton) {

        resetButton.addEventListener(
            'click',
            function () {

                if (searchInput) {
                    searchInput.value = '';
                }

                if (typeFilter) {
                    typeFilter.value = '';
                }

                if (statusFilter) {
                    statusFilter.value = '';
                }

                applyFilters();

            }
        );

    }


    /* =========================================================
       STATUS TOGGLE
    ========================================================= */

    document
        .querySelectorAll('.menu-status-toggle')
        .forEach(function (button) {


            button.addEventListener(
                'click',
                function () {


                    const id =
                        button.dataset.id;


                    const currentStatus =
                        button.dataset.status === '1'
                            ? 1
                            : 0;


                    const newStatus =
                        currentStatus === 1
                            ? 0
                            : 1;


                    button.disabled = true;


                    fetch(
                        `{{ url('admin/update-field-status/menus') }}/${id}/${newStatus}`,
                        {

                            method: 'POST',

                            headers: {

                                'X-CSRF-TOKEN':
                                    csrfToken,

                                'Accept':
                                    'application/json'

                            }

                        }
                    )

                    .then(function (response) {

                        if (!response.ok) {

                            throw new Error(
                                'Status update failed.'
                            );

                        }

                        return response.json();

                    })

                    .then(function () {


                        button.dataset.status =
                            String(newStatus);


                        button.title =
                            newStatus === 1
                                ? 'Active'
                                : 'Inactive';


                        if (newStatus === 1) {

                            button.classList.add(
                                'active'
                            );

                        } else {

                            button.classList.remove(
                                'active'
                            );

                        }


                        /*
                         * Update filter status.
                         */

                        const row =
                            button.closest(
                                '.menu-row'
                            );


                        if (row) {

                            row.dataset.status =
                                String(newStatus);

                        }

                    })

                    .catch(function () {

                        alert(
                            'Could not update menu status. Please try again.'
                        );

                    })

                    .finally(function () {

                        button.disabled = false;

                    });

                }
            );

        });


    /* =========================================================
       DELETE CONFIRMATION
    ========================================================= */

    document
        .querySelectorAll('.delete-menu-form')
        .forEach(function (form) {

            form.addEventListener(
                'submit',
                function (event) {

                    const confirmed =
                        confirm(
                            'Are you sure you want to delete this menu item?'
                        );


                    if (!confirmed) {

                        event.preventDefault();

                    }

                }
            );

        });


    /* =========================================================
       PREVIEW SIDEBAR
    ========================================================= */

    const previewButton =
        document.getElementById(
            'previewSidebarBtn'
        );


       if (previewButton) {

        previewButton.addEventListener('click', function () {

            /*
             * Change this URL to your actual admin sidebar URL.
             *
             * For now, simply go to the dashboard.
             */

            window.location.href = @json(route('admin.dashboard'));

        });

    }


})();

</script>

@endsection