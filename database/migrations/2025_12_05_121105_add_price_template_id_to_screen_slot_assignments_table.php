<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('screen_slot_assignments', function (Blueprint $table) {

            // Add nullable first, because not all assignments have pricing yet
            $table->foreignId('price_template_id')
                ->nullable()
                ->after('movie')
                ->constrained('price_templates')
                ->nullOnDelete(); // If template deleted, assignment remains but loses pricing
        });
    }

    public function down(): void
    {
        Schema::table('screen_slot_assignments', function (Blueprint $table) {
            $table->dropForeign(['price_template_id']);
            $table->dropColumn('price_template_id');
        });
    }
};
