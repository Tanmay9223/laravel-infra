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
        Schema::create('states', function (Blueprint $table) {
            $table->id();

            // ---- Basic data ----
            $table->string('name', 100);

            // Country relationship (BTREE index implicit via FK)
            $table->foreignId('country_id')
                ->constrained('countries')
                ->onDelete('cascade');

            // Two‑letter ISO 3166‑2 code for look‑ups and joins
            $table->char('country_code', 2);

            // Optional reference codes
            $table->string('fips_code', 255)->nullable(); // legacy
            $table->string('iso2', 255)->nullable();      // e.g. “RJ” for Rajasthan
            $table->string('type', 255)->nullable();      // e.g. “state”, “province”

            // Geography
            $table->decimal('latitude', 15, 8)->nullable();
            $table->decimal('longitude', 15, 8)->nullable();

            // Meta
            $table->boolean('status')->default(true);
            $table->string('wikiDataId', 255)->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index(['country_id', 'name']);
            $table->unique(['country_id', 'iso2']);  // nullable but still OK in PG
            $table->index('country_code');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('states');
    }
};
