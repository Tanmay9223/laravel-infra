<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('user_wallets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->decimal('total_deposit', 14, 5)->default(0);
            $table->decimal('total_withdraw_btc', 14, 5)->default(0);
            $table->decimal('total_withdraw_usd', 14, 2)->default(0);
            $table->decimal('total_winning_btc', 14, 5)->default(0);
            $table->decimal('total_winning_usd', 14, 2)->default(0);
            $table->decimal('total_commission_btc', 14, 5)->default(0);
            $table->decimal('total_commission_usd', 14, 2)->default(0);
            $table->decimal('other_btc', 14, 5)->default(0);
            $table->decimal('other_usd', 14, 2)->default(0);
            $table->boolean('status')->default(1);
            $table->timestamps();
        });

        DB::statement("
            ALTER TABLE user_wallets
            ADD COLUMN total_wallet_btc_amount numeric(14,5)
            GENERATED ALWAYS AS (
                total_withdraw_btc + total_winning_btc + total_commission_btc - other_btc
            ) STORED
        ");

        DB::statement("
            ALTER TABLE user_wallets
            ADD COLUMN total_wallet_usd_amount numeric(14,2)
            GENERATED ALWAYS AS (
                total_deposit - total_withdraw_usd + total_winning_usd + total_commission_usd - other_usd
            ) STORED
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_wallets');
    }
};
