<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('price_templates', function (Blueprint $table) {
            $table->id();

            // Template name must be unique
            // Examples: Standard, Weekend, Festival, Premium Friday
            $table->string('name')->unique();

            // Optional base price (many templates won't use this)
            $table->decimal('base_price', 10, 2)->nullable();

            // Flexible JSON for future pricing rules
            // (GST rules, dynamic multipliers, holiday pricing, versioning, etc.)
            $table->json('metadata')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('price_templates');
    }
};
