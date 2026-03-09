<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('closing_reports', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['daily', 'weekly', 'monthly']);
            $table->date('period_start');
            $table->date('period_end');

            // Revenue & Sales
            $table->decimal('total_revenue', 12, 2)->default(0);
            $table->integer('total_transactions')->default(0);

            // Payment breakdown
            $table->decimal('cash_amount', 12, 2)->default(0);
            $table->decimal('khqr_amount', 12, 2)->default(0);
            $table->decimal('aba_amount', 12, 2)->default(0);

            // Expenses & Profit
            $table->decimal('total_expenses', 12, 2)->default(0);
            $table->decimal('net_profit', 12, 2)->default(0);

            // JSON data for complex fields
            $table->json('top_products')->nullable();   // [{name, qty, revenue}]
            $table->json('staff_performance')->nullable(); // [{name, role, items_sold, revenue}]

            // Who triggered it
            $table->enum('triggered_by', ['scheduler', 'manual'])->default('scheduler');
            $table->foreignId('triggered_by_user_id')->nullable()->constrained('users')->nullOnDelete();

            // Telegram
            $table->boolean('telegram_sent')->default(false);
            $table->timestamp('telegram_sent_at')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('closing_reports');
    }
};