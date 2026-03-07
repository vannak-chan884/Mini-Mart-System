@extends('layouts.app')

@section('title', 'Expenses')

@push('styles')
    <style>
        /* ── Stat cards ──────────────────────────────────────────── */
        .stat-card {
            position: relative;
            overflow: hidden;
            border-radius: 16px;
            padding: 20px 22px 18px;
            background: #fff;
            border: 1px solid rgba(0, 0, 0, 0.07);
            box-shadow: 0 1px 4px rgba(0, 0, 0, 0.05);
            transition: transform .2s, box-shadow .2s;
        }

        html.dark .stat-card {
            background: rgba(255, 255, 255, 0.03);
            border-color: rgba(255, 255, 255, 0.07);
            box-shadow: none;
        }

        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1);
        }

        html.dark .stat-card:hover {
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.35);
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            border-radius: 16px 16px 0 0;
        }

        .stat-today::before {
            background: linear-gradient(90deg, #EF4444, #F97316);
        }

        .stat-month::before {
            background: linear-gradient(90deg, #F59E0B, #EAB308);
        }

        .stat-total::before {
            background: linear-gradient(90deg, #003087, #3B82F6);
        }

        .stat-label {
            font-size: 10.5px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .8px;
            color: #9CA3AF;
            margin-bottom: 8px;
            font-family: 'DM Sans', sans-serif;
        }

        html.dark .stat-label {
            color: #555968;
        }

        .stat-value {
            font-family: 'IBM Plex Mono', monospace;
            font-size: 26px;
            font-weight: 700;
            color: #1A1A2E;
            line-height: 1;
        }

        html.dark .stat-value {
            color: #EEEAE2;
        }

        /* ── Table card ──────────────────────────────────────────── */
        .tbl-card {
            background: #fff;
            border: 1px solid rgba(0, 0, 0, 0.07);
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 1px 4px rgba(0, 0, 0, 0.05);
        }

        html.dark .tbl-card {
            background: rgba(255, 255, 255, 0.03);
            border-color: rgba(255, 255, 255, 0.07);
            box-shadow: none;
        }

        .tbl-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 14px 22px;
            border-bottom: 1px solid rgba(0, 0, 0, 0.06);
            background: rgba(0, 0, 0, 0.01);
        }

        html.dark .tbl-header {
            border-color: rgba(255, 255, 255, 0.06);
            background: rgba(255, 255, 255, 0.02);
        }

        .tbl-title {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .8px;
            color: #9CA3AF;
            font-family: 'DM Sans', sans-serif;
        }

        .tbl-badge {
            font-family: 'IBM Plex Mono', monospace;
            font-size: 11.5px;
            color: #9CA3AF;
            background: rgba(0, 0, 0, 0.04);
            border: 1px solid rgba(0, 0, 0, 0.07);
            padding: 2px 10px;
            border-radius: 999px;
        }

        html.dark .tbl-badge {
            background: rgba(255, 255, 255, 0.04);
            border-color: rgba(255, 255, 255, 0.07);
        }

        /* ── Table ───────────────────────────────────────────────── */
        .etbl {
            width: 100%;
            border-collapse: collapse;
        }

        .etbl thead th {
            padding: 10px 20px;
            font-size: 10.5px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .8px;
            color: #9CA3AF;
            text-align: left;
            border-bottom: 1px solid rgba(0, 0, 0, 0.06);
            font-family: 'DM Sans', sans-serif;
            white-space: nowrap;
        }

        html.dark .etbl thead th {
            color: #555968;
            border-color: rgba(255, 255, 255, 0.06);
        }

        .etbl thead th.th-right {
            text-align: right;
        }

        .etbl tbody tr {
            border-bottom: 1px solid rgba(0, 0, 0, 0.04);
            transition: background .12s;
        }

        html.dark .etbl tbody tr {
            border-color: rgba(255, 255, 255, 0.04);
        }

        .etbl tbody tr:last-child {
            border-bottom: none;
        }

        .etbl tbody tr:hover {
            background: rgba(0, 0, 0, 0.02);
        }

        html.dark .etbl tbody tr:hover {
            background: rgba(255, 255, 255, 0.025);
        }

        .etbl tbody td {
            padding: 13px 20px;
            font-size: 13.5px;
            color: #1A1A2E;
            vertical-align: middle;
            font-family: 'DM Sans', sans-serif;
        }

        html.dark .etbl tbody td {
            color: #E8E4DC;
        }

        .etbl tbody td.td-right {
            text-align: right;
        }

        /* Row number */
        .row-num {
            font-family: 'IBM Plex Mono', monospace;
            font-size: 11px;
            color: #D1D5DB;
        }

        html.dark .row-num {
            color: #374151;
        }

        /* Category badge */
        .cat-wrap {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .cat-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: linear-gradient(135deg, #003087, #3B82F6);
            flex-shrink: 0;
        }

        .cat-name {
            font-weight: 600;
            font-size: 13.5px;
            color: #1A1A2E;
        }

        html.dark .cat-name {
            color: #EEEAE2;
        }

        /* Amount */
        .amount-cell {
            font-family: 'IBM Plex Mono', monospace;
            font-size: 13.5px;
            font-weight: 700;
            color: #DC2626;
        }

        html.dark .amount-cell {
            color: #FCA5A5;
        }

        /* Date */
        .date-cell {
            font-family: 'IBM Plex Mono', monospace;
            font-size: 12.5px;
            color: #6B7280;
        }

        html.dark .date-cell {
            color: #555968;
        }

        /* Note */
        .note-cell {
            font-size: 12.5px;
            color: #9CA3AF;
            max-width: 180px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        html.dark .note-cell {
            color: #555968;
        }

        /* Action buttons */
        .act-wrap {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 6px;
        }

        .act-btn {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 5px 11px;
            border-radius: 7px;
            font-size: 11.5px;
            font-weight: 600;
            font-family: 'DM Sans', sans-serif;
            text-decoration: none;
            cursor: pointer;
            border: 1px solid;
            transition: all .15s;
            white-space: nowrap;
        }

        .act-edit {
            background: rgba(0, 48, 135, .08);
            border-color: rgba(0, 48, 135, .2);
            color: #1D4ED8;
        }

        html.dark .act-edit {
            background: rgba(0, 48, 135, .2);
            border-color: rgba(0, 48, 135, .35);
            color: #93C5FD;
        }

        .act-edit:hover {
            background: rgba(0, 48, 135, .15);
            border-color: rgba(0, 48, 135, .35);
        }

        html.dark .act-edit:hover {
            background: rgba(0, 48, 135, .35);
        }

        .act-delete {
            background: rgba(204, 0, 1, .07);
            border-color: rgba(204, 0, 1, .2);
            color: #DC2626;
        }

        html.dark .act-delete {
            background: rgba(204, 0, 1, .12);
            border-color: rgba(204, 0, 1, .28);
            color: #FCA5A5;
        }

        .act-delete:hover {
            background: rgba(204, 0, 1, .14);
            border-color: rgba(204, 0, 1, .35);
        }

        html.dark .act-delete:hover {
            background: rgba(204, 0, 1, .22);
        }

        /* Empty state */
        .empty-state {
            padding: 56px 24px;
            text-align: center;
        }

        .empty-icon {
            font-size: 44px;
            opacity: .25;
            margin-bottom: 12px;
        }

        .empty-title {
            font-size: 15px;
            font-weight: 700;
            color: #1A1A2E;
            margin-bottom: 5px;
        }

        html.dark .empty-title {
            color: #EEEAE2;
        }

        .empty-sub {
            font-size: 13px;
            color: #9CA3AF;
        }

        html.dark .empty-sub {
            color: #555968;
        }
    </style>
@endpush

@section('content')

    {{-- ── Page header ─────────────────────────────────────────── --}}
    <div class="flex items-center justify-between flex-wrap gap-3 mb-6">
        <div>
            <h1 class="font-['Playfair_Display'] text-[22px] font-bold text-[#1A1A2E] dark:text-[#EEEAE2] leading-tight">
                💸 Expenses
            </h1>
            <p class="font-['DM_Sans'] text-[12.5px] text-[#9CA3AF] dark:text-[#555968] mt-0.5">
                Track and manage all business expenses
            </p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.expenses.monthlyReport') }}"
                class="inline-flex items-center gap-2 px-4 py-2.5 rounded-[10px]
                  font-['DM_Sans'] text-[13px] font-semibold text-[#1D4ED8] dark:text-[#93C5FD]
                  bg-[rgba(0,48,135,0.07)] dark:bg-[rgba(0,48,135,0.2)]
                  border border-[rgba(0,48,135,0.18)] dark:border-[rgba(0,48,135,0.35)]
                  hover:bg-[rgba(0,48,135,0.14)] transition-all duration-150 no-underline">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                    <polyline points="14 2 14 8 20 8" />
                    <line x1="16" y1="13" x2="8" y2="13" />
                    <line x1="16" y1="17" x2="8" y2="17" />
                    <polyline points="10 9 9 9 8 9" />
                </svg>
                Report
            </a>
            <a href="{{ route('admin.expenses.create') }}"
                class="inline-flex items-center gap-2 px-4 py-2.5 rounded-[10px]
                  font-['DM_Sans'] text-[13px] font-bold text-white
                  bg-gradient-to-br from-[#003087] to-[#3B82F6]
                  shadow-[0_3px_12px_rgba(0,48,135,0.35)]
                  hover:-translate-y-px hover:shadow-[0_6px_18px_rgba(0,48,135,0.45)]
                  transition-all duration-150 no-underline border-0">
                <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.5"
                    viewBox="0 0 24 24">
                    <line x1="12" y1="5" x2="12" y2="19" />
                    <line x1="5" y1="12" x2="19" y2="12" />
                </svg>
                Add Expense
            </a>
        </div>
    </div>

    {{-- ── Stat cards ───────────────────────────────────────────── --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">

        <div class="stat-card stat-today">
            <div class="stat-label">Today</div>
            <div class="stat-value">${{ number_format($todayExpense, 2) }}</div>
            <div class="font-['DM_Sans'] text-[11.5px] text-[#9CA3AF] dark:text-[#555968] mt-2">
                expenses today
            </div>
        </div>

        <div class="stat-card stat-month">
            <div class="stat-label">This Month</div>
            <div class="stat-value">${{ number_format($monthExpense, 2) }}</div>
            <div class="font-['DM_Sans'] text-[11.5px] text-[#9CA3AF] dark:text-[#555968] mt-2">
                {{ now()->format('F Y') }}
            </div>
        </div>

        <div class="stat-card stat-total">
            <div class="stat-label">All Time</div>
            <div class="stat-value">${{ number_format($totalExpense, 2) }}</div>
            <div class="font-['DM_Sans'] text-[11.5px] text-[#9CA3AF] dark:text-[#555968] mt-2">
                total recorded
            </div>
        </div>

    </div>

    {{-- ── Success alert ────────────────────────────────────────── --}}
    @if (session('success'))
        <div
            class="flex items-center gap-2.5 rounded-[10px] px-4 py-3 mb-5
                bg-[rgba(22,163,74,0.08)] border border-[rgba(22,163,74,0.25)]
                font-['DM_Sans'] text-[13px] font-medium text-green-700 dark:text-[#86EFAC]">
            <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14" />
                <polyline points="22 4 12 14.01 9 11.01" />
            </svg>
            {{ session('success') }}
        </div>
    @endif

    {{-- ── Table card ───────────────────────────────────────────── --}}
    <div class="tbl-card">

        <div class="tbl-header">
            <span class="tbl-title">All Expenses</span>
            <span class="tbl-badge">{{ $expenses->total() }} records</span>
        </div>

        @if ($expenses->isEmpty())
            <div class="empty-state">
                <div class="empty-icon">💸</div>
                <div class="empty-title">No expenses yet</div>
                <div class="empty-sub">Start by adding your first expense record.</div>
            </div>
        @else
            <table class="etbl">
                <thead>
                    <tr>
                        <th class="w-10">#</th>
                        <th>Category</th>
                        <th>Title</th>
                        <th>Amount</th>
                        <th>Date</th>
                        <th>Note</th>
                        <th class="th-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($expenses as $i => $expense)
                        <tr>
                            <td><span class="row-num">{{ $expenses->firstItem() + $i }}</span></td>
                            <td>
                                <div class="cat-wrap">
                                    <span class="cat-dot"></span>
                                    <span class="cat-name">{{ $expense->category->name }}</span>
                                </div>
                            </td>
                            <td>{{ $expense->title }}</td>
                            <td><span class="amount-cell">-${{ number_format($expense->amount, 2) }}</span></td>
                            <td><span class="date-cell">{{ \Carbon\Carbon::parse($expense->date)->format('d M Y') }}</span>
                            </td>
                            <td><span class="note-cell">{{ $expense->note ?: '—' }}</span></td>
                            <td class="td-right">
                                <div class="act-wrap">
                                    <a href="{{ route('admin.expenses.edit', $expense->id) }}" class="act-btn act-edit">
                                        <svg width="11" height="11" fill="none" stroke="currentColor"
                                            stroke-width="2.2" viewBox="0 0 24 24">
                                            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7" />
                                            <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z" />
                                        </svg>
                                        Edit
                                    </a>
                                    <form action="{{ route('admin.expenses.destroy', $expense->id) }}" method="POST"
                                        onsubmit="return confirm('Delete «{{ $expense->title }}»? This cannot be undone.')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="act-btn act-delete">
                                            <svg width="11" height="11" fill="none" stroke="currentColor"
                                                stroke-width="2.2" viewBox="0 0 24 24">
                                                <polyline points="3 6 5 6 21 6" />
                                                <path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6" />
                                                <path d="M10 11v6" />
                                                <path d="M14 11v6" />
                                                <path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2" />
                                            </svg>
                                            Delete
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            {{-- Pagination --}}
            <div class="px-5 py-4 border-t border-[rgba(0,0,0,0.05)] dark:border-[rgba(255,255,255,0.05)]">
                {{ $expenses->links() }}
            </div>
        @endif

    </div>

@endsection
