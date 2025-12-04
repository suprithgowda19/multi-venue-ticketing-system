<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('screen_slot_assignments', function (Blueprint $table) {
            $table->id();

            // Venue
            $table->foreignId('venue_id')
                  ->constrained('venues')
                  ->cascadeOnDelete();

            // Screen under that venue
            $table->foreignId('screen_id')
                  ->constrained('screens')
                  ->cascadeOnDelete();

            // Slot under that venue
            $table->foreignId('slot_id')
                  ->constrained('slots')
                  ->cascadeOnDelete();

            // Movie name (simple string)
            $table->string('movie');

            // Day number (1–7)
            $table->unsignedTinyInteger('day')
                  ->comment('Event day number (1 to 7)');

            // Active or inactive assignment
            $table->enum('status', ['active', 'inactive'])
                  ->default('active');

            $table->timestamps();

            // Prevent duplicate schedules
            $table->unique(['venue_id', 'screen_id', 'slot_id', 'day'], 'unique_schedule');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('screen_slot_assignments');
    }
};
