<?php

use App\Models\Driver;
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
        Schema::create('trip_driver_notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Trip::class);
            $table->foreignIdFor(Driver::class);
            $table->timestamp('sent_at');
            $table->timestamps();
            $table->unique(['trip_id', 'driver_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trip_driver_notifications');
    }
};
