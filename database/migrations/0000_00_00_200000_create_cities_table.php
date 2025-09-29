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
        Schema::create('cities', function (Blueprint $table) {
            $table->id();

            // Foreign keys (signed in PostgreSQL)
            $table->foreignId('country_id')->constrained('countries')->onDelete('cascade');
            $table->foreignId('state_id')->constrained('states')->onDelete('cascade');

            $table->string('name', 100);

            $table->decimal('latitude', 15, 8)->nullable();
            $table->decimal('longitude', 15, 8)->nullable();

            $table->boolean('status')->default(true);
            $table->string('wikiDataId', 255)->nullable();

            $table->timestamps();
            $table->softDeletes();

            
            $table->index(['state_id', 'name']);
            $table->unique(['state_id', 'name']); // avoids duplicate city names within a state
        });


    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cities');
    }
};
