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
        // Schema::table('trips', function (Blueprint $table) {
        //     $table->dropColumn(['distance', 'fare']);
        // });

        // Schema::table('trips', function (Blueprint $table) {
        //     $table->decimal('distance', 10, 2)->nullable();
        //     $table->decimal('fare', 10, 2)->nullable();
        //     $table->decimal('expected_fare', 10, 2)->nullable();
        // });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Schema::table('trips', function (Blueprint $table) {
        //     $table->dropColumn(['distance', 'fare', 'expected_fare']);
        // });

        // Schema::table('trips', function (Blueprint $table) {
        //     $table->bigInteger('distance')->nullable();
        //     $table->bigInteger('fare')->nullable();
        // });
    }
};
