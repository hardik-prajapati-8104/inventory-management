<?php

namespace Tests\Unit;

use App\Services\Import\PdfEstimateParser;
use PHPUnit\Framework\TestCase;

/**
 * Exercises PdfEstimateParser::parseText() directly against strings shaped
 * like smalot/pdfparser's ->getText() output, so these run without needing a
 * real PDF file, a database, or the Laravel framework at all.
 */
class PdfEstimateParserTest extends TestCase
{
    private PdfEstimateParser $parser;

    protected function setUp(): void
    {
        parent::setUp();
        $this->parser = new PdfEstimateParser();
    }

    public function test_parses_a_well_formed_line_item(): void
    {
        $rows = $this->parser->parseText(
            '1 APFL-28 FLOR — ACTIVA — WHITE ACTIVA N/ AP GO 2 750.00 750.00 1500.00'
        );

        $this->assertCount(1, $rows);
        $this->assertSame('APFL-28', $rows[0]['part_number']);
        $this->assertSame('FLOR — ACTIVA — WHITE ACTIVA N/ AP GO', $rows[0]['part_name']);
        $this->assertSame(2, $rows[0]['opening_stock']);
        $this->assertSame(750.0, $rows[0]['purchase_price']);
        $this->assertSame(0, $rows[0]['selling_price']);
    }

    public function test_ignores_header_footer_and_summary_lines(): void
    {
        $text = implode("\n", [
            'VEER AUTO SALES',
            'V.A.S. E S T I M A T E',
            'M/S PAVAN AUTO PARTS NO. :SA-01074 DT.:22/08/2026',
            'SN. PART NO PRODUCT DISCRIPTION VEHICLE COMP QTY RATE NET RATE AMOUNT',
            '1 APFL-28 FLOR — ACTIVA — WHITE ACTIVA N/ AP GO 2 750.00 750.00 1500.00',
            'Cont.Page.No: 2',
            ': 0.00',
            'Rupees Forty-One Thousand Forty Only',
            'NET AMOUNT 41040',
        ]);

        $rows = $this->parser->parseText($text);

        $this->assertCount(1, $rows);
        $this->assertSame('APFL-28', $rows[0]['part_number']);
    }

    public function test_parses_multiple_rows_from_the_sample_estimate(): void
    {
        $text = implode("\n", [
            '1 APFL-28 FLOR — ACTIVA — WHITE ACTIVA N/ AP GO 2 750.00 750.00 1500.00',
            '7 APFM-28 FRONT MUJGARD — ACTIVA (N/M) (M.GRAY) ACTIVA N/ AP GO 2 580.00 580.00 1160.00',
            '23 APSW-89 SIDE WING-ACTIVA (3G) (BROWN) ACTIVA 3G AP GO 1 1500.00 1500.00 1500.00',
        ]);

        $rows = $this->parser->parseText($text);

        $this->assertCount(3, $rows);
        $this->assertSame(['APFL-28', 'APFM-28', 'APSW-89'], array_column($rows, 'part_number'));
        $this->assertSame([2, 2, 1], array_column($rows, 'opening_stock'));
    }

    public function test_rejects_a_line_whose_amount_does_not_match_qty_times_rate(): void
    {
        // Same shape as a real row, but the trailing amount is nonsense —
        // this should never happen on a real invoice, but if a line wraps
        // oddly and produces this, it must not be imported as-is.
        $rows = $this->parser->parseText(
            '1 APFL-28 FLOR — ACTIVA — WHITE 2 750.00 750.00 999999.00'
        );

        $this->assertCount(0, $rows);
    }

    public function test_ignores_blank_lines(): void
    {
        $rows = $this->parser->parseText("\n\n   \n");

        $this->assertCount(0, $rows);
    }
}
