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
        Schema::create('admins', function (Blueprint $table) {
            $table->id();
            $table->string('user_name')->nullable();
            $table->string('email')->unique()->nullable(); // Unique email addresses
            $table->string('phone')->unique();
            $table->longText('profile_photo')->nullable(); // Nullable, for profile picture or file path
            $table->timestamp('two_factor_expires_at')->nullable();
            $table->char('two_factor_code', length: 6);
            $table->boolean('two_factor_enabled')->default(true);;
            $table->string('password'); // Encrypted password
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admins');
    }
};
