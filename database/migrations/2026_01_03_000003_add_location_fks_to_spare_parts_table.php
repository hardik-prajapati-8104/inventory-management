<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Phase 2 shipped spare_parts.warehouse/rack/shelf/bin as plain text so data
 * entry could start immediately. Now that the real hierarchy exists, this adds
 * proper foreign keys. The old text columns are kept (nulled out going forward)
 * rather than dropped, so nothing is lost for parts entered before this
 * migration — see MigrateLegacyLocations command below for backfill.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('spare_parts', function (Blueprint $table) {
            $table->foreignId('warehouse_id')->nullable()->after('bin')->constrained('warehouses')->nullOnDelete();
            $table->foreignId('rack_id')->nullable()->after('warehouse_id')->constrained('racks')->nullOnDelete();
            $table->foreignId('shelf_id')->nullable()->after('rack_id')->constrained('shelves')->nullOnDelete();
            $table->foreignId('bin_id')->nullable()->after('shelf_id')->constrained('bins')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('spare_parts', function (Blueprint $table) {
            $table->dropConstrainedForeignId('bin_id');
            $table->dropConstrainedForeignId('shelf_id');
            $table->dropConstrainedForeignId('rack_id');
            $table->dropConstrainedForeignId('warehouse_id');
        });
    }
};
