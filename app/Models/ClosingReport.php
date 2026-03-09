<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClosingReport extends Model
{
    protected $fillable = [
        'type',
        'period_start',
        'period_end',
        'total_revenue',
        'total_transactions',
        'cash_amount',
        'khqr_amount',
        'aba_amount',
        'total_expenses',
        'net_profit',
        'top_products',
        'staff_performance',
        'triggered_by',
        'triggered_by_user_id',
        'telegram_sent',
        'telegram_sent_at',
    ];

    protected $casts = [
        'period_start'      => 'date',
        'period_end'        => 'date',
        'total_revenue'     => 'decimal:2',
        'cash_amount'       => 'decimal:2',
        'khqr_amount'       => 'decimal:2',
        'aba_amount'        => 'decimal:2',
        'total_expenses'    => 'decimal:2',
        'net_profit'        => 'decimal:2',
        'top_products'      => 'array',
        'staff_performance' => 'array',
        'telegram_sent'     => 'boolean',
        'telegram_sent_at'  => 'datetime',
    ];

    // ── Relationships ─────────────────────────────────────────────────────────

    public function triggeredByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'triggered_by_user_id');
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    public function getTypeLabel(): string
    {
        return match($this->type) {
            'daily'   => 'Daily Closing',
            'weekly'  => 'Weekly Closing',
            'monthly' => 'Monthly Closing',
            default   => ucfirst($this->type),
        };
    }

    public function getTypeEmoji(): string
    {
        return match($this->type) {
            'daily'   => '📅',
            'weekly'  => '📆',
            'monthly' => '🗓️',
            default   => '📋',
        };
    }

    public function getPeriodLabel(): string
    {
        if ($this->type === 'daily') {
            return $this->period_start->format('d M Y');
        }
        return $this->period_start->format('d M') . ' – ' . $this->period_end->format('d M Y');
    }
}