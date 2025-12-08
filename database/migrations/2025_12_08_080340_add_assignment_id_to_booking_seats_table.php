<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('booking_seats', function (Blueprint $table) {
            // 1) Add the column
            $table->foreignId('assignment_id')
                ->after('booking_id')
                ->constrained('screen_slot_assignments')
                ->cascadeOnDelete();

            // 2) Add indexes
            $table->index('assignment_id');

            // 3) Add unique constraint to prevent double booking per show
            $table->unique(['assignment_id', 'seat_id']);
        });
    }

    public function down(): void
    {
        Schema::table('booking_seats', function (Blueprint $table) {
            $table->dropUnique(['assignment_id', 'seat_id']);
            $table->dropColumn('assignment_id');
        });
    }
};
