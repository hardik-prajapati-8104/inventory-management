<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('spare_parts', function (Blueprint $table) {
            $table->id();

            // Basic Information
            $table->string('part_number', 60)->unique();
            $table->string('sku', 60)->unique();
            $table->string('barcode', 60)->unique()->nullable();
            $table->string('oem_number', 60)->nullable()->index();
            $table->string('alternate_number', 60)->nullable();
            $table->string('name', 180)->index();
            $table->string('short_description', 255)->nullable();
            $table->text('description')->nullable();

            // Classification
            $table->foreignId('category_id')->nullable()->constrained('categories')->nullOnDelete();
            $table->foreignId('sub_category_id')->nullable()->constrained('categories')->nullOnDelete();
            $table->foreignId('brand_id')->nullable()->constrained('brands')->nullOnDelete();
            $table->foreignId('manufacturer_id')->nullable()->constrained('manufacturers')->nullOnDelete();
            $table->foreignId('unit_id')->nullable()->constrained('units')->nullOnDelete();
            $table->string('part_type', 60)->nullable();

            // Pricing (weighted-average purchase cost lives in stock_movements from
            // Phase 3 onward; this purchase_price is the *last known* / default cost
            // used to pre-fill new Purchase lines)
            $table->decimal('purchase_price', 12, 2)->default(0);
            $table->decimal('wholesale_price', 12, 2)->default(0);
            $table->decimal('retail_price', 12, 2)->default(0);
            $table->decimal('min_selling_price', 12, 2)->default(0);
            $table->decimal('max_selling_price', 12, 2)->nullable();
            $table->decimal('tax_percentage', 5, 2)->default(0);
            $table->decimal('discount_percentage', 5, 2)->default(0);

            // Inventory (per-warehouse quantities move to `stock` in Phase 3;
            // these columns seed the opening_stock movement and act as a
            // denormalized total for fast list/search display)
            $table->integer('opening_stock')->default(0);
            $table->integer('current_stock')->default(0);
            $table->integer('minimum_stock')->default(0);
            $table->integer('maximum_stock')->nullable();
            $table->integer('reorder_level')->default(0);
            $table->integer('reserved_stock')->default(0);
            $table->integer('damaged_stock')->default(0);

            // Location (plain text until Phase 3 normalizes warehouses/racks/shelves/bins)
            $table->string('warehouse', 100)->nullable();
            $table->string('rack', 50)->nullable();
            $table->string('shelf', 50)->nullable();
            $table->string('bin', 50)->nullable();

            // Media
            $table->string('main_image')->nullable();

            // SEO (Section 45 Tab 6, for future public catalogue)
            $table->string('slug', 220)->unique()->nullable();
            $table->string('seo_title', 180)->nullable();
            $table->string('seo_description', 255)->nullable();
            $table->string('keywords', 255)->nullable();

            // Status
            $table->enum('status', ['active', 'inactive', 'discontinued'])->default('active');

            $table->foreignId('created_by')->nullable()->constrained('admins')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('spare_parts');
    }
};
