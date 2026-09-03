<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('warehouse_zones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('warehouse_id')->constrained('warehouses')->cascadeOnDelete();
            $table->string('name', 60); // e.g. "Engine Zone"
            $table->boolean('status')->default(1);
            $table->timestamps();
        });

        Schema::create('racks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('warehouse_id')->constrained('warehouses')->cascadeOnDelete();
            $table->foreignId('warehouse_zone_id')->nullable()->constrained('warehouse_zones')->nullOnDelete();
            $table->string('name', 40); // e.g. "A-05"
            $table->boolean('status')->default(1);
            $table->timestamps();
        });

        Schema::create('shelves', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rack_id')->constrained('racks')->cascadeOnDelete();
            $table->string('name', 40); // e.g. "Shelf 03"
            $table->boolean('status')->default(1);
            $table->timestamps();
        });

        Schema::create('bins', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shelf_id')->constrained('shelves')->cascadeOnDelete();
            $table->string('name', 40); // e.g. "Bin 12"
            $table->boolean('status')->default(1);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bins');
        Schema::dropIfExists('shelves');
        Schema::dropIfExists('racks');
        Schema::dropIfExists('warehouse_zones');
    }
};
