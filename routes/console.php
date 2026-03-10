<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// ── Auto-renew Bakong token daily at 8:00 AM ─────────────────────────
Schedule::command('bakong:renew-token')->dailyAt('08:00');

// ── Optional: Closing report every day at 11:00 PM ───────────────────
// Schedule::command('report:closing')->dailyAt('23:00');

// ── Daily closing at 7:00 PM Cambodia time ────────────────────────────────────
Schedule::command('closing:daily')
    ->dailyAt('19:00')
    ->timezone('Asia/Phnom_Penh')
    ->name('closing-daily')
    ->withoutOverlapping();

// ── Weekly closing — Sunday at 7:00 PM Cambodia time ─────────────────────────
Schedule::command('closing:weekly')
    ->weeklyOn(0, '19:00')        // 0 = Sunday
    ->timezone('Asia/Phnom_Penh')
    ->name('closing-weekly')
    ->withoutOverlapping();

// ── Monthly closing — last day of month at 7:00 PM Cambodia time ─────────────
Schedule::command('closing:monthly')
    ->lastDayOfMonth('19:00')
    ->timezone('Asia/Phnom_Penh')
    ->name('closing-monthly')
    ->withoutOverlapping();

// ── To activate the scheduler, add this cron to your server: ─────────────────
// * * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1