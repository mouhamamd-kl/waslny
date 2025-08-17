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
            $table->unsignedInteger('driver_search_radius')->default(5000); // meters (5km)
            $table->timestamp('search_started_at')->nullable();
            $table->timestamp('search_expires_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('trips', function (Blueprint $table) {
            $table->dropColumn('driver_search_radius'); // meters (5km)
            $table->dropColumn('search_started_at')->nullable();
            $table->dropColumn('search_expires_at')->nullable();
        });
    }
};
