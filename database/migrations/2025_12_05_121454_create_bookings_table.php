<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();

            // Who booked
            $table->foreignId('attendee_id')->constrained()->cascadeOnDelete();

            // Which showtime (movie + screen + slot + day)
            $table->foreignId('assignment_id')
                ->constrained('screen_slot_assignments')
                ->cascadeOnDelete();

            // Total amount for the booking
            $table->decimal('total_amount', 10, 2)->default(0);

            // Currency, default INR
            $table->string('currency', 10)->default('INR');

            // Booking status: pending, paid, failed, refunded, cancelled
            $table->string('status', 20)->default('pending');

            // Reference returned by payment gateway
            $table->string('payment_reference')->nullable()->index();

            // QR code file path (S3 or storage)
            $table->string('qr_path')->nullable();

            // Raw gateway response, device info, discounts, etc.
            $table->json('metadata')->nullable();

            $table->timestamps();

            // Indexes for reporting & performance
            $table->index(['assignment_id', 'status']);
            $table->index('attendee_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
