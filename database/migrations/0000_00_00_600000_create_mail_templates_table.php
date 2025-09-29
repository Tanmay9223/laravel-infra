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
        Schema::create('mail_templates', function (Blueprint $table) {
            $table->id();

            $table->uuid('uuid')->unique(); // Public-safe identifier

            $table->string('title', 100);    // e.g. “User Welcome Email”
            $table->string('slug', 255);     // e.g. “user-welcome-email” for lookups
            $table->text('subject');         // Email subject
            $table->longText('body');        // Full email body with placeholders

            $table->boolean('status')->default(true)->comment('active/inactive');

            $table->timestamps();
            $table->softDeletes();

            $table->unique('slug');         // Enforce uniqueness for fast retrieval
            // Optional: $table->index('status'); // Only if filtered often
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mail_templates');
    }
};
