@extends('layouts.app')

@section('title', 'Sales History')

@push('styles')
    <style>
        /* ── Stats cards ─────────────────────────────── */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 16px;
            margin-bottom: 28px;
        }

        .stat-card {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.07);
            border-radius: 14px;
            padding: 20px;
            position: relative;
            overflow: hidden;
            transition: border-color 0.2s;
        }

        .stat-card:hover {
            border-color: rgba(255, 255, 255, 0.12);
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 2px;
        }

        .stat-card.blue::before {
            background: linear-gradient(90deg, #003087, #1a4db3);
        }

        .stat-card.green::before {
            background: linear-gradient(90deg, #15803D, #16A34A);
        }

        .stat-card.gold::before {
            background: linear-gradient(90deg, #92400E, #F4A900);
        }

        .stat-card.red::before {
            background: linear-gradient(90deg, #991B1B, #CC0001);
        }

        .stat-card.purple::before {
            background: linear-gradient(90deg, #6D28D9, #8B5CF6);
        }

        .stat-card.teal::before {
            background: linear-gradient(90deg, #0F766E, #14B8A6);
        }

        .stat-icon {
            font-size: 22px;
            margin-bottom: 10px;
        }

        .stat-value {
            font-family: 'IBM Plex Mono', monospace;
            font-size: 22px;
            font-weight: 600;
            color: #fff;
            line-height: 1;
            margin-bottom: 4px;
        }

        .stat-label {
            font-size: 12px;
            color: #6B7280;
            font-weight: 400;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* ── Filters ─────────────────────────────────── */
        .filters-bar {
            display: flex;
            gap: 10px;
            align-items: center;
            flex-wrap: wrap;
            margin-bottom: 20px;
        }

        .filter-input {
            background: rgba(255, 255, 255, 0.04);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 9px;
            padding: 9px 14px;
            font-size: 13px;
            font-family: 'DM Sans', sans-serif;
            color: #E8E4DC;
            outline: none;
            transition: all 0.2s;
            height: 38px;
        }

        .filter-input:focus {
            border-color: rgba(0, 80, 200, 0.5);
            background: rgba(0, 48, 135, 0.08);
            box-shadow: 0 0 0 3px rgba(0, 48, 135, 0.12);
        }

        .filter-input::placeholder {
            color: rgba(156, 163, 175, 0.45);
        }

        .filter-input option {
            background: #1C1C2E;
            color: #E8E4DC;
        }

        .search-wrap {
            position: relative;
            flex: 1;
            min-width: 200px;
        }

        .search-wrap .search-icon {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 14px;
            color: #6B7280;
            pointer-events: none;
        }

        .search-wrap .filter-input {
            padding-left: 36px;
            width: 100%;
        }

        .btn-filter {
            height: 38px;
            padding: 0 16px;
            border-radius: 9px;
            font-size: 13px;
            font-weight: 600;
            font-family: 'DM Sans', sans-serif;
            cursor: pointer;
            border: none;
            transition: all 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 7px;
        }

        .btn-filter.primary {
            background: linear-gradient(135deg, #003087, #1a4db3);
            color: #fff;
            box-shadow: 0 2px 10px rgba(0, 48, 135, 0.3);
        }

        .btn-filter.primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 16px rgba(0, 48, 135, 0.4);
        }

        .btn-filter.ghost {
            background: rgba(255, 255, 255, 0.04);
            border: 1px solid rgba(255, 255, 255, 0.08);
            color: #9CA3AF;
        }

        .btn-filter.ghost:hover {
            color: #fff;
            border-color: rgba(255, 255, 255, 0.16);
        }

        /* ── Table ───────────────────────────────────── */
        .table-wrap {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.07);
            border-radius: 16px;
            overflow: hidden;
        }

        .table-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 18px 24px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.07);
        }

        .table-head-title {
            font-family: 'Playfair Display', serif;
            font-size: 16px;
            font-weight: 700;
            color: #fff;
        }

        .table-count {
            font-size: 12px;
            color: #6B7280;
            background: rgba(255, 255, 255, 0.04);
            border: 1px solid rgba(255, 255, 255, 0.07);
            padding: 3px 10px;
            border-radius: 999px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        thead th {
            padding: 12px 20px;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            color: #6B7280;
            text-align: left;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
            background: rgba(255, 255, 255, 0.02);
            white-space: nowrap;
        }

        tbody tr {
            border-bottom: 1px solid rgba(255, 255, 255, 0.04);
            transition: background 0.15s;
        }

        tbody tr:last-child {
            border-bottom: none;
        }

        tbody tr:hover {
            background: rgba(255, 255, 255, 0.03);
        }

        tbody td {
            padding: 14px 20px;
            font-size: 13.5px;
            color: #E8E4DC;
            vertical-align: middle;
        }

        /* Invoice number */
        .invoice-num {
            font-family: 'IBM Plex Mono', monospace;
            font-size: 12px;
            font-weight: 500;
            color: #93C5FD;
        }

        /* Payment badge */
        .payment-badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 3px 10px;
            border-radius: 999px;
            font-size: 11px;
            font-weight: 700;
            white-space: nowrap;
        }

        .badge-cash {
            background: rgba(22, 163, 74, 0.15);
            border: 1px solid rgba(22, 163, 74, 0.3);
            color: #86EFAC;
        }

        .badge-khqr-usd {
            background: rgba(0, 48, 135, 0.2);
            border: 1px solid rgba(0, 48, 135, 0.4);
            color: #93C5FD;
        }

        .badge-khqr-khr {
            background: rgba(204, 0, 1, 0.15);
            border: 1px solid rgba(204, 0, 1, 0.3);
            color: #FCA5A5;
        }

        /* Amount */
        .amount {
            font-family: 'IBM Plex Mono', monospace;
            font-size: 14px;
            font-weight: 600;
            color: #fff;
        }

        /* Date */
        .date-cell {
            font-size: 12.5px;
            color: #9CA3AF;
        }

        .date-cell .time {
            color: #6B7280;
            font-size: 11px;
        }

        /* Actions */
        .action-btn {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 5px 12px;
            border-radius: 7px;
            font-size: 12px;
            font-weight: 500;
            font-family: 'DM Sans', sans-serif;
            cursor: pointer;
            border: none;
            text-decoration: none;
            transition: all 0.15s;
        }

        .action-view {
            background: rgba(0, 48, 135, 0.2);
            border: 1px solid rgba(0, 48, 135, 0.35);
            color: #93C5FD;
        }

        .action-view:hover {
            background: rgba(0, 48, 135, 0.35);
        }

        .action-delete {
            background: rgba(204, 0, 1, 0.12);
            border: 1px solid rgba(204, 0, 1, 0.25);
            color: #FCA5A5;
        }

        .action-delete:hover {
            background: rgba(204, 0, 1, 0.25);
        }

        /* Empty state */
        .empty-state {
            padding: 60px 24px;
            text-align: center;
        }

        .empty-icon {
            font-size: 48px;
            margin-bottom: 16px;
            opacity: 0.4;
        }

        .empty-title {
            font-size: 16px;
            font-weight: 600;
            color: #fff;
            margin-bottom: 6px;
        }

        .empty-sub {
            font-size: 13px;
            color: #6B7280;
        }

        /* Pagination */
        .pagination-wrap {
            padding: 16px 24px;
            border-top: 1px solid rgba(255, 255, 255, 0.06);
            display: flex;
            align-items: center;
            justify-content: between;
            gap: 12px;
        }

        .pagination-wrap nav {
            width: 100%;
        }

        /* Alert */
        .alert-success {
            display: flex;
            align-items: center;
            gap: 10px;
            background: rgba(22, 163, 74, 0.1);
            border: 1px solid rgba(22, 163, 74, 0.3);
            border-radius: 10px;
            padding: 12px 16px;
            margin-bottom: 20px;
            font-size: 13px;
            color: #86EFAC;
        }
    </style>
@endpush

@section('content')

    @if (session('success'))
        <div class="alert-success">✅ {{ session('success') }}</div>
    @endif

    {{-- Stats --}}
    <div class="stats-grid">
        <div class="stat-card blue">
            <div class="stat-icon">🧾</div>
            <div class="stat-value">{{ number_format($stats['total_sales']) }}</div>
            <div class="stat-label">Total Sales</div>
        </div>
        <div class="stat-card green">
            <div class="stat-icon">💰</div>
            <div class="stat-value">${{ number_format($stats['total_revenue'], 2) }}</div>
            <div class="stat-label">Total Revenue</div>
        </div>
        <div class="stat-card teal">
            <div class="stat-icon">💵</div>
            <div class="stat-value">${{ number_format($stats['cash_revenue'], 2) }}</div>
            <div class="stat-label">Cash Revenue</div>
        </div>
        <div class="stat-card purple">
            <div class="stat-icon">📱</div>
            <div class="stat-value">${{ number_format($stats['khqr_revenue'], 2) }}</div>
            <div class="stat-label">KHQR Revenue</div>
        </div>
        <div class="stat-card gold">
            <div class="stat-icon">📅</div>
            <div class="stat-value">${{ number_format($stats['today_revenue'], 2) }}</div>
            <div class="stat-label">Today Revenue</div>
        </div>
        <div class="stat-card red">
            <div class="stat-icon">🛒</div>
            <div class="stat-value">{{ $stats['today_count'] }}</div>
            <div class="stat-label">Today Sales</div>
        </div>
    </div>

    {{-- Filters --}}
    <form method="GET" action="{{ route('admin.sales.index') }}">
        <div class="filters-bar">
            <div class="search-wrap">
                <span class="search-icon">🔍</span>
                <input type="text" name="search" class="filter-input" placeholder="Search invoice number..."
                    value="{{ request('search') }}">
            </div>

            <select name="payment" class="filter-input" style="min-width:150px;">
                <option value="">All Payments</option>
                <option value="cash" {{ request('payment') == 'cash' ? 'selected' : '' }}>💵 Cash</option>
                <option value="khqr_usd" {{ request('payment') == 'khqr_usd' ? 'selected' : '' }}>🇺🇸 KHQR USD</option>
                <option value="khqr_khr" {{ request('payment') == 'khqr_khr' ? 'selected' : '' }}>🇰🇭 KHQR KHR</option>
            </select>

            <input type="date" name="from" class="filter-input" value="{{ request('from') }}" title="From date">
            <input type="date" name="to" class="filter-input" value="{{ request('to') }}" title="To date">

            <button type="submit" class="btn-filter primary">🔍 Filter</button>
            @if (request()->hasAny(['search', 'payment', 'from', 'to']))
                <a href="{{ route('admin.sales.index') }}" class="btn-filter ghost">✕ Clear</a>
            @endif
        </div>
    </form>

    {{-- Table --}}
    <div class="table-wrap">
        <div class="table-head">
            <div class="table-head-title">🧾 Sales Records</div>
            <span class="table-count">{{ $sales->total() }} records</span>
        </div>

        @if ($sales->count())
            <table>
                <thead>
                    <tr>
                        <th>Invoice</th>
                        <th>Date & Time</th>
                        <th>Items</th>
                        <th>Payment</th>
                        <th>Total</th>
                        <th>Change</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($sales as $sale)
                        <tr>
                            <td>
                                <span class="invoice-num">{{ $sale->invoice_no }}</span>
                            </td>
                            <td>
                                <div class="date-cell">
                                    {{ \Carbon\Carbon::parse($sale->created_at)->timezone('Asia/Phnom_Penh')->format('d M Y') }}
                                    <div class="time">
                                        {{ \Carbon\Carbon::parse($sale->created_at)->timezone('Asia/Phnom_Penh')->format('h:i A') }}
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span style="font-size:13px;color:#9CA3AF;">
                                    {{ $sale->items->count() }} item{{ $sale->items->count() != 1 ? 's' : '' }}
                                </span>
                            </td>
                            <td>
                                @if ($sale->payment_method === 'cash')
                                    <span class="payment-badge badge-cash">💵 Cash</span>
                                @elseif($sale->payment_method === 'khqr_usd')
                                    <span class="payment-badge badge-khqr-usd">🇺🇸 KHQR USD</span>
                                @elseif($sale->payment_method === 'khqr_khr')
                                    <span class="payment-badge badge-khqr-khr">🇰🇭 KHQR KHR</span>
                                @else
                                    <span class="payment-badge"
                                        style="background:rgba(255,255,255,0.06);border:1px solid rgba(255,255,255,0.1);color:#9CA3AF;">
                                        {{ $sale->payment_method }}
                                    </span>
                                @endif
                            </td>
                            <td><span class="amount">${{ number_format($sale->total_amount, 2) }}</span></td>
                            <td>
                                @if ($sale->payment_method === 'cash' && $sale->change_amount > 0)
                                    <span style="font-family:'IBM Plex Mono',monospace;font-size:13px;color:#86EFAC;">
                                        ${{ number_format($sale->change_amount, 2) }}
                                    </span>
                                @else
                                    <span style="color:#4B5563;">—</span>
                                @endif
                            </td>
                            <td>
                                <div style="display:flex;gap:6px;align-items:center;">
                                    <a href="{{ route('admin.sales.show', $sale) }}" class="action-btn action-view">
                                        👁 View
                                    </a>
                                    <a href="{{ route('admin.pos.receipt', $sale) }}" target="_blank"
                                        class="action-btn action-view"
                                        style="background:rgba(22,163,74,0.15);border-color:rgba(22,163,74,0.3);color:#86EFAC;">
                                        🖨️ Receipt
                                    </a>
                                    <form method="POST" action="{{ route('admin.sales.destroy', $sale) }}">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="action-btn action-delete"
                                            onclick="return confirm('Delete this sale? This cannot be undone.')">
                                            🗑
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="pagination-wrap">
                {{ $sales->links() }}
            </div>
        @else
            <div class="empty-state">
                <div class="empty-icon">🧾</div>
                <div class="empty-title">No sales found</div>
                <div class="empty-sub">
                    {{ request()->hasAny(['search', 'payment', 'from', 'to']) ? 'Try adjusting your filters.' : 'Sales will appear here once transactions are made.' }}
                </div>
            </div>
        @endif
    </div>

@endsection
