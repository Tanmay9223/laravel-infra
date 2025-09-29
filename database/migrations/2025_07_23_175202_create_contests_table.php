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
        
        Schema::create('contests', function (Blueprint $table) {
            $table->id('id');
            $table->uuid('uuid')->unique();
            $table->string('title')->unique();
            $table->decimal('entry_fee', 14, 5)->default(0.00);
            $table->decimal('prize_pool', 14, 5)->default(0.00);
            $table->timestamp('start_time');
            $table->tinyInteger('type')->comment('1=Daily_usd,2=Daily_btc,3=Weekly,4=Monthly');
            $table->tinyInteger('status')->default(0)->comment('0=Pending,1=Active,2=Completed,3=Cancelled');
            $table->text('description')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contests');
    }
};
