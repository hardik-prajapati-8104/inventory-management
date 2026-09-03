<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;

/**
 * Pure parsing only — deliberately does NOT write to the database. Section 35's
 * flow is Upload -> Validate -> Preview -> Confirm -> Insert, so the actual
 * database writes happen in SparePartsImportController::confirm(), after the
 * person has reviewed the preview. This class's only job is turning the sheet
 * into an array of associative rows keyed by (snake_cased) column header.
 *
 * Expected columns (Section 35), header names are case/spacing-insensitive
 * thanks to WithHeadingRow's normalization:
 *   Part Number | SKU | Part Name | Category | Brand | OEM Number |
 *   Purchase Price | Selling Price | Opening Stock | Minimum Stock |
 *   Warehouse | Rack
 */
class SparePartsImport implements ToCollection, WithHeadingRow, SkipsEmptyRows
{
    public array $rows = [];

    public function collection(Collection $rows)
    {
        $this->rows = $rows->map(fn ($row) => $row->toArray())->toArray();
    }
}
