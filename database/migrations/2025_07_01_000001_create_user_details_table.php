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
        Schema::create('user_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade')->index();
            $table->foreignId('country_id')->nullable()->constrained('countries')->onDelete('cascade');
            $table->foreignId('state_id')->nullable()->constrained('states')->onDelete('cascade');
            $table->foreignId('city_id')->nullable()->constrained('cities')->onDelete('cascade');
            $table->string('gender')->nullable();       // Will add check constraint below
            $table->string('zipcode')->nullable();
            $table->date('dob')->nullable();
            $table->date('ekyc_date')->nullable();      // eKYC verification date
            $table->string('address')->nullable();      // citizenship
            $table->string('reason')->nullable();       // optional rejection/delay note
            $table->timestamps();
            $table->softDeletes();
        });
        
        // Add check constraint for gender values
        DB::statement("
            ALTER TABLE user_details
            ADD CONSTRAINT gender_check
            CHECK (gender IN ('male', 'female', 'other'))
        ");

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_details');
    }
};
