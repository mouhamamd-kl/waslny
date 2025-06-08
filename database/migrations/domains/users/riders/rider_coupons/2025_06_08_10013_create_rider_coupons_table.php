<?php

use App\Models\Coupon;
use App\Models\Rider;
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
        Schema::create('rider_coupons', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Coupon::class)->onDelete('cascade');
            $table->foreignIdFor(Rider::class)->onDelete('cascade');
            $table->timestamp('used_at')->nullable(); // Track when coupon was used
            $table->timestamps();
            $table->unique(['coupon_id', 'rider_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rider_coupons');
    }
};
