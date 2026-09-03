<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Section 42: every stock-changing operation in the entire system — purchases,
 * sales, returns, transfers, adjustments, damage, opening stock — creates a row
 * here. Nothing ever UPDATEs a quantity in place; current_stock in `stock` is
 * always recomputed from (or incrementally derived alongside) this ledger, the
 * same architectural rule AuditLog enforces for admin actions in Phase 1.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('spare_part_id')->constrained('spare_parts')->cascadeOnDelete();
            $table->foreignId('warehouse_id')->constrained('warehouses')->cascadeOnDelete();

            $table->enum('type', [
                'OPENING_STOCK',
                'PURCHASE',
                'SALE',
                'PURCHASE_RETURN',
                'SALES_RETURN',
                'TRANSFER_IN',
                'TRANSFER_OUT',
                'ADJUSTMENT_IN',
                'ADJUSTMENT_OUT',
                'DAMAGE',
            ]);

            // Signed: positive increases current_stock, negative decreases it.
            $table->integer('quantity');
            $table->integer('stock_before');
            $table->integer('stock_after');

            // Polymorphic reference to whatever created this movement
            // (StockAdjustment, StockTransfer, and later Purchase/Sale/Return).
            $table->string('reference_type')->nullable();
            $table->unsignedBigInteger('reference_id')->nullable();

            $table->string('notes', 255)->nullable();
            $table->foreignId('created_by')->nullable()->constrained('admins')->nullOnDelete();
            $table->timestamps();

            $table->index(['spare_part_id', 'warehouse_id']);
            $table->index(['reference_type', 'reference_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_movements');
    }
};
