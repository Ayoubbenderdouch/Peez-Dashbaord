<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('shops', function (Blueprint $table) {
            $table->id();
            $table->foreignId('neighborhood_id')->constrained()->onDelete('cascade');
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->decimal('discount_percent', 4, 2);
            $table->decimal('lat', 10, 7);
            $table->decimal('lng', 10, 7);
            $table->string('phone');
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            // Unique constraint: ONE shop per category per neighborhood
            $table->unique(['neighborhood_id', 'category_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shops');
    }
};
