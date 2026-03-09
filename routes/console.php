<?php

use Illuminate\Support\Facades\Schedule;

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