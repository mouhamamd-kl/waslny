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
        Schema::create('money_codes', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->decimal('value', 10, 2);
            $table->foreignIdFor(Rider::class)->nullable()->constrained()->onDelete('set null');
            $table->timestamp('used_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('money_codes');
    }
};
