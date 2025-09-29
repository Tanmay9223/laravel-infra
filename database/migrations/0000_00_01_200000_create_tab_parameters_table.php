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
        Schema::create('tab_parameters', function (Blueprint $table) {
            $table->id();
            $table->string('param_name')->nullable();
            $table->string('param_key')->nullable();
            $table->integer('param_value')->nullable();
            $table->string('param_description')->nullable();
            $table->text('param_data')->nullable();
            $table->boolean('status')->default(true)->index();
            $table->timestamps();
            $table->softDeletes();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tab_parameters');
    }
};
