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
        // ------------------------------ USERS ---------------------------------
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();   
            $table->string('username')->unique();                            // public‑safe id
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email')->unique();
            $table->string('mobile', 15)->nullable();
            $table->string('dial_code')->nullable();                                  // phone dial‑code (e.g. +91)
            $table->string('country_code')->nullable();                               // ISO‑3166 alpha‑2
            $table->tinyInteger('is_email_verified')->default('0');
            $table->string('password');
            $table->string('sponsor_id')->nullable();                     // external (human) ref
            $table->foreignId('sponsor_by')->nullable()->index();         // FK to users.id (upline)
            $table->string('avatar', 255)->nullable();
            $table->rememberToken();
            $table->ipAddress('ip_address')->nullable();
            // $table->boolean('is_login')->default(false)->index();
            $table->timestamp('password_changed_at')->nullable();
            $table->text('password_history')->nullable();                 // JSON of hashes
            $table->boolean('is_google2fa_enable')->default(false)->index();
            $table->tinyInteger('stage_status')->default(0)->comment('1-New,2-EmailV,3-PI,4-ID,5-Address,6-KYC OK,7-KYC NotOK');
            $table->boolean('status')->default(true)->index();           // active/inactive
            $table->timestamps();
            $table->softDeletes();

            // composite for fast login/user‑lookup by mobile & dial‑code
            $table->index(['dial_code','mobile']);
        });

        // ---------------------- PASSWORD RESET TOKENS -------------------------
        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
            $table->index('created_at');                                  // purge old tokens quickly
        });

        // ----------------------------- SESSIONS -------------------------------
        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();            // session owner
            $table->ipAddress('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();                    // epoch seconds
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('users');
    }
};
