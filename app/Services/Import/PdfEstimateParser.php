<?php

namespace App\Services\Import;

use Smalot\PdfParser\Parser as PdfTextParser;

/**
 * Parses supplier "estimate" / invoice-style PDFs that list spare parts in a
 * tabular layout:
 *
 *   SN | Part No | Description (free text, may include vehicle/colour/comp) | Qty | Rate | Net Rate | Amount
 *
 * — e.g. the "V.A.S. ESTIMATE" PDF format used by Veer Auto Sales. Only the
 * fixed-format trailing numeric block is depended on (an integer quantity
 * followed by three 2-decimal money values); everything between the part
 * number and that block — description, vehicle model, component codes — is
 * captured as one free-text string. That's deliberate: it means colour/
 * variant text (e.g. "FLOOR-ACTIVA (N/M) (M.GRAY)") naturally becomes part
 * of the item name instead of being lost, which is exactly what lets two
 * rows sharing the same catalogue part number be told apart later.
 *
 * Any line that doesn't match the pattern — the company header, address,
 * "Cont.Page.No", the "NET AMOUNT" summary line, blank lines, etc. — simply
 * doesn't produce a row. There is no header/footer stripping step because
 * none is needed: nothing else in a normal estimate happens to match
 * "<int> <token> <text with a letter> <int> <d.dd> <d.dd> <d.dd>" at both
 * ends of the same line.
 */
class PdfEstimateParser
{
    /**
     * @return array<int, array<string, mixed>> rows shaped exactly like
     *   SparePartsImport::$rows, so SparePartsImportController can run both
     *   sources through one shared validation/preview pipeline.
     */
    public function parse(string $absolutePath): array
    {
        $document = (new PdfTextParser())->parseFile($absolutePath);

        return $this->parseText((string) $document->getText());
    }

    /**
     * The line-parsing half of parse(), split out so it can be unit tested
     * against plain strings without needing to generate a real PDF file on
     * disk — smalot/pdfparser's extraction step is the only part that
     * genuinely needs a PDF.
     *
     * @return array<int, array<string, mixed>>
     */
    public function parseText(string $text): array
    {
        $lines = preg_split('/\r\n|\r|\n/', $text) ?: [];
        $rows = [];

        foreach ($lines as $line) {
            $line = trim(preg_replace('/\s+/', ' ', $line) ?? '');

            if ($line === '') {
                continue;
            }

            $row = $this->parseLine($line);

            if ($row !== null) {
                $rows[] = $row;
            }
        }

        return $rows;
    }

    private function parseLine(string $line): ?array
    {
        // ^SN  PARTNO  <description...>  QTY  RATE  NET_RATE  AMOUNT$
        // SN and PART NO are anchored to the start (first two tokens);
        // QTY/RATE/NET_RATE/AMOUNT are anchored to the end (an integer
        // followed by three values with exactly 2 decimal places).
        $pattern = '/^(\d+)\s+(\S+)\s+(.+?)\s+(\d+)\s+([\d,]+\.\d{2})\s+([\d,]+\.\d{2})\s+([\d,]+\.\d{2})$/';

        if (! preg_match($pattern, $line, $m)) {
            return null;
        }

        [, , $partNo, $description, $qty, $rate, , $amount] = $m;

        // A real line-item description always has letters in it. This guards
        // against a stray all-numeric line (e.g. a totals row split across
        // wrapping) accidentally satisfying the pattern above.
        if (! preg_match('/[A-Za-z]/', $description)) {
            return null;
        }

        $qtyInt = (int) $qty;
        $rateFloat = (float) str_replace(',', '', $rate);
        $amountFloat = (float) str_replace(',', '', $amount);

        // Sanity check: qty * rate should roughly equal amount (allow small
        // rounding slack). If it's wildly off, the line likely isn't really
        // a part row (e.g. a wrapped/merged line) — skip it rather than
        // import garbage.
        if ($qtyInt > 0 && $rateFloat > 0) {
            $expected = $qtyInt * $rateFloat;
            $diff = abs($expected - $amountFloat);
            if ($diff > max(1.0, $expected * 0.02)) {
                return null;
            }
        }

        return [
            'part_number' => trim($partNo),
            'sku' => '',
            'part_name' => trim($description),
            'category' => '',
            'brand' => '',
            'oem_number' => '',
            'purchase_price' => $rateFloat,
            'selling_price' => 0,
            'opening_stock' => $qtyInt,
            'minimum_stock' => 0,
            'warehouse' => '',
            'rack' => '',
        ];
    }
}
