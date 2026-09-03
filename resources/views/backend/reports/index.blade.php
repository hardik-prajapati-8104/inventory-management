@extends('backend.layouts.master')

@section('title')
Reports - Vehicle Spare Parts Inventory
@endsection

@section('admin-content')

<div class="page-title-area">
    <div class="row align-items-center">
        <div class="col-12">
            <h4 class="page-title mb-1">Reports</h4>
            <ul class="breadcrumbs">
                <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li><span>Reports</span></li>
            </ul>
        </div>
    </div>
</div>

@php
    $reports = [
        ['route' => 'admin.reports.stock-valuation', 'icon' => 'bi-box-seam', 'title' => 'Stock Valuation', 'desc' => 'Current stock quantity and value per part, filterable by category.'],
        ['route' => 'admin.reports.purchases', 'icon' => 'bi-bar-chart', 'title' => 'Purchase Reports', 'desc' => 'Summary, by Supplier, by Product, or by Category, over any date range.'],
        ['route' => 'admin.reports.sales', 'icon' => 'bi-bar-chart-line', 'title' => 'Sales Reports', 'desc' => 'Summary, by Customer, by Product, by Category, or by Salesperson.'],
        ['route' => 'admin.reports.profit', 'icon' => 'bi-piggy-bank', 'title' => 'Profit Reports', 'desc' => 'Daily, monthly, or yearly gross profit, plus top/bottom products by margin.'],
        ['route' => 'admin.reports.outstanding-suppliers', 'icon' => 'bi-file-bar-graph', 'title' => 'Supplier Outstanding', 'desc' => 'Every supplier with an unpaid purchase balance.'],
        ['route' => 'admin.reports.outstanding-customers', 'icon' => 'bi-file-bar-graph', 'title' => 'Customer Outstanding', 'desc' => 'Every customer with an unpaid sales balance.'],
        ['route' => 'admin.reports.supplier-price-comparison', 'icon' => 'bi-tags', 'title' => 'Supplier Price Comparison', 'desc' => 'See every supplier who has sold a given part, and who offers the best price.'],
    ];
@endphp

<div class="row g-3">
    @foreach ($reports as $r)
    <div class="col-md-4">
        <a href="{{ route($r['route']) }}" class="text-decoration-none">
            <div class="card h-100">
                <div class="card-body">
                    <div class="vsp-kpi__icon mb-3"><i class="bi {{ $r['icon'] }}"></i></div>
                    <h6 class="mb-1 text-dark">{{ $r['title'] }}</h6>
                    <p class="small text-muted mb-0">{{ $r['desc'] }}</p>
                </div>
            </div>
        </a>
    </div>
    @endforeach
</div>

@endsection
