<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * The public catalogue (Section 50) should be opt-in per part, not automatic —
 * a business might have hundreds of internal-only parts they never want
 * showing up on a public page. Everything the catalogue needs (slug,
 * seo_title, seo_description, keywords) was already added back in Phase 2's
 * Tab 6, built explicitly for this.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('spare_parts', function (Blueprint $table) {
            $table->boolean('is_published')->default(false)->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('spare_parts', function (Blueprint $table) {
            $table->dropColumn('is_published');
        });
    }
};
