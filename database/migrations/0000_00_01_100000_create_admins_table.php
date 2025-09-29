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
        Schema::create('admins', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('role_id')->constrained('roles')->onDelete('cascade')->index();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->date('dob')->nullable();
            $table->string('avatar', 255)->nullable();
            $table->ipAddress('ip_address')->nullable();
            $table->boolean('is_login')->default(false)->comment('currently logged in flag')->index();
            $table->timestamp('login_time')->nullable();
            $table->timestamp('activity_time')->nullable();
            $table->timestamp('logout_time')->nullable();
            $table->json('password_history')->nullable();
            $table->timestamp('password_changed_at')->nullable();
            $table->boolean('is_google2fa_enable')->default(false)->index();
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
        Schema::dropIfExists('admins');
    }
};
