<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('seats', function (Blueprint $table) {
            $table->id();

            // Each seat belongs to a screen
            $table->foreignId('screen_id')
                ->constrained('screens')
                ->cascadeOnDelete();

            // Row label: A, B, C, VIP, Balcony, Box etc.
            $table->string('row_label', 20);

            // Seat number in that row
            $table->integer('seat_number');

            // Combined seat code: A1, B12, VIP3 etc.
            $table->string('seat_code', 50);

            // Active / inactive seat (for broken or unavailable seats)
            $table->string('status', 20)->default('active');

            // For future seat properties (wheelchair, recliner, sofa etc.)
            $table->json('metadata')->nullable();

            $table->timestamps();

            // Fast lookups
            $table->index('screen_id');

            // Prevent duplicate seats (per screen)
            $table->unique(['screen_id', 'seat_code']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('seats');
    }
};
