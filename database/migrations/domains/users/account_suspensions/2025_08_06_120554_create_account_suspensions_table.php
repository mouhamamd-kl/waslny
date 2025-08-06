<?php

use App\Models\Suspension;
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
        Schema::create('account_suspensions', function (Blueprint $table) {
            $table->id();
            $table->morphs('suspendable');  // Creates suspendable_id + suspendable_type
            $table->foreignIdFor(Suspension::class)->constrained('driver_statuses')
                ->onDelete('cascade');
            $table->timestamp('lifted_at')->nullable();
            $table->boolean('is_permanent')->default(false); // New flag
            $table->timestamp('suspended_until')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('account_suspensions');
    }
};
