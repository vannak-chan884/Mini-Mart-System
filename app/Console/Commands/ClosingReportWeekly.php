<?php
namespace App\Console\Commands;

use App\Services\ClosingReportService;
use Illuminate\Console\Command;

class ClosingReportWeekly extends Command
{
    protected $signature   = 'closing:weekly';
    protected $description = 'Generate weekly closing report and send to Telegram';

    public function handle(): void
    {
        $this->info('Generating weekly closing report...');
        $report = ClosingReportService::generateWeekly();
        $this->info("✅ Weekly closing report #{$report->id} generated.");
    }
}