@extends('layouts.app')
@section('title', 'Closing Reports')

@push('styles')
<style>
    :root {
        --blue: #003087; --blue-mid: #1a4db3;
        --muted: #6B7280; --muted-lt: #9CA3AF;
        --border: rgba(255,255,255,0.07);
        --text: #E8E4DC;
    }
    .page-heading { font-family:'Playfair Display',serif; font-size:22px; font-weight:700; }
    .page-heading span { font-family:'IBM Plex Mono',monospace; font-size:13px; font-weight:400; color:var(--muted); margin-left:10px; }

    /* Trigger card */
    .trigger-card { background:rgba(255,255,255,0.03); border:1px solid rgba(255,255,255,0.07); border-radius:16px; padding:20px 24px; margin-bottom:24px; }
    .trigger-title { font-family:'Playfair Display',serif; font-size:16px; font-weight:700; color:#fff; margin-bottom:4px; }
    .trigger-sub { font-size:12px; color:var(--muted); margin-bottom:16px; }
    .trigger-btns { display:flex; gap:10px; flex-wrap:wrap; }

    .btn-trigger { display:inline-flex; align-items:center; gap:7px; padding:10px 18px; border-radius:10px; font-size:13px; font-weight:600; font-family:'DM Sans',sans-serif; cursor:pointer; border:1px solid; transition:all 0.2s; }
    .btn-daily   { background:rgba(99,102,241,0.12); border-color:rgba(99,102,241,0.3);  color:#A5B4FC; }
    .btn-daily:hover   { background:rgba(99,102,241,0.25); }
    .btn-weekly  { background:rgba(16,185,129,0.12); border-color:rgba(16,185,129,0.3);  color:#6EE7B7; }
    .btn-weekly:hover  { background:rgba(16,185,129,0.25); }
    .btn-monthly { background:rgba(245,158,11,0.12); border-color:rgba(245,158,11,0.3);  color:#FCD34D; }
    .btn-monthly:hover { background:rgba(245,158,11,0.25); }

    /* Filter bar */
    .filter-bar { display:flex; gap:8px; margin-bottom:20px; flex-wrap:wrap; }
    .filter-btn { padding:6px 14px; border-radius:8px; font-size:12px; font-weight:600; font-family:'DM Sans',sans-serif; cursor:pointer; border:1px solid rgba(255,255,255,0.1); background:rgba(255,255,255,0.04); color:var(--muted-lt); text-decoration:none; transition:all 0.15s; }
    .filter-btn.active, .filter-btn:hover { background:rgba(0,48,135,0.2); border-color:rgba(0,48,135,0.4); color:#93C5FD; }

    /* Table */
    .table-card { background:rgba(255,255,255,0.03); border:1px solid rgba(255,255,255,0.07); border-radius:16px; overflow:hidden; }
    .table-header-bar { display:flex; align-items:center; justify-content:space-between; padding:16px 20px; border-bottom:1px solid rgba(255,255,255,0.07); }
    .table-card-title { font-size:13px; font-weight:700; color:var(--muted-lt); text-transform:uppercase; letter-spacing:0.7px; }
    table { width:100%; border-collapse:collapse; }
    thead th { padding:11px 20px; font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:0.8px; color:var(--muted); text-align:left; border-bottom:1px solid rgba(255,255,255,0.07); }
    tbody tr { border-bottom:1px solid rgba(255,255,255,0.04); transition:background 0.15s; }
    tbody tr:last-child { border-bottom:none; }
    tbody tr:hover { background:rgba(255,255,255,0.025); }
    tbody td { padding:14px 20px; font-size:13px; color:var(--text); vertical-align:middle; }

    /* Type badge */
    .badge { display:inline-flex; align-items:center; gap:5px; padding:3px 10px; border-radius:999px; font-size:11px; font-weight:700; }
    .badge-daily   { background:rgba(99,102,241,0.12); border:1px solid rgba(99,102,241,0.25); color:#A5B4FC; }
    .badge-weekly  { background:rgba(16,185,129,0.12); border:1px solid rgba(16,185,129,0.25); color:#6EE7B7; }
    .badge-monthly { background:rgba(245,158,11,0.12); border:1px solid rgba(245,158,11,0.25); color:#FCD34D; }

    /* Telegram badge */
    .tg-sent { color:#6EE7B7; font-size:12px; }
    .tg-fail { color:#FCA5A5; font-size:12px; }

    /* Revenue */
    .revenue { font-family:'IBM Plex Mono',monospace; font-size:13px; font-weight:700; color:#6EE7B7; }
    .profit-pos { font-family:'IBM Plex Mono',monospace; font-size:12px; color:#6EE7B7; }
    .profit-neg { font-family:'IBM Plex Mono',monospace; font-size:12px; color:#FCA5A5; }

    /* View btn */
    .btn-view { display:inline-flex; align-items:center; gap:5px; padding:5px 12px; border-radius:7px; font-size:12px; font-weight:600; font-family:'DM Sans',sans-serif; text-decoration:none; background:rgba(0,48,135,0.15); border:1px solid rgba(0,48,135,0.3); color:#93C5FD; transition:all 0.15s; }
    .btn-view:hover { background:rgba(0,48,135,0.3); }

    /* Empty */
    .empty-state { padding:56px 24px; text-align:center; }
    .empty-icon { font-size:44px; margin-bottom:14px; opacity:0.35; }
    .empty-title { font-size:15px; font-weight:600; color:#fff; margin-bottom:6px; }
    .empty-sub { font-size:13px; color:var(--muted); }
</style>
@endpush

@section('content')

{{-- Header --}}
<div class="flex items-center justify-between flex-wrap gap-3 mb-6">
    <div class="page-heading text-white">
        📊 Closing Reports
        <span>{{ $reports->total() }} total</span>
    </div>
</div>

{{-- Success alert --}}
@if(session('success'))
    <div class="flex items-center gap-2 bg-green-500/10 border border-green-500/30 rounded-xl px-4 py-3 mb-5 text-green-300 text-sm font-medium">
        ✅ {{ session('success') }}
    </div>
@endif

{{-- Manual Trigger Card --}}
<div class="trigger-card">
    <div class="trigger-title">📤 Generate Closing Report</div>
    <div class="trigger-sub">Manually generate a report for the current day, week, or month. A Telegram message will be sent automatically.</div>
    <div class="trigger-btns">
        {{-- Daily --}}
        <form method="POST" action="{{ route('admin.closing-reports.trigger') }}">
            @csrf
            <input type="hidden" name="type" value="daily">
            <button type="submit" class="btn-trigger btn-daily">📅 Daily Closing</button>
        </form>
        {{-- Weekly --}}
        <form method="POST" action="{{ route('admin.closing-reports.trigger') }}">
            @csrf
            <input type="hidden" name="type" value="weekly">
            <button type="submit" class="btn-trigger btn-weekly">📆 Weekly Closing</button>
        </form>
        {{-- Monthly --}}
        <form method="POST" action="{{ route('admin.closing-reports.trigger') }}">
            @csrf
            <input type="hidden" name="type" value="monthly">
            <button type="submit" class="btn-trigger btn-monthly">🗓️ Monthly Closing</button>
        </form>
    </div>
</div>

{{-- Filter bar --}}
<div class="filter-bar">
    <a href="{{ route('admin.closing-reports.index') }}"
        class="filter-btn {{ !request('type') ? 'active' : '' }}">All</a>
    <a href="{{ route('admin.closing-reports.index', ['type' => 'daily']) }}"
        class="filter-btn {{ request('type') === 'daily' ? 'active' : '' }}">📅 Daily</a>
    <a href="{{ route('admin.closing-reports.index', ['type' => 'weekly']) }}"
        class="filter-btn {{ request('type') === 'weekly' ? 'active' : '' }}">📆 Weekly</a>
    <a href="{{ route('admin.closing-reports.index', ['type' => 'monthly']) }}"
        class="filter-btn {{ request('type') === 'monthly' ? 'active' : '' }}">🗓️ Monthly</a>
</div>

{{-- Table --}}
<div class="table-card">
    <div class="table-header-bar">
        <div class="table-card-title">All Reports</div>
        <span style="font-size:12px;color:var(--muted);font-family:'IBM Plex Mono',monospace;">{{ $reports->total() }} records</span>
    </div>

    @if($reports->count())
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Type</th>
                    <th>Period</th>
                    <th>Revenue</th>
                    <th>Profit</th>
                    <th>Sales</th>
                    <th>Telegram</th>
                    <th>Triggered</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach($reports as $report)
                <tr>
                    <td style="font-family:'IBM Plex Mono',monospace;font-size:12px;color:var(--muted);">
                        {{ str_pad($loop->iteration + ($reports->currentPage() - 1) * $reports->perPage(), 2, '0', STR_PAD_LEFT) }}
                    </td>
                    <td>
                        <span class="badge badge-{{ $report->type }}">
                            {{ $report->getTypeEmoji() }} {{ ucfirst($report->type) }}
                        </span>
                    </td>
                    <td style="font-size:12px;">{{ $report->getPeriodLabel() }}</td>
                    <td><span class="revenue">${{ number_format($report->total_revenue, 2) }}</span></td>
                    <td>
                        @if($report->net_profit >= 0)
                            <span class="profit-pos">+${{ number_format($report->net_profit, 2) }}</span>
                        @else
                            <span class="profit-neg">-${{ number_format(abs($report->net_profit), 2) }}</span>
                        @endif
                    </td>
                    <td style="font-family:'IBM Plex Mono',monospace;font-size:12px;">{{ $report->total_transactions }}</td>
                    <td>
                        @if($report->telegram_sent)
                            <span class="tg-sent">✅ Sent</span>
                        @else
                            <span class="tg-fail">❌ Failed</span>
                        @endif
                    </td>
                    <td style="font-size:12px;color:var(--muted);">
                        {{ $report->triggered_by === 'manual' ? '👆 Manual' : '🤖 Auto' }}
                        @if($report->triggeredByUser)
                            <br><span style="font-size:11px;">{{ $report->triggeredByUser->name }}</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('admin.closing-reports.show', $report) }}" class="btn-view">
                            👁 View
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        {{-- Pagination --}}
        @if($reports->hasPages())
            <div class="px-5 py-4 border-t border-white/[0.07]">
                {{ $reports->links() }}
            </div>
        @endif

    @else
        <div class="empty-state">
            <div class="empty-icon">📊</div>
            <div class="empty-title">No closing reports yet</div>
            <div class="empty-sub">Use the buttons above to generate your first report.</div>
        </div>
    @endif
</div>

@endsection