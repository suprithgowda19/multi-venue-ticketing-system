<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('price_template_rows', function (Blueprint $table) {
            $table->id();

            // Link to the parent price template
            $table->foreignId('price_template_id')
                ->constrained('price_templates')
                ->cascadeOnDelete();

            // Seat row label: A, B, C, VIP, Gold, Silver etc.
            $table->string('row_label', 20);

            // Price assigned for this row under the template
            $table->decimal('price', 10, 2);

            $table->timestamps();

            // Prevent same row being added twice under the same template
            $table->unique(['price_template_id', 'row_label']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('price_template_rows');
    }
};
