<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendees', function (Blueprint $table) {
            $table->id();

            $table->string('name');
            $table->string('email')->unique();       // Identity for OTP + login
            $table->string('mobile')->unique();      // For SMS/WhatsApp support
            $table->string('category');              // Delegate / Public / Student

            $table->string('pass_id')->unique()->nullable(); // Festival pass ID
            $table->string('qr_path')->nullable();   // Stored QR image file path
            $table->boolean('is_verified')->default(false); // OTP verification flag

            $table->json('meta')->nullable();        // Future expansion

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendees');
    }
};
