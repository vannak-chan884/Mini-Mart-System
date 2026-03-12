<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            // Tracks physical delivery — separate from payment status
            // pending    = order placed, not yet out for delivery
            // delivering = out for delivery
            // delivered  = customer received the product
            $table->enum('delivery_status', ['pending', 'delivering', 'delivered'])
                  ->default('pending')
                  ->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->dropColumn('delivery_status');
        });
    }
};