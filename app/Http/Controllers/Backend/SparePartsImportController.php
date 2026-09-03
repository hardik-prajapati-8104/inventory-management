<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Imports\SparePartsImport;
use App\Models\AuditLog;
use App\Models\Brand;
use App\Models\Category;
use App\Models\ImportLog;
use App\Models\SparePart;
use App\Models\Unit;
use App\Models\Warehouse;
use App\Services\Import\PdfEstimateParser;
use App\Services\StockService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;

class SparePartsImportController extends Controller
{
    public $user;
    private StockService $stockService;

    public function __construct(StockService $stockService)
    {
        $this->stockService = $stockService;

         $this->user = Auth::guard('admin')->user();
    }

    public function create()
    {
        if (is_null($this->user) || ! $this->user->can('spare-part.import')) {
            abort(403, 'Sorry !! You are Unauthorized to import Spare Parts !');
        }

        $recentLogs = ImportLog::with('createdBy')->where('type', 'spare_parts')->latest()->limit(10)->get();

        return view('backend.spare-parts-import.create', compact('recentLogs'));
    }

    public function template()
    {
        if (is_null($this->user) || ! $this->user->can('spare-part.import')) {
            abort(403);
        }

        $headers = ['Part Number', 'SKU', 'Part Name', 'Category', 'Brand', 'OEM Number', 'Purchase Price', 'Selling Price', 'Opening Stock', 'Minimum Stock', 'Warehouse', 'Rack'];
        $sample = ['BP-1001', 'BRK-00045', 'Front Brake Pad', 'Brake Parts', 'Bosch', 'OEM-4521', '500', '750', '25', '10', 'Main Warehouse', 'A-05'];

        return response()->streamDownload(function () use ($headers, $sample) {
            $out = fopen('php://output', 'w');
            fputcsv($out, $headers);
            fputcsv($out, $sample);
            fclose($out);
        }, 'spare-parts-import-template.csv', ['Content-Type' => 'text/csv']);
    }

    /**
     * Section 35 steps 1-7, extended to also accept PDF supplier
     * estimates/invoices: Upload -> Parse -> Validate columns -> Resolve
     * each row against the existing catalogue (new part vs. restock of an
     * existing one) -> Validate numeric values -> Display errors -> Show
     * preview. Nothing is written to the database yet.
     */
    public function preview(Request $request)
    {
        if (is_null($this->user) || ! $this->user->can('spare-part.import')) {
            abort(403);
        }

        $request->validate([
            'file' => 'required|file|mimes:csv,txt,xlsx,xls,pdf|max:20480',
        ]);

        $file = $request->file('file');
        $extension = strtolower($file->getClientOriginalExtension());
        $sourceType = $extension === 'pdf' ? 'pdf' : 'sheet';

        if ($sourceType === 'pdf') {
            $rows = (new PdfEstimateParser())->parse($file->getRealPath());
        } else {
            $import = new SparePartsImport();
            Excel::import($import, $file);
            $rows = $import->rows;
        }

        if (empty($rows)) {
            $message = $sourceType === 'pdf'
                ? 'Couldn\'t find any part rows in this PDF. This importer reads tabular estimates/invoices with SN, Part No, Description, Qty, Rate, Net Rate, and Amount columns (like a VAS-style supplier estimate). Scanned/image-only PDFs or a very different layout can\'t be read automatically — try the CSV/Excel template instead.'
                : 'The file has no data rows, or its column headers don\'t match the template. Download the template and compare.';

            return back()->withErrors(['file' => $message]);
        }

        // Cap what we hold in the session — large files should be split.
        // 2,000 rows is generous for a single spare-parts import batch.
        if (count($rows) > 2000) {
            return back()->withErrors(['file' => 'This file has more than 2,000 rows. Please split it into smaller batches.']);
        }

        $seenInFile = []; // part_number/sku -> first row index, to catch duplicates within the file itself
        $partNumberSuffix = []; // base part number -> next suffix to try, for auto-deduping reused catalogue numbers
        $validRows = [];
        $errorRows = [];

        foreach ($rows as $i => $row) {
            $rowNum = $i + 2; // +1 for zero-index, +1 for the header row
            $errors = [];

            $partName = trim($row['part_name'] ?? '');
            $partNumber = trim($row['part_number'] ?? '');
            $sku = trim($row['sku'] ?? '');

            if ($partName === '') {
                $errors[] = 'Part Name is required.';
            }

            foreach (['purchase_price' => 'Purchase Price', 'selling_price' => 'Selling Price', 'opening_stock' => 'Opening Stock', 'minimum_stock' => 'Minimum Stock'] as $key => $label) {
                $val = $row[$key] ?? null;
                if ($val !== null && $val !== '' && ! is_numeric($val)) {
                    $errors[] = "$label (\"$val\") is not a valid number.";
                }
            }

            if ($sku !== '') {
                if (SparePart::where('sku', $sku)->exists()) {
                    $errors[] = "SKU \"$sku\" already exists in the system.";
                } elseif (isset($seenInFile['sku'][$sku])) {
                    $errors[] = "SKU \"$sku\" is duplicated on row {$seenInFile['sku'][$sku]} of this file.";
                } else {
                    $seenInFile['sku'][$sku] = $rowNum;
                }
            }

            // Decide what this row actually does: RESTOCK an existing part
            // (exact part number + name match — an existing catalogue item
            // getting more units in) or CREATE a new one. This is what lets
            // a supplier PDF both add brand-new parts *and* top up stock on
            // parts already in the system, in one import.
            $action = 'create';
            $matchedPart = null;
            $note = null;

            if ($errors === [] && $partNumber !== '') {
                $matchedPart = SparePart::whereRaw('LOWER(part_number) = ?', [Str::lower($partNumber)])
                    ->whereRaw('LOWER(name) = ?', [Str::lower($partName)])
                    ->first();

                if ($matchedPart) {
                    $action = 'restock';
                } else {
                    // Same part number already used by a *different* item —
                    // common in these supplier PDFs, where one catalogue
                    // number covers several colour/variant rows. Rather than
                    // erroring the row out, keep it unique with a suffix.
                    $taken = SparePart::where('part_number', $partNumber)->exists()
                        || isset($seenInFile['pn'][$partNumber]);

                    if ($taken) {
                        $original = $partNumber;
                        $n = $partNumberSuffix[$original] ?? 2;
                        $candidate = $original.'-'.$n;
                        while (SparePart::where('part_number', $candidate)->exists() || isset($seenInFile['pn'][$candidate])) {
                            $n++;
                            $candidate = $original.'-'.$n;
                        }
                        $partNumberSuffix[$original] = $n + 1;
                        $partNumber = $candidate;
                        $note = "Part number \"$original\" is already used by a different item — saving this variant as \"$partNumber\".";
                    }

                    $seenInFile['pn'][$partNumber] = $rowNum;
                }
            }

            $entry = [
                'row' => $rowNum,
                'action' => $action,
                'matched_part_id' => $matchedPart?->id,
                'matched_current_stock' => $matchedPart?->current_stock,
                'note' => $note,
                'part_number' => $partNumber,
                'sku' => $sku,
                'part_name' => $partName,
                'category' => trim($row['category'] ?? ''),
                'brand' => trim($row['brand'] ?? ''),
                'oem_number' => trim($row['oem_number'] ?? ''),
                'purchase_price' => is_numeric($row['purchase_price'] ?? null) ? (float) $row['purchase_price'] : 0,
                'selling_price' => is_numeric($row['selling_price'] ?? null) ? (float) $row['selling_price'] : 0,
                'opening_stock' => is_numeric($row['opening_stock'] ?? null) ? (int) $row['opening_stock'] : 0,
                'minimum_stock' => is_numeric($row['minimum_stock'] ?? null) ? (int) $row['minimum_stock'] : 0,
                'warehouse' => trim($row['warehouse'] ?? ''),
                'rack' => trim($row['rack'] ?? ''),
            ];

            if ($errors) {
                $errorRows[] = array_merge($entry, ['errors' => $errors]);
            } else {
                $validRows[] = $entry;
            }
        }

        $token = (string) Str::uuid();
        session([
            "import.$token" => [
                'filename' => $file->getClientOriginalName(),
                'source_type' => $sourceType,
                'valid_rows' => $validRows,
                'error_rows' => $errorRows,
                'total' => count($rows),
            ],
        ]);

        return view('backend.spare-parts-import.preview', [
            'token' => $token,
            'filename' => $file->getClientOriginalName(),
            'sourceType' => $sourceType,
            'validRows' => $validRows,
            'errorRows' => $errorRows,
            'total' => count($rows),
            'createCount' => count(array_filter($validRows, fn ($r) => $r['action'] === 'create')),
            'restockCount' => count(array_filter($validRows, fn ($r) => $r['action'] === 'restock')),
        ]);
    }

    /**
     * Section 35 steps 8-10: Confirm import -> Write records -> Generate
     * import log. Each valid row either creates a brand-new spare part
     * (with an opening-stock movement) or restocks an existing one matched
     * during preview (a PURCHASE movement) — either way, current_stock is
     * only ever touched through StockService, never written directly.
     * Error rows are simply left out (their reasons are in the import log).
     */
    public function confirm(Request $request)
    {
        if (is_null($this->user) || ! $this->user->can('spare-part.import')) {
            abort(403);
        }

        $token = $request->get('token');
        $data = session("import.$token");

        if (! $data) {
            session()->flash('error', 'This import session has expired. Please upload the file again.');
            return redirect()->route('admin.spare-parts.import.create');
        }

        $defaultWarehouse = Warehouse::default();
        $created = 0;
        $restocked = 0;

        DB::transaction(function () use ($data, $defaultWarehouse, &$created, &$restocked) {
            foreach ($data['valid_rows'] as $row) {
                $warehouse = $row['warehouse']
                    ? Warehouse::firstOrCreate(['name' => $row['warehouse']], ['code' => 'WH-'.Str::upper(Str::random(4))])
                    : $defaultWarehouse;

                if ($row['action'] === 'restock' && $row['matched_part_id']) {
                    $sparePart = SparePart::find($row['matched_part_id']);

                    // Matched part could theoretically have been deleted
                    // between preview and confirm — fall back to creating it
                    // fresh rather than silently dropping the row's stock.
                    if ($sparePart) {
                        $restockWarehouse = $warehouse ?: ($sparePart->warehouse_id ? Warehouse::find($sparePart->warehouse_id) : $defaultWarehouse);

                        if ($row['opening_stock'] > 0 && $restockWarehouse) {
                            $this->stockService->purchaseStock(
                                $sparePart,
                                $restockWarehouse,
                                $row['opening_stock'],
                                notes: "Restocked via import: {$data['filename']}"
                            );
                        }

                        if ($row['purchase_price'] > 0) {
                            $sparePart->purchase_price = $row['purchase_price'];
                            $sparePart->save();
                        }

                        $restocked++;
                        continue;
                    }
                }

                $category = $row['category'] ? Category::firstOrCreate(['name' => $row['category']]) : null;
                $brand = $row['brand'] ? Brand::firstOrCreate(['name' => $row['brand']]) : null;

                $sparePart = SparePart::create([
                    'part_number' => $row['part_number'] ?: null,
                    'sku' => $row['sku'] ?: null,
                    'oem_number' => $row['oem_number'] ?: null,
                    'name' => $row['part_name'],
                    'category_id' => $category?->id,
                    'brand_id' => $brand?->id,
                    'unit_id' => Unit::first()?->id,
                    'purchase_price' => $row['purchase_price'],
                    'retail_price' => $row['selling_price'],
                    'opening_stock' => $row['opening_stock'],
                    'minimum_stock' => $row['minimum_stock'],
                    'warehouse_id' => $warehouse?->id,
                    'status' => 'active',
                    'created_by' => $this->user->id,
                ]);

                // Auto-generate part number/SKU if the row left them blank —
                // same generator SparePartsController uses, kept in sync here.
                if (! $row['part_number']) {
                    $sparePart->part_number = 'PN-'.now()->format('ym').'-'.str_pad($sparePart->id, 5, '0', STR_PAD_LEFT);
                }
                if (! $row['sku']) {
                    $sparePart->sku = 'IMP-'.str_pad($sparePart->id, 5, '0', STR_PAD_LEFT);
                }
                $sparePart->save();

                if ($row['opening_stock'] > 0 && $warehouse) {
                    $this->stockService->openingStock($sparePart, $warehouse, $row['opening_stock'], 'Opening stock from bulk import');
                }

                $created++;
            }
        });

        $importLog = ImportLog::create([
            'type' => 'spare_parts',
            'source_type' => $data['source_type'] ?? 'sheet',
            'original_filename' => $data['filename'],
            'total_rows' => $data['total'],
            'imported_count' => $created,
            'restocked_count' => $restocked,
            'skipped_count' => count($data['error_rows']),
            'errors' => array_map(fn ($r) => ['row' => $r['row'], 'message' => implode(' ', $r['errors'])], $data['error_rows']),
            'created_by' => $this->user->id,
        ]);

        AuditLog::record(
            'import',
            'Spare Parts',
            $importLog,
            "Imported \"{$data['filename']}\": {$created} spare part(s) created, {$restocked} restocked, {$importLog->skipped_count} skipped",
        );

        session()->forget("import.$token");
        session()->flash('success', "Import complete: {$created} spare part(s) created, {$restocked} restocked (stock updated), {$importLog->skipped_count} row(s) skipped.");

        return redirect()->route('admin.spare-parts.index');
    }
}
