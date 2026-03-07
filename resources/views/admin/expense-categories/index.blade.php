@extends('layouts.app')

@section('title', 'Expense Categories')

@push('styles')
    <style>
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
            font-family: 'DM Sans', sans-serif;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .8px;
            color: #9CA3AF;
        }

        html.dark .tbl-title {
            color: #555968;
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
            color: #555968;
        }

        .ctbl {
            width: 100%;
            border-collapse: collapse;
        }

        .ctbl thead th {
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

        html.dark .ctbl thead th {
            color: #555968;
            border-color: rgba(255, 255, 255, 0.06);
        }

        .ctbl thead th.th-right {
            text-align: right;
        }

        .ctbl tbody tr {
            border-bottom: 1px solid rgba(0, 0, 0, 0.04);
            transition: background .12s;
        }

        html.dark .ctbl tbody tr {
            border-color: rgba(255, 255, 255, 0.04);
        }

        .ctbl tbody tr:last-child {
            border-bottom: none;
        }

        .ctbl tbody tr:hover {
            background: rgba(0, 0, 0, 0.02);
        }

        html.dark .ctbl tbody tr:hover {
            background: rgba(255, 255, 255, 0.025);
        }

        .ctbl tbody td {
            padding: 13px 20px;
            font-size: 13.5px;
            color: #1A1A2E;
            vertical-align: middle;
            font-family: 'DM Sans', sans-serif;
        }

        html.dark .ctbl tbody td {
            color: #E8E4DC;
        }

        .ctbl tbody td.td-right {
            text-align: right;
        }

        .row-num {
            font-family: 'IBM Plex Mono', monospace;
            font-size: 11px;
            color: #D1D5DB;
        }

        html.dark .row-num {
            color: #374151;
        }

        .cat-chip {
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .cat-chip-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: linear-gradient(135deg, #003087, #3B82F6);
            flex-shrink: 0;
        }

        .cat-chip-name {
            font-weight: 600;
            font-size: 13.5px;
            color: #1A1A2E;
        }

        html.dark .cat-chip-name {
            color: #EEEAE2;
        }

        .date-mono {
            font-family: 'IBM Plex Mono', monospace;
            font-size: 12px;
            color: #9CA3AF;
        }

        html.dark .date-mono {
            color: #555968;
        }

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
    </style>
@endpush

@section('content')

    {{-- ── Page header ─────────────────────────────────────────── --}}
    <div class="flex items-center justify-between flex-wrap gap-3 mb-6">
        <div>
            <h1
                class="font-['Playfair_Display'] text-[22px] font-bold
                   text-[#1A1A2E] dark:text-[#EEEAE2] leading-tight">
                🗂️ Expense Categories
            </h1>
            <p class="font-['DM_Sans'] text-[12.5px] text-[#9CA3AF] dark:text-[#555968] mt-0.5">
                Organise expenses into named categories
            </p>
        </div>
        <a href="{{ route('admin.expense-categories.create') }}"
            class="inline-flex items-center gap-2 px-4 py-2.5 rounded-[10px]
              font-['DM_Sans'] text-[13px] font-bold text-white no-underline border-0
              bg-gradient-to-br from-[#003087] to-[#3B82F6]
              shadow-[0_3px_12px_rgba(0,48,135,0.35)]
              hover:-translate-y-px hover:shadow-[0_6px_18px_rgba(0,48,135,0.45)]
              transition-all duration-150">
            <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <line x1="12" y1="5" x2="12" y2="19" />
                <line x1="5" y1="12" x2="19" y2="12" />
            </svg>
            Add Category
        </a>
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
            <span class="tbl-title">All Categories</span>
            <span class="tbl-badge">{{ $categories->count() }} total</span>
        </div>

        @if ($categories->isEmpty())
            <div class="text-center py-14">
                <div class="text-[44px] opacity-20 mb-3">🗂️</div>
                <div
                    class="font-['DM_Sans'] text-[15px] font-semibold
                        text-[#1A1A2E] dark:text-[#EEEAE2] mb-1">
                    No categories yet
                </div>
                <div class="font-['DM_Sans'] text-[13px] text-[#9CA3AF] dark:text-[#555968]">
                    Add your first category to get started.
                </div>
            </div>
        @else
            <table class="ctbl">
                <thead>
                    <tr>
                        <th class="w-10">#</th>
                        <th>Category Name</th>
                        <th>Created</th>
                        <th class="th-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($categories as $i => $category)
                        <tr>
                            <td><span class="row-num">{{ $i + 1 }}</span></td>
                            <td>
                                <div class="cat-chip">
                                    <span class="cat-chip-dot"></span>
                                    <span class="cat-chip-name">{{ $category->name }}</span>
                                </div>
                            </td>
                            <td>
                                <span class="date-mono">
                                    {{ $category->created_at->format('d M Y') }}
                                </span>
                            </td>
                            <td class="td-right">
                                <div class="act-wrap">
                                    <a href="{{ route('admin.expense-categories.edit', $category->id) }}"
                                        class="act-btn act-edit">
                                        <svg width="11" height="11" fill="none" stroke="currentColor"
                                            stroke-width="2.2" viewBox="0 0 24 24">
                                            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7" />
                                            <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z" />
                                        </svg>
                                        Edit
                                    </a>
                                    <form action="{{ route('admin.expense-categories.destroy', $category->id) }}"
                                        method="POST" class="inline"
                                        onsubmit="return confirm('Delete «{{ $category->name }}»? This cannot be undone.')">
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
        @endif

    </div>

@endsection
