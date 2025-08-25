<?php

use App\Models\Rider;
use App\Models\RiderFolder;
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
        Schema::create('rider_saved_locations', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Rider::class)
                ->constrained('riders')
                ->onDelete('cascade');
            $table->foreignIdFor(RiderFolder::class)
                ->constrained('rider_folders')
                ->onDelete('cascade')->nullable();
            $table->magellanPoint('location', 4326);

            // Spatial index for performance
            $table->spatialIndex('location');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rider_saved_locations');
    }
};
