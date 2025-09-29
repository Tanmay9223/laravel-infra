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
        Schema::create('currencies', function (Blueprint $table) {
            $table->id();

            $table->string('currency_name');          // e.g. "Indian Rupee"
            $table->string('currency_symbol');        // e.g. "₹"
            $table->string('currency');               // e.g. "INR" — ISO 4217

            $table->decimal('amount', 10, 2)->default(0);

            $table->boolean('default')->default(false);      // system default currency
            $table->boolean('admin_status')->default(false); // enabled by admin
            $table->boolean('status')->default(false);       // general active/inactive

            $table->timestamps();

            $table->unique('currency');
            $table->index('default');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('currencies');
    }
};
