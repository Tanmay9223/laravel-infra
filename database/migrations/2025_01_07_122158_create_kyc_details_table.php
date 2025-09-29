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
        Schema::create('kyc_details', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->unsignedBigInteger('user_id')->unique()->index();
            $table->tinyInteger('no_of_attempts');
            $table->dateTime('timer')->nullable();
            $table->dateTime('block_time')->nullable();
            $table->string('email_otp')->nullable();
            $table->datetime('email_validity')->nullable();
            $table->boolean('email_verification_status')->default(false);
            $table->timestamp('email_verify_at')->nullable();
            $table->string('mobile_otp')->nullable();
            $table->datetime('mobile_validity')->nullable();
            $table->boolean('mobile_verification_status')->default(false);
            $table->timestamp('mobile_verify_at')->nullable();
            $table->boolean('profile_verification_status')->default(false);
            $table->timestamp('profile_verify_at')->nullable();
            $table->boolean('id_verification_status')->default(false);
            $table->timestamp('id_verify_at')->nullable();
            $table->boolean('address_verification_status')->default(false);
            $table->timestamp('address_verify_at')->nullable();
            $table->boolean('kyc_approved_status')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kyc_details');
    }
};
