@extends('layouts.app')
@section('title', $closingReport->getTypeLabel())

@push('styles')
<style>
    :root { --blue:#003087; --blue-mid:#1a4db3; --muted:#6B7280; --muted-lt:#9CA3AF; --border:rgba(255,255,255,0.07); --text:#E8E4DC; }
    .page-heading { font-family:'Playfair Display',serif; font-size:22px; font-weight:700; }

    /* Stat cards */
    .stats-grid { display:grid; grid-template-columns:repeat(auto-fit,minmax(180px,1fr)); gap:14px; margin-bottom:24px; }
    .stat-card { background:rgba(255,255,255,0.03); border:1px solid rgba(255,255,255,0.07); border-radius:14px; padding:18px 20px; }
    .stat-label { font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:0.7px; color:var(--muted); margin-bottom:8px; }
    .stat-value { font-family:'IBM Plex Mono',monospace; font-size:22px; font-weight:700; color:#fff; }
    .stat-sub { font-size:11px; color:var(--muted); margin-top:4px; }

    /* Section card */
    .section-card { background:rgba(255,255,255,0.03); border:1px solid rgba(255,255,255,0.07); border-radius:14px; overflow:hidden; margin-bottom:20px; }
    .section-header { padding:14px 20px; border-bottom:1px solid rgba(255,255,255,0.07); font-size:13px; font-weight:700; color:var(--muted-lt); text-transform:uppercase; letter-spacing:0.7px; }
    .section-body { padding:20px; }

    /* Payment bars */
    .payment-row { display:flex; align-items:center; gap:12px; margin-bottom:14px; }
    .payment-label { font-size:13px; font-weight:600; color:var(--text); width:120px; flex-shrink:0; }
    .payment-bar-wrap { flex:1; background:rgba(255,255,255,0.06); border-radius:999px; height:8px; overflow:hidden; }
    .payment-bar { height:100%; border-radius:999px; transition:width 0.6s ease; }
    .bar-cash  { background:linear-gradient(90deg,#10B981,#34D399); }
    .bar-khqr  { background:linear-gradient(90deg,#6366F1,#818CF8); }
    .bar-aba   { background:linear-gradient(90deg,#F59E0B,#FCD34D); }
    .payment-amount { font-family:'IBM Plex Mono',monospace; font-size:13px; font-weight:700; color:#fff; width:80px; text-align:right; flex-shrink:0; }

    /* Top products table */
    .rank-table { width:100%; border-collapse:collapse; }
    .rank-table td { padding:10px 12px; font-size:13px; border-bottom:1px solid rgba(255,255,255,0.04); }
    .rank-table tr:last-child td { border-bottom:none; }
    .rank-num { font-family:'IBM Plex Mono',monospace; font-size:11px; color:var(--muted); width:30px; }
    .rank-name { font-weight:600; color:var(--text); }
    .rank-qty { font-family:'IBM Plex Mono',monospace; font-size:12px; color:var(--muted-lt); }
    .rank-rev { font-family:'IBM Plex Mono',monospace; font-size:13px; font-weight:700; color:#6EE7B7; text-align:right; }

    /* Staff table */
    .staff-row { display:flex; align-items:center; gap:14px; padding:12px 0; border-bottom:1px solid rgba(255,255,255,0.04); }
    .staff-row:last-child { border-bottom:none; }
    .staff-avatar { width:36px; height:36px; border-radius:10px; background:linear-gradient(135deg,#003087,#1a4db3); display:flex; align-items:center; justify-content:center; font-size:14px; font-weight:700; color:#fff; flex-shrink:0; }
    .staff-name { font-size:14px; font-weight:600; color:#fff; }
    .staff-role { font-size:11px; color:var(--muted); text-transform:capitalize; }
    .staff-stats { margin-left:auto; text-align:right; }
    .staff-items { font-family:'IBM Plex Mono',monospace; font-size:13px; font-weight:700; color:#A5B4FC; }
    .staff-rev { font-family:'IBM Plex Mono',monospace; font-size:12px; color:#6EE7B7; }
    .medal { font-size:18px; margin-right:4px; }

    /* Profit positive/negative */
    .profit-pos { color:#6EE7B7; }
    .profit-neg { color:#FCA5A5; }

    /* Back & action buttons */
    .btn-back { display:inline-flex; align-items:center; gap:6px; padding:8px 16px; border-radius:9px; border:1px solid rgba(255,255,255,0.1); background:rgba(255,255,255,0.04); color:var(--muted-lt); font-size:13px; font-weight:600; font-family:'DM Sans',sans-serif; text-decoration:none; transition:all 0.15s; cursor:pointer; }
    .btn-back:hover { background:rgba(255,255,255,0.08); color:#fff; }
    .btn-resend { display:inline-flex; align-items:center; gap:6px; padding:8px 16px; border-radius:9px; border:1px solid rgba(99,102,241,0.3); background:rgba(99,102,241,0.1); color:#A5B4FC; font-size:13px; font-weight:600; font-family:'DM Sans',sans-serif; cursor:pointer; transition:all 0.15s; }
    .btn-resend:hover { background:rgba(99,102,241,0.22); }

    /* Type badge */
    .badge { display:inline-flex; align-items:center; gap:5px; padding:4px 12px; border-radius:999px; font-size:12px; font-weight:700; }
    .badge-daily   { background:rgba(99,102,241,0.12); border:1px solid rgba(99,102,241,0.25); color:#A5B4FC; }
    .badge-weekly  { background:rgba(16,185,129,0.12); border:1px solid rgba(16,185,129,0.25); color:#6EE7B7; }
    .badge-monthly { background:rgba(245,158,11,0.12); border:1px solid rgba(245,158,11,0.25); color:#FCD34D; }
</style>
@endpush

@section('content')

{{-- Header --}}
<div class="flex items-center justify-between flex-wrap gap-3 mb-6">
    <div>
        <div class="page-heading text-white">
            {{ $closingReport->getTypeEmoji() }} {{ $closingReport->getTypeLabel() }}
        </div>
        <div class="flex items-center gap-2 mt-2">
            <span class="badge badge-{{ $closingReport->type }}">{{ ucfirst($closingReport->type) }}</span>
            <span style="font-size:12px;color:var(--muted);">{{ $closingReport->getPeriodLabel() }}</span>
            @if($closingReport->triggered_by === 'manual')
                <span style="font-size:12px;color:var(--muted);">· 👆 Manual</span>
            @else
                <span style="font-size:12px;color:var(--muted);">· 🤖 Auto</span>
            @endif
        </div>
    </div>
    <div class="flex gap-2 flex-wrap">
        {{-- Resend Telegram --}}
        <form method="POST" action="{{ route('admin.closing-reports.resend-telegram', $closingReport) }}">
            @csrf
            <button type="submit" class="btn-resend">
                📨 Resend Telegram
            </button>
        </form>
        <a href="{{ route('admin.closing-reports.index') }}" class="btn-back">← Back</a>
    </div>
</div>

{{-- Success --}}
@if(session('success'))
    <div class="flex items-center gap-2 bg-green-500/10 border border-green-500/30 rounded-xl px-4 py-3 mb-5 text-green-300 text-sm font-medium">
        ✅ {{ session('success') }}
    </div>
@endif

{{-- KPI Stats --}}
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-label">💰 Total Revenue</div>
        <div class="stat-value">${{ number_format($closingReport->total_revenue, 2) }}</div>
        <div class="stat-sub">{{ $closingReport->total_transactions }} transactions</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">💸 Total Expenses</div>
        <div class="stat-value" style="color:#FCA5A5;">${{ number_format($closingReport->total_expenses, 2) }}</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">{{ $closingReport->net_profit >= 0 ? '📈' : '📉' }} Net Profit</div>
        <div class="stat-value {{ $closingReport->net_profit >= 0 ? 'profit-pos' : 'profit-neg' }}">
            {{ $closingReport->net_profit >= 0 ? '+' : '-' }}${{ number_format(abs($closingReport->net_profit), 2) }}
        </div>
        @php $margin = $closingReport->total_revenue > 0 ? round(($closingReport->net_profit / $closingReport->total_revenue) * 100, 1) : 0; @endphp
        <div class="stat-sub">{{ $margin }}% margin</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">🧾 Transactions</div>
        <div class="stat-value">{{ $closingReport->total_transactions }}</div>
        @php $avg = $closingReport->total_transactions > 0 ? round($closingReport->total_revenue / $closingReport->total_transactions, 2) : 0; @endphp
        <div class="stat-sub">Avg ${{ $avg }} / sale</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">📨 Telegram</div>
        <div class="stat-value" style="font-size:16px;margin-top:4px;">
            @if($closingReport->telegram_sent)
                <span style="color:#6EE7B7;">✅ Sent</span>
            @else
                <span style="color:#FCA5A5;">❌ Not Sent</span>
            @endif
        </div>
        @if($closingReport->telegram_sent_at)
            <div class="stat-sub">{{ $closingReport->telegram_sent_at->setTimezone('Asia/Phnom_Penh')->format('d M Y H:i') }}</div>
        @endif
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-5">

    {{-- Payment Breakdown --}}
    <div class="section-card">
        <div class="section-header">💳 Payment Breakdown</div>
        <div class="section-body">
            @php $total = max($closingReport->total_revenue, 0.01); @endphp

            <div class="payment-row">
                <div class="payment-label">💵 Cash</div>
                <div class="payment-bar-wrap">
                    <div class="payment-bar bar-cash" style="width:{{ min(100, ($closingReport->cash_amount / $total) * 100) }}%"></div>
                </div>
                <div class="payment-amount">${{ number_format($closingReport->cash_amount, 2) }}</div>
            </div>
            <div class="payment-row">
                <div class="payment-label">📱 KHQR Bakong</div>
                <div class="payment-bar-wrap">
                    <div class="payment-bar bar-khqr" style="width:{{ min(100, ($closingReport->khqr_amount / $total) * 100) }}%"></div>
                </div>
                <div class="payment-amount">${{ number_format($closingReport->khqr_amount, 2) }}</div>
            </div>
            <div class="payment-row" style="margin-bottom:0">
                <div class="payment-label">🏦 ABA PayWay</div>
                <div class="payment-bar-wrap">
                    <div class="payment-bar bar-aba" style="width:{{ min(100, ($closingReport->aba_amount / $total) * 100) }}%"></div>
                </div>
                <div class="payment-amount">${{ number_format($closingReport->aba_amount, 2) }}</div>
            </div>
        </div>
    </div>

    {{-- Top Products --}}
    <div class="section-card">
        <div class="section-header">🏆 Top Selling Products</div>
        <div class="section-body" style="padding:0;">
            @if(!empty($closingReport->top_products))
                <table class="rank-table">
                    @php $medals = ['🥇','🥈','🥉','4️⃣','5️⃣']; @endphp
                    @foreach($closingReport->top_products as $i => $product)
                    <tr>
                        <td class="rank-num">{{ $medals[$i] ?? ($i+1) }}</td>
                        <td class="rank-name">{{ $product['name'] }}</td>
                        <td class="rank-qty">{{ $product['qty'] }} items</td>
                        <td class="rank-rev">${{ number_format($product['revenue'], 2) }}</td>
                    </tr>
                    @endforeach
                </table>
            @else
                <div style="padding:24px;text-align:center;color:var(--muted);font-size:13px;">No sales in this period</div>
            @endif
        </div>
    </div>

</div>

{{-- Staff Performance --}}
<div class="section-card">
    <div class="section-header">👥 Staff Performance</div>
    <div class="section-body">
        @if(!empty($closingReport->staff_performance))
            @php $medals = ['🥇','🥈','🥉']; @endphp
            @foreach($closingReport->staff_performance as $i => $staff)
            <div class="staff-row">
                <div class="staff-avatar">{{ strtoupper(substr($staff['name'], 0, 1)) }}</div>
                <div>
                    <div class="staff-name">
                        <span class="medal">{{ $medals[$i] ?? '' }}</span>{{ $staff['name'] }}
                    </div>
                    <div class="staff-role">{{ $staff['role'] }} · {{ $staff['transactions'] }} transactions</div>
                </div>
                <div class="staff-stats">
                    <div class="staff-items">{{ $staff['items_sold'] }} items sold</div>
                    <div class="staff-rev">${{ number_format($staff['revenue'], 2) }}</div>
                </div>
            </div>
            @endforeach
        @else
            <div style="text-align:center;color:var(--muted);font-size:13px;">No staff data for this period</div>
        @endif
    </div>
</div>

{{-- Meta info --}}
<div style="margin-top:8px;font-size:12px;color:var(--muted);text-align:right;">
    Report generated: {{ $closingReport->created_at->setTimezone('Asia/Phnom_Penh')->format('d M Y H:i') }}
    @if($closingReport->triggeredByUser)
        · by {{ $closingReport->triggeredByUser->name }}
    @endif
</div>

@endsection