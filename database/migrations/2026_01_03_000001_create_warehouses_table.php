<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('warehouses', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('code', 20)->unique();
            $table->string('manager', 100)->nullable();
            $table->string('contact_number', 30)->nullable();
            $table->text('address')->nullable();
            $table->string('city', 60)->nullable();
            $table->string('country', 60)->nullable();
            $table->boolean('is_default')->default(false);
            $table->boolean('status')->default(1);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('warehouses');
    }
};
