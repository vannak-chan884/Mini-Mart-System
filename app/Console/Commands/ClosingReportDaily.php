<?php
namespace App\Console\Commands;

use App\Services\ClosingReportService;
use Illuminate\Console\Command;

class ClosingReportDaily extends Command
{
    protected $signature   = 'closing:daily';
    protected $description = 'Generate daily closing report and send to Telegram';

    public function handle(): void
    {
        $this->info('Generating daily closing report...');
        $report = ClosingReportService::generateDaily();
        $this->info("✅ Daily closing report #{$report->id} generated.");
        $this->info("   Telegram sent: " . ($report->telegram_sent ? 'Yes' : 'No'));
    }
}