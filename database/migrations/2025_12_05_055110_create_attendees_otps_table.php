<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendee_otps', function (Blueprint $table) {
            $table->id();

            // Identity: either email or mobile (we store both to be flexible)
            $table->string('email')->nullable()->index();
            $table->string('mobile')->nullable()->index();

            // Purpose (registration, login, verify_mobile, entry_gate, admin_login, etc.)
            $table->string('purpose')->default('registration')->index();

            // Hashed OTP + security fields
            $table->string('otp_hash');
            $table->timestamp('expires_at');
            $table->unsignedTinyInteger('attempts')->default(0);

            $table->timestamps();

            // Composite index for fast lookup by identity + purpose
            $table->index(['email', 'purpose', 'created_at']);
            $table->index(['mobile', 'purpose', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendee_otps');
    }
};
