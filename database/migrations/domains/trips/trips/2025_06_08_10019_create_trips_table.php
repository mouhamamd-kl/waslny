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

            $table->foreignIdFor(Driver::class)->nullable();
            $table->foreignIdFor(Rider::class);

            $table->foreignIdFor(TripStatus::class)->nullable();
            $table->foreignIdFor(TripType::class);
            $table->foreignIdFor(TripTimeType::class);

            $table->foreignIdFor(RiderCoupon::class)->nullable();

            $table->foreignIdFor(PaymentMethod::class);

            $table->timestamp('requested_time')->nullable();

            $table->json('status_timeline')->nullable()->after('trip_status_id');

            $table->timestamp('start_time')->nullable();
            $table->timestamp('end_time')->nullable();

            $table->magellanPoint('current_location', 4326)->nullable();

            $table->bigInteger('distance')->nullable();
            $table->bigInteger('fare')->nullable();

            $table->unsignedInteger('driver_search_radius')->default(5000); // meters (5km)
            $table->timestamp('search_started_at')->nullable();
            $table->timestamp('search_expires_at')->nullable();

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
