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
        Schema::table('activations', function (Blueprint $table) {
            // Add idempotency_key for preventing duplicate activations
            $table->string('idempotency_key', 36)->nullable()->unique()->after('months');
            
            // Add index for faster lookups
            $table->index('idempotency_key');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('activations', function (Blueprint $table) {
            $table->dropIndex(['idempotency_key']);
            $table->dropColumn('idempotency_key');
        });
    }
};
