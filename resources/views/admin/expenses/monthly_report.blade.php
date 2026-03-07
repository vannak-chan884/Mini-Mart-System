@extends('layouts.app')

@section('title', 'Monthly Expense Report')

@push('styles')
    <style>
        /* ── Report summary cards ───────────────────────────────── */
        .rpt-card {
            background: #fff;
            border: 1px solid rgba(0, 0, 0, 0.07);
            border-radius: 14px;
            padding: 18px 20px;
            box-shadow: 0 1px 4px rgba(0, 0, 0, 0.04);
        }

        html.dark .rpt-card {
            background: rgba(255, 255, 255, 0.03);
            border-color: rgba(255, 255, 255, 0.07);
            box-shadow: none;
        }

        .rpt-label {
            font-family: 'DM Sans', sans-serif;
            font-size: 10.5px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .8px;
            color: #9CA3AF;
            margin-bottom: 6px;
        }

        html.dark .rpt-label {
            color: #555968;
        }

        .rpt-value {
            font-family: 'IBM Plex Mono', monospace;
            font-size: 22px;
            font-weight: 700;
            color: #1A1A2E;
            line-height: 1;
        }

        html.dark .rpt-value {
            color: #EEEAE2;
        }

        /* ── Category breakdown ─────────────────────────────────── */
        .cat-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            padding: 12px 0;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        }

        html.dark .cat-row {
            border-color: rgba(255, 255, 255, 0.05);
        }

        .cat-row:last-child {
            border-bottom: none;
        }

        .cat-bar-wrap {
            flex: 1;
            height: 4px;
            border-radius: 999px;
            background: rgba(0, 0, 0, 0.06);
            overflow: hidden;
        }

        html.dark .cat-bar-wrap {
            background: rgba(255, 255, 255, 0.06);
        }

        .cat-bar {
            height: 100%;
            border-radius: 999px;
            background: linear-gradient(90deg, #003087, #3B82F6);
            transition: width .6s ease;
        }

        /* ── Table ──────────────────────────────────────────────── */
        .rtbl {
            width: 100%;
            border-collapse: collapse;
        }

        .rtbl thead th {
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

        html.dark .rtbl thead th {
            color: #555968;
            border-color: rgba(255, 255, 255, 0.06);
        }

        .rtbl tbody tr {
            border-bottom: 1px solid rgba(0, 0, 0, 0.04);
            transition: background .12s;
        }

        html.dark .rtbl tbody tr {
            border-color: rgba(255, 255, 255, 0.04);
        }

        .rtbl tbody tr:last-child {
            border-bottom: none;
        }

        .rtbl tbody tr:hover {
            background: rgba(0, 0, 0, 0.02);
        }

        html.dark .rtbl tbody tr:hover {
            background: rgba(255, 255, 255, 0.02);
        }

        .rtbl tbody td {
            padding: 12px 20px;
            font-size: 13px;
            color: #1A1A2E;
            font-family: 'DM Sans', sans-serif;
            vertical-align: middle;
        }

        html.dark .rtbl tbody td {
            color: #E8E4DC;
        }

        .rtbl tfoot td {
            padding: 12px 20px;
            font-size: 13px;
            font-weight: 700;
            color: #1A1A2E;
            font-family: 'DM Sans', sans-serif;
            border-top: 2px solid rgba(0, 0, 0, 0.08);
        }

        html.dark .rtbl tfoot td {
            color: #EEEAE2;
            border-color: rgba(255, 255, 255, 0.1);
        }
    </style>
@endpush

@section('content')

    {{-- ── Page header ─────────────────────────────────────────── --}}
    <div class="flex items-center justify-between flex-wrap gap-3 mb-6">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.expenses.index') }}"
                class="flex items-center justify-center w-8 h-8 rounded-[9px]
                  bg-[rgba(0,0,0,0.04)] dark:bg-[rgba(255,255,255,0.05)]
                  border border-[rgba(0,0,0,0.07)] dark:border-[rgba(255,255,255,0.07)]
                  text-[#6B7280] dark:text-[#8B909E]
                  hover:text-[#1A1A2E] dark:hover:text-[#EEEAE2]
                  transition-colors no-underline">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                    <polyline points="15 18 9 12 15 6" />
                </svg>
            </a>
            <div>
                <h1
                    class="font-['Playfair_Display'] text-[20px] font-bold text-[#1A1A2E] dark:text-[#EEEAE2] leading-tight">
                    Monthly Report
                </h1>
                <p class="font-['DM_Sans'] text-[12px] text-[#9CA3AF] dark:text-[#555968]">
                    Expense summary for
                    <span class="font-semibold text-[#6B7280] dark:text-[#8B909E]">
                        {{ \Carbon\Carbon::parse($month . '-01')->format('F Y') }}
                    </span>
                </p>
            </div>
        </div>

        {{-- Month picker --}}
        <form method="GET" class="flex items-center gap-2">
            <div class="relative">
                <div
                    class="absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none
                        text-[#9CA3AF] dark:text-[#555968]">
                    <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2"
                        viewBox="0 0 24 24">
                        <rect x="3" y="4" width="18" height="18" rx="2" />
                        <line x1="16" y1="2" x2="16" y2="6" />
                        <line x1="8" y1="2" x2="8" y2="6" />
                        <line x1="3" y1="10" x2="21" y2="10" />
                    </svg>
                </div>
                <input type="month" name="month" value="{{ $month }}"
                    class="pl-9 pr-4 py-2.5 rounded-[10px]
                          font-['DM_Sans'] text-[13px]
                          bg-white dark:bg-[rgba(255,255,255,0.04)]
                          border border-[rgba(0,0,0,0.1)] dark:border-[rgba(255,255,255,0.1)]
                          text-[#1A1A2E] dark:text-[#EEEAE2]
                          outline-none transition-all duration-200
                          focus:border-[rgba(59,130,246,0.5)] focus:shadow-[0_0_0_3px_rgba(59,130,246,0.1)]">
            </div>
            <button type="submit"
                class="px-4 py-2.5 rounded-[10px]
                   font-['DM_Sans'] text-[13px] font-bold text-white
                   bg-gradient-to-br from-[#003087] to-[#3B82F6]
                   shadow-[0_3px_10px_rgba(0,48,135,0.3)]
                   hover:-translate-y-px hover:shadow-[0_5px_16px_rgba(0,48,135,0.4)]
                   transition-all duration-150 border-0 cursor-pointer">
                Filter
            </button>
        </form>
    </div>

    {{-- ── Summary cards ────────────────────────────────────────── --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">

        <div class="rpt-card">
            <div class="rpt-label">Total Spent</div>
            <div class="rpt-value text-[#DC2626] dark:text-[#FCA5A5]">
                ${{ number_format($total, 2) }}
            </div>
            <div class="font-['DM_Sans'] text-[11.5px] text-[#9CA3AF] dark:text-[#555968] mt-1.5">
                across {{ $expenses->count() }} expenses
            </div>
        </div>

        <div class="rpt-card">
            <div class="rpt-label">Avg per Expense</div>
            <div class="rpt-value">
                ${{ $expenses->count() ? number_format($total / $expenses->count(), 2) : '0.00' }}
            </div>
            <div class="font-['DM_Sans'] text-[11.5px] text-[#9CA3AF] dark:text-[#555968] mt-1.5">
                average amount
            </div>
        </div>

        <div class="rpt-card">
            <div class="rpt-label">Daily Average</div>
            <div class="rpt-value">
                @php $days = \Carbon\Carbon::parse($month . '-01')->daysInMonth; @endphp
                ${{ number_format($total / $days, 2) }}
            </div>
            <div class="font-['DM_Sans'] text-[11.5px] text-[#9CA3AF] dark:text-[#555968] mt-1.5">
                per day ({{ $days }} days)
            </div>
        </div>

    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

        {{-- ── By category breakdown ───────────────────────────── --}}
        <div
            class="bg-white dark:bg-[rgba(255,255,255,0.03)]
                border border-[rgba(0,0,0,0.07)] dark:border-[rgba(255,255,255,0.07)]
                rounded-[16px] shadow-[0_1px_4px_rgba(0,0,0,0.05)] dark:shadow-none
                overflow-hidden">

            <div
                class="flex items-center justify-between px-5 py-3.5
                    border-b border-[rgba(0,0,0,0.06)] dark:border-[rgba(255,255,255,0.06)]
                    bg-[rgba(0,0,0,0.01)] dark:bg-[rgba(255,255,255,0.02)]">
                <span
                    class="font-['DM_Sans'] text-[11px] font-bold uppercase tracking-[.8px]
                         text-[#9CA3AF] dark:text-[#555968]">
                    By Category
                </span>
            </div>

            <div class="p-5">
                @php
                    $byCategory = $expenses->groupBy('category.name')->map(fn($g) => $g->sum('amount'))->sortDesc();
                    $maxCat = $byCategory->max() ?: 1;
                @endphp

                @forelse ($byCategory as $catName => $catTotal)
                    <div class="cat-row">
                        <div class="min-w-0">
                            <div
                                class="font-['DM_Sans'] text-[13px] font-semibold text-[#1A1A2E] dark:text-[#EEEAE2] truncate">
                                {{ $catName }}
                            </div>
                            <div class="cat-bar-wrap mt-1.5">
                                <div class="cat-bar" style="width:{{ round(($catTotal / $maxCat) * 100) }}%"></div>
                            </div>
                        </div>
                        <div
                            class="font-['IBM_Plex_Mono'] text-[12.5px] font-bold
                                text-[#DC2626] dark:text-[#FCA5A5] flex-shrink-0">
                            ${{ number_format($catTotal, 2) }}
                        </div>
                    </div>
                @empty
                    <p class="font-['DM_Sans'] text-[13px] text-[#9CA3AF] dark:text-[#555968] text-center py-6">
                        No data for this month.
                    </p>
                @endforelse
            </div>
        </div>

        {{-- ── Expense table ────────────────────────────────────── --}}
        <div
            class="lg:col-span-2
                bg-white dark:bg-[rgba(255,255,255,0.03)]
                border border-[rgba(0,0,0,0.07)] dark:border-[rgba(255,255,255,0.07)]
                rounded-[16px] shadow-[0_1px_4px_rgba(0,0,0,0.05)] dark:shadow-none
                overflow-hidden">

            <div
                class="flex items-center justify-between px-5 py-3.5
                    border-b border-[rgba(0,0,0,0.06)] dark:border-[rgba(255,255,255,0.06)]
                    bg-[rgba(0,0,0,0.01)] dark:bg-[rgba(255,255,255,0.02)]">
                <span
                    class="font-['DM_Sans'] text-[11px] font-bold uppercase tracking-[.8px]
                         text-[#9CA3AF] dark:text-[#555968]">
                    All Transactions
                </span>
                <span
                    class="font-['IBM_Plex_Mono'] text-[11px] text-[#9CA3AF] dark:text-[#555968]
                         bg-[rgba(0,0,0,0.04)] dark:bg-[rgba(255,255,255,0.04)]
                         border border-[rgba(0,0,0,0.06)] dark:border-[rgba(255,255,255,0.06)]
                         px-2.5 py-0.5 rounded-full">
                    {{ $expenses->count() }} records
                </span>
            </div>

            @if ($expenses->isEmpty())
                <div class="text-center py-14">
                    <div class="text-[44px] opacity-20 mb-3">📊</div>
                    <div class="font-['DM_Sans'] text-[14px] font-semibold text-[#1A1A2E] dark:text-[#EEEAE2] mb-1">
                        No expenses this month
                    </div>
                    <div class="font-['DM_Sans'] text-[12.5px] text-[#9CA3AF] dark:text-[#555968]">
                        Try selecting a different month.
                    </div>
                </div>
            @else
                <table class="rtbl">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Category</th>
                            <th>Title</th>
                            <th>Amount</th>
                            <th>Note</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($expenses as $expense)
                            <tr>
                                <td>
                                    <span class="font-['IBM_Plex_Mono'] text-[12px] text-[#6B7280] dark:text-[#555968]">
                                        {{ \Carbon\Carbon::parse($expense->date)->format('d M') }}
                                    </span>
                                </td>
                                <td>
                                    <span class="inline-flex items-center gap-1.5">
                                        <span
                                            class="w-1.5 h-1.5 rounded-full bg-gradient-to-br from-[#003087] to-[#3B82F6] flex-shrink-0"></span>
                                        {{ $expense->category->name }}
                                    </span>
                                </td>
                                <td class="font-semibold">{{ $expense->title }}</td>
                                <td>
                                    <span
                                        class="font-['IBM_Plex_Mono'] text-[13px] font-bold text-[#DC2626] dark:text-[#FCA5A5]">
                                        -${{ number_format($expense->amount, 2) }}
                                    </span>
                                </td>
                                <td>
                                    <span class="text-[12px] text-[#9CA3AF] dark:text-[#555968] italic">
                                        {{ $expense->note ?: '—' }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="3">Total for {{ \Carbon\Carbon::parse($month . '-01')->format('F Y') }}</td>
                            <td>
                                <span class="font-['IBM_Plex_Mono'] font-bold text-[#DC2626] dark:text-[#FCA5A5]">
                                    -${{ number_format($total, 2) }}
                                </span>
                            </td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            @endif

        </div>
    </div>

@endsection
