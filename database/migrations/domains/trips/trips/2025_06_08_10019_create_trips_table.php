<?php

use App\Models\CarServiceLevel;
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

            $table->decimal('distance', 10, 2)->nullable();
            $table->decimal('fare', 10, 2)->nullable();
            $table->decimal('expected_fare', 10, 2)->nullable();

            $table->unsignedInteger('driver_search_radius')->default(5000); // meters (5km)
            $table->timestamp('search_started_at')->nullable();
            $table->timestamp('search_expires_at')->nullable();

            $table->timestamp('approaching_pickup_notified_at')->nullable();
            $table->foreignIdFor(CarServiceLevel::class)->onDelete('cascade');

            $table->unsignedTinyInteger('rider_rating')->nullable()->after('fare')->check('rider_rating >= 1 AND rider_rating <= 5');
            $table->text('rider_review_notes')->nullable()->after('rider_rating');
            $table->unsignedTinyInteger('driver_rating')->nullable()->after('rider_review_notes')->check('driver_rating >= 1 AND driver_rating <= 5');
            $table->text('driver_review_notes')->nullable()->after('driver_rating');
            $table->bigInteger('tip_amount')->nullable()->after('driver_review_notes');
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
