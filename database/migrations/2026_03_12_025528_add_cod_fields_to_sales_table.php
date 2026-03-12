<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            // Order status for COD flow
            // pending   = cash order just placed, awaiting delivery
            // delivering = out for delivery
            // paid       = payment confirmed by admin/cashier
            // cancelled  = cancelled
            // (KHQR orders are created directly as 'paid')
            $table->enum('status', ['pending', 'delivering', 'paid', 'cancelled'])
                  ->default('pending')
                  ->after('payment_method');

            // Optional order notes from customer
            $table->text('notes')->nullable()->after('status');

            // Payment proof — reference number entered by admin
            $table->string('payment_reference')->nullable()->after('notes');

            // Payment proof — photo uploaded by admin (stored in storage/app/public/payment_proofs)
            $table->string('payment_proof')->nullable()->after('payment_reference');

            // Who confirmed the payment (admin/cashier user id)
            $table->foreignId('confirmed_by')->nullable()->constrained('users')->nullOnDelete()->after('payment_proof');

            // When payment was confirmed
            $table->timestamp('confirmed_at')->nullable()->after('confirmed_by');
        });
    }

    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->dropConstrainedForeignId('confirmed_by');
            $table->dropColumn(['status', 'notes', 'payment_reference', 'payment_proof', 'confirmed_at']);
        });
    }
};