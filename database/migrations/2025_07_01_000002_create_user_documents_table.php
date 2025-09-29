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
        Schema::create('user_documents', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade')->index();
            $table->unsignedInteger('doc_type')->nullable();                        // references document type (config or lookup table)
            $table->string('type')->nullable();                                     // e.g. 'passport', 'aadhaar'
            $table->string('description')->nullable();
            $table->string('front_side')->nullable();                               // file path or URL
            $table->string('back_side')->nullable();                                // file path or URL
            $table->boolean('status')->default(1);                           // approval status (e.g. pending/approved)
            $table->text('comment')->nullable();                                    // admin remark
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_documents');
    }
};
