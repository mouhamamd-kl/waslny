<?php

use App\Models\Trip;
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
        Schema::create('trip_locations', function (Blueprint $table) {
            $table->id();
            $table->magellanPoint('location', 4326);
            $table->smallInteger('location_order');
            $table->enum('location_type', ['pickup', 'dropoff','stop']);

            $table->timestamp('estimated_arrival_time'); // When trip is accepted
            $table->timestamp('actual_arrival_time')->nullable(); // When trip completes


            $table->boolean('is_completed');
            $table->foreignIdFor(Trip::class);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trip_locations');
    }
};
