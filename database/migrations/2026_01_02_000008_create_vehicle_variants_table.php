<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vehicle_variants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicle_model_id')->constrained('vehicle_models')->cascadeOnDelete();
            $table->string('name', 60); // 1.6L, 1.8L, 2.0L...
            $table->string('generation', 40)->nullable();
            $table->string('engine_type', 60)->nullable();
            $table->string('engine_capacity', 20)->nullable();
            $table->enum('fuel_type', ['Petrol', 'Diesel', 'Hybrid', 'Electric', 'CNG', 'LPG'])->nullable();
            $table->enum('transmission', ['Manual', 'Automatic', 'CVT'])->nullable();
            $table->enum('drive_type', ['FWD', 'RWD', 'AWD', '4WD'])->nullable();
            $table->unsignedSmallInteger('start_year')->nullable();
            $table->unsignedSmallInteger('end_year')->nullable();
            $table->boolean('status')->default(1);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vehicle_variants');
    }
};
