@extends('backend.layouts.master')

@section('title')
Settings - Vehicle Spare Parts Inventory
@endsection

@section('admin-content')

<div class="page-title-area">
    <div class="row align-items-center">
        <div class="col-md-8">
            <h4 class="page-title mb-1">Settings</h4>
            <ul class="breadcrumbs">
                <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li><span>Settings</span></li>
            </ul>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <ul class="nav nav-tabs" id="settingsTabs" role="tablist">
            <li class="nav-item"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#company" type="button">Company</button></li>
            <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#invoice" type="button">Invoice</button></li>
            <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#inventory" type="button">Inventory</button></li>
            <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#general" type="button">General</button></li>
        </ul>

        <div class="tab-content pt-4">

            {{-- Company --}}
            <div class="tab-pane fade show active" id="company">
                <form action="{{ route('admin.settings.update') }}" method="POST">
                    @csrf
                    <input type="hidden" name="group" value="company">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label>Company Name</label>
                            <input type="text" class="form-control" name="fields[name]" value="{{ $company['name'] ?? '' }}">
                        </div>
                        <div class="col-md-6">
                            <label>Phone</label>
                            <input type="text" class="form-control" name="fields[phone]" value="{{ $company['phone'] ?? '' }}">
                        </div>
                        <div class="col-md-6">
                            <label>Email</label>
                            <input type="email" class="form-control" name="fields[email]" value="{{ $company['email'] ?? '' }}">
                        </div>
                        <div class="col-md-6">
                            <label>Website</label>
                            <input type="text" class="form-control" name="fields[website]" value="{{ $company['website'] ?? '' }}">
                        </div>
                        <div class="col-md-6">
                            <label>Tax / VAT Number</label>
                            <input type="text" class="form-control" name="fields[tax_number]" value="{{ $company['tax_number'] ?? '' }}">
                        </div>
                        <div class="col-12">
                            <label>Address</label>
                            <textarea class="form-control" name="fields[address]">{{ $company['address'] ?? '' }}</textarea>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-add text-white mt-4"><i class="bi bi-save"></i> Save Company Settings</button>
                </form>
            </div>

            {{-- Invoice --}}
            <div class="tab-pane fade" id="invoice">
                <form action="{{ route('admin.settings.update') }}" method="POST">
                    @csrf
                    <input type="hidden" name="group" value="invoice">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label>Invoice Prefix</label>
                            <input type="text" class="form-control" name="fields[invoice_prefix]" value="{{ $invoice['invoice_prefix'] ?? 'INV-' }}">
                        </div>
                        <div class="col-md-4">
                            <label>Purchase Prefix</label>
                            <input type="text" class="form-control" name="fields[purchase_prefix]" value="{{ $invoice['purchase_prefix'] ?? 'PO-' }}">
                        </div>
                        <div class="col-md-4">
                            <label>Sales Prefix</label>
                            <input type="text" class="form-control" name="fields[sales_prefix]" value="{{ $invoice['sales_prefix'] ?? 'SO-' }}">
                        </div>
                        <div class="col-md-4">
                            <label>Return Prefix</label>
                            <input type="text" class="form-control" name="fields[return_prefix]" value="{{ $invoice['return_prefix'] ?? 'RT-' }}">
                        </div>
                        <div class="col-12">
                            <label>Invoice Footer / Terms &amp; Conditions</label>
                            <textarea class="form-control" name="fields[footer]">{{ $invoice['footer'] ?? '' }}</textarea>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-add text-white mt-4"><i class="bi bi-save"></i> Save Invoice Settings</button>
                </form>
            </div>

            {{-- Inventory --}}
            <div class="tab-pane fade" id="inventory">
                <form action="{{ route('admin.settings.update') }}" method="POST">
                    @csrf
                    <input type="hidden" name="group" value="inventory">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label>Allow Negative Stock</label>
                            <select class="form-select" name="fields[allow_negative_stock]">
                                <option value="0" {{ ($inventory['allow_negative_stock'] ?? '0') == '0' ? 'selected' : '' }}>No</option>
                                <option value="1" {{ ($inventory['allow_negative_stock'] ?? '0') == '1' ? 'selected' : '' }}>Yes</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label>Stock Valuation Method</label>
                            <select class="form-select" name="fields[valuation_method]">
                                <option value="weighted_average" {{ ($inventory['valuation_method'] ?? 'weighted_average') == 'weighted_average' ? 'selected' : '' }}>Weighted Average Cost</option>
                                <option value="fifo" {{ ($inventory['valuation_method'] ?? '') == 'fifo' ? 'selected' : '' }}>FIFO</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label>Low Stock Notification</label>
                            <select class="form-select" name="fields[low_stock_notification]">
                                <option value="1" {{ ($inventory['low_stock_notification'] ?? '1') == '1' ? 'selected' : '' }}>Enabled</option>
                                <option value="0" {{ ($inventory['low_stock_notification'] ?? '') == '0' ? 'selected' : '' }}>Disabled</option>
                            </select>
                            <div class="form-text">
                                Controls the low-stock &amp; out-of-stock alerts everywhere they appear: the
                                <i class="bi bi-bell"></i> bell dropdown, the <a href="{{ route('admin.notifications.index') }}">Notifications</a> page,
                                the Dashboard banner, and the daily email digest. Turning this off doesn't
                                hide the <a href="{{ route('admin.stock.low') }}">Low Stock</a> page itself
                                or the per-part badges on Spare Parts / Current Stock — only the proactive alerts.
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label>Default Minimum Stock (new parts)</label>
                            <input type="number" min="0" class="form-control" name="fields[low_stock_default_minimum]" value="{{ $inventory['low_stock_default_minimum'] ?? 0 }}">
                            <div class="form-text">
                                Pre-fills the "Minimum Stock" field when adding a new Spare Part, so low-stock
                                alerts work out of the box instead of needing every part edited first. Existing
                                parts are unaffected — change their minimum individually on the Spare Part edit page.
                            </div>
                        </div>
                    </div>
                    <div class="alert alert-light border mt-3 small text-muted">
                        Default Warehouse and Default Tax dropdowns will populate here once
                        Warehouses (Phase 3) and Tax rules (Phase 6) exist.
                    </div>
                    <button type="submit" class="btn btn-add text-white mt-2"><i class="bi bi-save"></i> Save Inventory Settings</button>
                </form>
            </div>

            {{-- General --}}
            <div class="tab-pane fade" id="general">
                <form action="{{ route('admin.settings.update') }}" method="POST">
                    @csrf
                    <input type="hidden" name="group" value="general">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label>Currency</label>
                            <input type="text" class="form-control" name="fields[currency]" value="{{ $general['currency'] ?? 'INR' }}">
                        </div>
                        <div class="col-md-4">
                            <label>Date Format</label>
                            <input type="text" class="form-control" name="fields[date_format]" value="{{ $general['date_format'] ?? 'Y-m-d' }}">
                        </div>
                        <div class="col-md-4">
                            <label>Timezone</label>
                            <input type="text" class="form-control" name="fields[timezone]" value="{{ $general['timezone'] ?? 'Asia/Kolkata' }}">
                        </div>
                    </div>
                    <button type="submit" class="btn btn-add text-white mt-4"><i class="bi bi-save"></i> Save General Settings</button>
                </form>
            </div>

        </div>
    </div>
</div>

@endsection
