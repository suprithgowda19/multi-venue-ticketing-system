<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('booking_seats', function (Blueprint $table) {
            $table->id();

            // Link to the booking header
            $table->foreignId('booking_id')
                ->constrained('bookings')
                ->cascadeOnDelete();

            // The exact seat booked
            $table->foreignId('seat_id')
                ->constrained('seats')
                ->cascadeOnDelete();

            // Final seat price at time of booking
            // Important: Do NOT calculate later. Must be snapshot.
            $table->decimal('price', 10, 2);

            // booked / checked_in / cancelled
            $table->string('status', 20)->default('booked');

            $table->timestamps();

            // Faster lookups
            $table->index('booking_id');
            $table->index('seat_id');

            // Prevent duplicate seat inside same booking
            $table->unique(['booking_id', 'seat_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('booking_seats');
    }
};
