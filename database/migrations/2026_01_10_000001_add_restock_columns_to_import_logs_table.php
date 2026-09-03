<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Section 35 extension: importing can now also *restock* spare parts that
 * already exist (matched by part number + name) instead of only creating
 * new ones — most useful for PDF supplier estimates/invoices, which almost
 * always reference parts already in the catalogue. These columns let the
 * import log and its UI distinguish "created" from "restocked" instead of
 * lumping both into imported_count, and record whether a batch came from a
 * spreadsheet or a PDF.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('import_logs', function (Blueprint $table) {
            $table->unsignedInteger('restocked_count')->default(0)->after('imported_count');
            $table->string('source_type', 20)->default('sheet')->after('type'); // 'sheet' | 'pdf'
        });
    }

    public function down(): void
    {
        Schema::table('import_logs', function (Blueprint $table) {
            $table->dropColumn(['restocked_count', 'source_type']);
        });
    }
};
