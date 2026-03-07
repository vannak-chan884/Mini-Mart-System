<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ── 1. Master list of every permission key ────────────────
        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();   // e.g. "expenses.create"
            $table->string('label');           // e.g. "Create Expenses"
            $table->string('group');           // e.g. "Expenses"
            $table->timestamps();
        });

        // ── 2. Which keys are enabled for a given role ────────────
        Schema::create('role_permissions', function (Blueprint $table) {
            $table->id();
            $table->string('role');            // "cashier"
            $table->string('permission_key'); // "expenses.create"
            $table->timestamps();

            $table->unique(['role', 'permission_key']);

            $table->foreign('permission_key')
                  ->references('key')
                  ->on('permissions')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('role_permissions');
        Schema::dropIfExists('permissions');
    }
};