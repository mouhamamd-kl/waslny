<?php

use App\Models\CarManufacturer;
use App\Models\CarServiceLevel;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('car_models', function (Blueprint $table) {
            $table->id();

            $table->foreignIdFor(CarManufacturer::class)
                ->onDelete('cascade');
            $table->foreignIdFor(CarServiceLevel::class)->onDelete('cascade');

            $table->string('name');
            $table->boolean('is_active')->default(true);


            $table->year('model_year');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('car_models');
    }
};
