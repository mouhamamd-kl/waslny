<?php

use App\Models\Coupon;
use App\Models\Driver;
use App\Models\PaymentMethod;
use App\Models\Rider;
use App\Models\RiderCoupon;
use App\Models\TripStatus;
use App\Models\TripTimeType;
use App\Models\TripType;
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
        Schema::create('trips', function (Blueprint $table) {
            $table->id();

            $table->foreignIdFor(Driver::class);
            $table->foreignIdFor(Rider::class);

            $table->foreignIdFor(TripStatus::class);
            $table->foreignIdFor(TripType::class);
            $table->foreignIdFor(TripTimeType::class);

            $table->foreignIdFor(RiderCoupon::class)->nullable();

            $table->foreignIdFor(PaymentMethod::class);

            $table->timestamp('start_time')->nullable();
            $table->timestamp('end_time')->nullable();

            $table->bigInteger('distance');
            $table->bigInteger('fare');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trips');
    }
};
