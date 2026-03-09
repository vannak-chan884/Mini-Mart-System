<?php
namespace App\Console\Commands;

use App\Services\ClosingReportService;
use Illuminate\Console\Command;

class ClosingReportMonthly extends Command
{
    protected $signature   = 'closing:monthly';
    protected $description = 'Generate monthly closing report and send to Telegram';

    public function handle(): void
    {
        $this->info('Generating monthly closing report...');
        $report = ClosingReportService::generateMonthly();
        $this->info("✅ Monthly closing report #{$report->id} generated.");
    }
}