<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('spare_part_vehicle', function (Blueprint $table) {
            $table->id();
            $table->foreignId('spare_part_id')->constrained('spare_parts')->cascadeOnDelete();
            $table->foreignId('vehicle_variant_id')->constrained('vehicle_variants')->cascadeOnDelete();
            $table->string('oem_number', 60)->nullable();
            $table->enum('position', [
                'Front', 'Rear', 'Left', 'Right',
                'Front Left', 'Front Right', 'Rear Left', 'Rear Right', 'Universal',
            ])->default('Universal');
            $table->string('notes', 255)->nullable();
            $table->timestamps();

            $table->unique(['spare_part_id', 'vehicle_variant_id', 'position'], 'spare_part_vehicle_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('spare_part_vehicle');
    }
};
