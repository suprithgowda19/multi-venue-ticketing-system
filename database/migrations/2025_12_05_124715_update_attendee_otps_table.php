<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('attendee_otps', function (Blueprint $table) {

            // Link OTP to attendee (if not exists)
            if (!Schema::hasColumn('attendee_otps', 'attendee_id')) {
                $table->foreignId('attendee_id')
                    ->nullable()
                    ->after('id')
                    ->constrained('attendees')
                    ->cascadeOnDelete();
            }

            // OTP purpose: registration / booking / etc.
            if (!Schema::hasColumn('attendee_otps', 'purpose')) {
                $table->string('purpose')->after('mobile')->nullable();
            }

            // Hashed OTP storage
            if (!Schema::hasColumn('attendee_otps', 'otp_hash')) {
                $table->string('otp_hash')->after('purpose')->nullable();
            }

            // OTP expiry time
            if (!Schema::hasColumn('attendee_otps', 'expires_at')) {
                $table->dateTime('expires_at')->after('otp_hash')->nullable();
            }

            // Number of wrong attempts
            if (!Schema::hasColumn('attendee_otps', 'attempts')) {
                $table->integer('attempts')->default(0)->after('expires_at');
            }

            // Whether OTP is already used
            if (!Schema::hasColumn('attendee_otps', 'used')) {
                $table->boolean('used')->default(false)->after('attempts');
            }

            // Store request IP for security/logging
            if (!Schema::hasColumn('attendee_otps', 'request_ip')) {
                $table->string('request_ip')->nullable()->after('used');
            }
        });
    }

    public function down(): void
    {
        Schema::table('attendee_otps', function (Blueprint $table) {
            if (Schema::hasColumn('attendee_otps', 'attendee_id')) {
                $table->dropForeign(['attendee_id']);
                $table->dropColumn('attendee_id');
            }

            if (Schema::hasColumn('attendee_otps', 'purpose')) {
                $table->dropColumn('purpose');
            }

            if (Schema::hasColumn('attendee_otps', 'otp_hash')) {
                $table->dropColumn('otp_hash');
            }

            if (Schema::hasColumn('attendee_otps', 'expires_at')) {
                $table->dropColumn('expires_at');
            }

            if (Schema::hasColumn('attendee_otps', 'attempts')) {
                $table->dropColumn('attempts');
            }

            if (Schema::hasColumn('attendee_otps', 'used')) {
                $table->dropColumn('used');
            }

            if (Schema::hasColumn('attendee_otps', 'request_ip')) {
                $table->dropColumn('request_ip');
            }
        });
    }
};
