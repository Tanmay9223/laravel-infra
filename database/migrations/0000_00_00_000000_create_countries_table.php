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
        Schema::create('countries', function (Blueprint $table) {
            $table->id();

            // ---- Identifiers & look‑ups ----
            $table->string('name', 100);                 // e.g. “India”
            $table->char('short_code', 3)->nullable();   // ISO‑3166‑1 alpha‑2 or alpha‑3
            $table->char('dial_code', 3)->nullable();    // Telephone country code
            $table->char('numeric_code', 3)->nullable(); // ISO‑3166‑1 numeric

            // ---- Optional details ----
            $table->char('phone_length', 3)->nullable(); // Typical subscriber length
            $table->string('capital', 255)->nullable();
            $table->string('currency', 255)->nullable();        // “INR”
            $table->string('currency_name', 255)->nullable();   // “Indian Rupee”
            $table->string('currency_symbol', 255)->nullable(); // “₹”

            // ---- Geography ----
            $table->decimal('latitude', 15, 8)->nullable();
            $table->decimal('longitude', 15, 8)->nullable();

            // ---- Meta ----
            $table->boolean('status')->default(true);
            $table->timestamps();
            $table->softDeletes();
            
            $table->unique('short_code');         // ISO alpha‑2/3 is globally unique
            $table->unique('numeric_code');       // ISO numeric is unique
            $table->index(['short_code', 'dial_code']); // common combo filter
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('countries');
    }
};
