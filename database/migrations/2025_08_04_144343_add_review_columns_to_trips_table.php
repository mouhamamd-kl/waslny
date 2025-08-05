<?php

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
        Schema::table('trips', function (Blueprint $table) {
            $table->unsignedTinyInteger('rider_rating')->nullable()->after('fare');
            $table->text('rider_review_notes')->nullable()->after('rider_rating');
            $table->unsignedTinyInteger('driver_rating')->nullable()->after('rider_review_notes');
            $table->text('driver_review_notes')->nullable()->after('driver_rating');
            $table->bigInteger('tip_amount')->nullable()->after('driver_review_notes');

            $table->check('rider_rating >= 1 AND rider_rating <= 5');
            $table->check('driver_rating >= 1 AND driver_rating <= 5');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('trips', function (Blueprint $table) {
            $table->dropColumn([
                'rider_rating',
                'rider_review_notes',
                'driver_rating',
                'driver_review_notes',
                'tip_amount',
            ]);
        });
    }
};
