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
        Schema::create('meta_data_lists', function (Blueprint $table) {
            $table->id();
            $table->integer('group_id');
            $table->enum('type', ['input', 'dropdown', 'date', 'checkbox', 'textarea', 'toggle'])->default('input');
            $table->text('meta_title')->nullable();
            $table->string('meta_key')->unique()->comment('unique key for metadata item');
            $table->longText('meta_value')->nullable();
            $table->boolean('status')->default(true)->index();
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('meta_data_lists');
    }
};
