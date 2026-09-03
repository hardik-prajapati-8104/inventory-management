@extends('backend.layouts.master')

@section('title')
Import Spare Parts - Vehicle Spare Parts Inventory
@endsection

@section('admin-content')

<div class="page-title-area">
    <div class="row align-items-center">
        <div class="col-md-8">
            <h4 class="page-title mb-1">Import Spare Parts</h4>
            <ul class="breadcrumbs">
                <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li><a href="{{ route('admin.spare-parts.index') }}">Spare Parts</a></li>
                <li><span>Import</span></li>
            </ul>
        </div>
        <div class="col-md-4 text-md-end mt-2 mt-md-0">
            <a href="{{ route('admin.spare-parts.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i> Back</a>
        </div>
    </div>
</div>

<div class="row g-3">
    <div class="col-lg-7">
        <div class="card">
            <div class="card-body">
                <h6 class="mb-3">Upload File</h6>

                <form action="{{ route('admin.spare-parts.import.preview') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label>File (.xlsx, .xls, .csv, or .pdf, max 20MB)</label>
                        <input type="file" name="file" accept=".xlsx,.xls,.csv,.pdf" required class="form-control">
                        @error('file') <div class="text-error">{{ $message }}</div> @enderror
                    </div>
                    <button type="submit" class="btn btn-add text-white"><i class="bi bi-upload"></i> Upload &amp; Preview</button>
                    <a href="{{ route('admin.spare-parts.import.template') }}" class="btn btn-outline-secondary"><i class="bi bi-download"></i> Download Template</a>
                </form>

                <hr>

                <h6 class="mb-2 small text-muted">Spreadsheet (.xlsx / .xls / .csv) — Expected Columns</h6>
                <div class="d-flex flex-wrap gap-1">
                    @foreach (['Part Number*', 'SKU*', 'Part Name (required)', 'Category', 'Brand', 'OEM Number', 'Purchase Price', 'Selling Price', 'Opening Stock', 'Minimum Stock', 'Warehouse', 'Rack'] as $col)
                        <span class="badge badge-info">{{ $col }}</span>
                    @endforeach
                </div>
                <p class="small text-muted mt-2 mb-0">
                    *Part Number and SKU are auto-generated if left blank. Category,
                    Brand, and Warehouse are matched by name and created
                    automatically if they don't already exist — nothing blocks the
                    import waiting on missing lookup data.
                </p>

                <hr>

                <h6 class="mb-2 small text-muted">PDF (supplier estimate / invoice)</h6>
                <p class="small text-muted mb-0">
                    Upload a tabular supplier PDF with <strong>SN, Part No, Description,
                    Qty, Rate, Net Rate, Amount</strong> columns (e.g. a VAS-style
                    estimate) and it's read automatically — no template needed.
                    Scanned/image-only PDFs can't be read this way.
                </p>
                <p class="small text-muted mt-2 mb-0">
                    Each row is matched against the existing catalogue by
                    <strong>Part Number + Name</strong>: a match <strong>restocks</strong>
                    that part (adds the PDF quantity to current stock, updates its
                    purchase price) and a non-match <strong>creates</strong> a new spare
                    part with that quantity as opening stock. Both are shown clearly
                    on the preview screen before anything is saved.
                </p>
            </div>
        </div>
    </div>

    <div class="col-lg-5">
        <div class="card">
            <div class="card-body">
                <h6 class="mb-3">Recent Imports</h6>
                <table class="table table-bordered table-sm">
                    <thead><tr><th>File</th><th>Type</th><th>Created</th><th>Restocked</th><th>Skipped</th><th>Date</th></tr></thead>
                    <tbody>
                        @forelse ($recentLogs as $log)
                        <tr>
                            <td class="small">{{ $log->original_filename }}</td>
                            <td class="small"><span class="badge {{ ($log->source_type ?? 'sheet') === 'pdf' ? 'bg-danger text-white' : 'badge-info' }}">{{ strtoupper($log->source_type ?? 'sheet') }}</span></td>
                            <td class="text-success">{{ $log->imported_count }}</td>
                            <td class="text-primary">{{ $log->restocked_count ?? 0 }}</td>
                            <td class="{{ $log->skipped_count > 0 ? 'text-danger' : '' }}">{{ $log->skipped_count }}</td>
                            <td class="small">{{ $log->created_at->format('Y-m-d H:i') }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="6" class="text-center text-muted py-3">No imports yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@endsection
