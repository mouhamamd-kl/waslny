<?php

use App\Models\Car;
use App\Models\Driver;
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
        Schema::create('driver_car', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Car::class)->onDelete('cascade');
            $table->foreignIdFor(Driver::class)->onDelete('cascade');

            $table->string('front_photo');
            $table->string('back_photo');
            $table->string('left_photo');
            $table->string('right_photo');
            $table->string('inside_photo');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
