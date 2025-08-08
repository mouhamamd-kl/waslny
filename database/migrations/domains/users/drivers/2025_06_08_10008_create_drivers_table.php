<?php

use App\Enums\SuspensionReason;
use App\Models\DriverStatus;
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
        Schema::create('drivers', function (Blueprint $table) {
            $table->id();

            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->enum('gender', ['male', 'female'])->nullable();
            $table->string('national_number')->unique()->nullable(); // Unique phone numbers
            $table->string('phone')->unique(); // Unique phone numbers
            $table->string('email')->unique()->nullable(); // Unique email addresses
            $table->longText('profile_photo')->nullable(); // Nullable, for profile picture or file path
            $table->longText('driver_license_photo')->nullable();

            $table->double('rating')
                ->default(5.0)
                ->check('rating >= 0 AND rating <= 5'); // PostgreSQL CHECK constraint

            $table->magellanPoint('location', 4326)->nullable();


            $table->foreignIdFor(DriverStatus::class)->nullable();

            // $table->boolean('suspended')->default(true);
            // $table->enum('suspension_reason', SuspensionReason::values())
            //     ->default(SuspensionReason::NEED_REVIEW->value)
            //     ->nullable();

            $table->timestamp('two_factor_expires_at')->nullable();
            $table->char('two_factor_code', length: 6)->nullable();

            $table->rememberToken()->nullable();
            $table->timestamps();

            $table->spatialIndex('location'); // Spatial index for location
            $table->index('driver_status_id'); // Index for status
            $table->index('rating'); // Index for rating
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('drivers');
    }
};
