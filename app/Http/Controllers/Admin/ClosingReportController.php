<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClosingReport;
use App\Services\ClosingReportService;
use Illuminate\Http\Request;

class ClosingReportController extends Controller
{
    // ── History page ──────────────────────────────────────────────────────────
    public function index(Request $request)
    {
        $query = ClosingReport::with('triggeredByUser')
            ->orderByDesc('created_at');

        // Filter by type
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        $reports = $query->paginate(5)->withQueryString();

        return view('admin.closing-reports.index', compact('reports'));
    }

    // ── Show single report ────────────────────────────────────────────────────
    public function show(ClosingReport $closingReport)
    {
        return view('admin.closing-reports.show', compact('closingReport'));
    }

    // ── Manual trigger ────────────────────────────────────────────────────────
    public function trigger(Request $request)
    {
        $request->validate([
            'type' => ['required', 'in:daily,weekly,monthly'],
        ]);

        $user   = auth()->user();
        $type   = $request->type;

        $report = match($type) {
            'daily'   => ClosingReportService::generateDaily($user, manual: true),
            'weekly'  => ClosingReportService::generateWeekly($user, manual: true),
            'monthly' => ClosingReportService::generateMonthly($user, manual: true),
        };

        return redirect()
            ->route('admin.closing-reports.show', $report)
            ->with('success', ucfirst($type) . ' closing report generated successfully!');
    }

    // ── Resend Telegram ───────────────────────────────────────────────────────
    public function resendTelegram(ClosingReport $closingReport)
    {
        ClosingReportService::sendTelegram($closingReport);

        return back()->with('success', 'Telegram message resent successfully!');
    }
}