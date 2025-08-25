5<?php

    use App\Enums\SuspensionReason;
    use App\Models\PaymentMethod;
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
            Schema::create('riders', function (Blueprint $table) {
                $table->id();
                $table->string('first_name')->nullable();
                $table->string('last_name')->nullable();
                $table->string('location')->nullable();


                $table->string('phone')->unique(); // Unique phone numbers
                $table->string('email')->unique()->nullable(); // Unique email addresses

                $table->rememberToken();

                $table->longText('profile_photo')->nullable(); // Nullable, for profile picture or file path

                $table->foreignIdFor(PaymentMethod::class)->nullable()
                    ->constrained('payment_methods')
                    ->onDelete('cascade');

                $table->double('rating')
                    ->default(5.0)
                    ->check('rating >= 0 AND rating <= 5'); // PostgreSQL CHECK constraint
                $table->timestamp('two_factor_expires_at')->nullable();
                $table->char('two_factor_code', length: 6)->nullable();

                $table->timestamp('last_seen_at')->nullable()->after('rememberToken');
                // two_factor_code CHAR(6),
                // two_factor_expires_at TIMESTAMP,
                // two_factor_enabled BOOLEAN DEFAULT false,
                $table->timestamps();
            });
        }

        /**
         * Reverse the migrations.
         */
        public function down(): void
        {
            Schema::dropIfExists('riders');
        }
    };
