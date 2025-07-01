<?php

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
        Schema::create('rider_folders', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignIdFor(Rider::class)->onDelete('cascade');
            $table->timestamps();
            $table->unique(['rider_id', 'name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rider_folders');
    }
};
