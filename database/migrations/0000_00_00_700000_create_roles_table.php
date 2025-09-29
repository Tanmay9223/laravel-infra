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
        // ---------------------------
        // roles table
        // ---------------------------
       Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();           // public-safe identifier
            $table->string('name', 100);               // e.g. “Admin”, “Editor”
            $table->boolean('show');                   // show role in UI lists
            $table->boolean('status')->index();        // active/inactive flag
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('modules', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();            // public-safe identifier
            $table->integer('parent_id')->nullable();  // parent module ID (for nesting)
            $table->string('title', 255);              // module display name
            $table->string('slug', 512)->unique();     // unique lookup key, e.g. “user-management”
            $table->string('icon', 512)->nullable();   // optional icon class/path
            $table->bigInteger('position')->index();   // sort order in menus
            $table->boolean('show')->index();          // include in UI menu?
            $table->boolean('status')->default(true)->index(); // enabled/disabled flag
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();                // public-safe identifier
            $table->foreignId('module_id')                  // link to parent module
                ->constrained('modules')
                ->onDelete('cascade');
            $table->string('name', 100);                    // human-readable name
            $table->string('slug', 255)->unique();          // unique lookup key, e.g. “module.create”
            $table->bigInteger('position');                 // sort order within module
            $table->boolean('menu_status')->index();        // show in permission menu?
            $table->boolean('show')->index();               // include in UI?
            $table->boolean('status')->index();             // active/inactive flag
            $table->timestamps();
            $table->softDeletes();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('permissions');
        Schema::dropIfExists('modules');
        Schema::dropIfExists('roles');
    }
};
