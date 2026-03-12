@extends('layouts.app')

@section('title', 'Sales History')

@push('styles')
    <style>
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 16px; margin-bottom: 28px; }
        .stat-card { background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.07); border-radius: 14px; padding: 20px; position: relative; overflow: hidden; transition: border-color 0.2s; }
        .stat-card:hover { border-color: rgba(255,255,255,0.12); }
        .stat-card::before { content: ''; position: absolute; top: 0; left: 0; right: 0; height: 2px; }
        .stat-card.blue::before   { background: linear-gradient(90deg, #003087, #1a4db3); }
        .stat-card.green::before  { background: linear-gradient(90deg, #15803D, #16A34A); }
        .stat-card.gold::before   { background: linear-gradient(90deg, #92400E, #F4A900); }
        .stat-card.red::before    { background: linear-gradient(90deg, #991B1B, #CC0001); }
        .stat-card.purple::before { background: linear-gradient(90deg, #6D28D9, #8B5CF6); }
        .stat-card.teal::before   { background: linear-gradient(90deg, #0F766E, #14B8A6); }
        .stat-icon  { font-size: 22px; margin-bottom: 10px; }
        .stat-value { font-family: 'IBM Plex Mono', monospace; font-size: 22px; font-weight: 600; color: #fff; line-height: 1; margin-bottom: 4px; }
        .stat-label { font-size: 12px; color: #6B7280; font-weight: 400; text-transform: uppercase; letter-spacing: 0.5px; }

        .cod-alert { display: flex; align-items: center; gap: 12px; background: rgba(234,179,8,0.08); border: 1px solid rgba(234,179,8,0.25); border-radius: 12px; padding: 14px 18px; margin-bottom: 20px; font-size: 13.5px; color: #FDE68A; }
        .cod-alert-icon { font-size: 22px; }
        .cod-alert a { color: #FDE68A; font-weight: 700; text-decoration: underline; text-underline-offset: 2px; }

        .status-pills { display: flex; gap: 8px; flex-wrap: wrap; margin-bottom: 16px; }
        .status-pill { display: inline-flex; align-items: center; gap: 5px; padding: 5px 14px; border-radius: 999px; font-size: 12px; font-weight: 600; font-family: 'DM Sans', sans-serif; text-decoration: none; border: 1px solid transparent; transition: all 0.2s; cursor: pointer; }
        .pill-all        { background: rgba(255,255,255,0.06); border-color: rgba(255,255,255,0.1); color: #9CA3AF; }
        .pill-all.active, .pill-all:hover { background: rgba(255,255,255,0.12); color: #fff; }
        .pill-pending    { background: rgba(234,179,8,0.1); border-color: rgba(234,179,8,0.25); color: #FDE68A; }
        .pill-pending.active, .pill-pending:hover { background: rgba(234,179,8,0.2); }
        .pill-delivering { background: rgba(0,48,135,0.15); border-color: rgba(0,48,135,0.3); color: #93C5FD; }
        .pill-delivering.active, .pill-delivering:hover { background: rgba(0,48,135,0.25); }
        .pill-delivered  { background: rgba(22,163,74,0.1); border-color: rgba(22,163,74,0.25); color: #86EFAC; }
        .pill-delivered.active, .pill-delivered:hover { background: rgba(22,163,74,0.2); }

        .filters-bar { display: flex; gap: 10px; align-items: center; flex-wrap: wrap; margin-bottom: 20px; }
        .filter-input { background: rgba(255,255,255,0.04); border: 1px solid rgba(255,255,255,0.08); border-radius: 9px; padding: 9px 14px; font-size: 13px; font-family: 'DM Sans', sans-serif; color: #E8E4DC; outline: none; transition: all 0.2s; height: 38px; }
        .filter-input:focus { border-color: rgba(0,80,200,0.5); background: rgba(0,48,135,0.08); box-shadow: 0 0 0 3px rgba(0,48,135,0.12); }
        .filter-input::placeholder { color: rgba(156,163,175,0.45); }
        .filter-input option { background: #1C1C2E; color: #E8E4DC; }
        .search-wrap { position: relative; flex: 1; min-width: 200px; }
        .search-wrap .search-icon { position: absolute; left: 12px; top: 50%; transform: translateY(-50%); font-size: 14px; color: #6B7280; pointer-events: none; }
        .search-wrap .filter-input { padding-left: 36px; width: 100%; }
        .btn-filter { height: 38px; padding: 0 16px; border-radius: 9px; font-size: 13px; font-weight: 600; font-family: 'DM Sans', sans-serif; cursor: pointer; border: none; transition: all 0.2s; display: inline-flex; align-items: center; gap: 7px; }
        .btn-filter.primary { background: linear-gradient(135deg, #003087, #1a4db3); color: #fff; box-shadow: 0 2px 10px rgba(0,48,135,0.3); }
        .btn-filter.primary:hover { transform: translateY(-1px); }
        .btn-filter.ghost { background: rgba(255,255,255,0.04); border: 1px solid rgba(255,255,255,0.08); color: #9CA3AF; }
        .btn-filter.ghost:hover { color: #fff; border-color: rgba(255,255,255,0.16); }

        .table-wrap { background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.07); border-radius: 16px; overflow: hidden; }
        .table-head { display: flex; align-items: center; justify-content: space-between; padding: 18px 24px; border-bottom: 1px solid rgba(255,255,255,0.07); }
        .table-head-title { font-family: 'Playfair Display', serif; font-size: 16px; font-weight: 700; color: #fff; }
        .table-count { font-size: 12px; color: #6B7280; background: rgba(255,255,255,0.04); border: 1px solid rgba(255,255,255,0.07); padding: 3px 10px; border-radius: 999px; }
        table { width: 100%; border-collapse: collapse; }
        thead th { padding: 12px 20px; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.8px; color: #6B7280; text-align: left; border-bottom: 1px solid rgba(255,255,255,0.05); background: rgba(255,255,255,0.02); white-space: nowrap; }
        tbody tr { border-bottom: 1px solid rgba(255,255,255,0.04); transition: background 0.15s; }
        tbody tr:last-child { border-bottom: none; }
        tbody tr:hover { background: rgba(255,255,255,0.03); }
        tbody td { padding: 14px 20px; font-size: 13.5px; color: #E8E4DC; vertical-align: middle; }
        .invoice-num { font-family: 'IBM Plex Mono', monospace; font-size: 12px; font-weight: 500; color: #93C5FD; }

        .payment-badge { display: inline-flex; align-items: center; gap: 5px; padding: 3px 10px; border-radius: 999px; font-size: 11px; font-weight: 700; white-space: nowrap; }
        .badge-cash     { background: rgba(22,163,74,0.15); border: 1px solid rgba(22,163,74,0.3); color: #86EFAC; }
        .badge-khqr-usd { background: rgba(0,48,135,0.2); border: 1px solid rgba(0,48,135,0.4); color: #93C5FD; }
        .badge-khqr-khr { background: rgba(204,0,1,0.15); border: 1px solid rgba(204,0,1,0.3); color: #FCA5A5; }

        /* Delivery badge */
        .delivery-badge { display: inline-flex; align-items: center; gap: 5px; padding: 3px 10px; border-radius: 999px; font-size: 11px; font-weight: 700; white-space: nowrap; }
        .d-pending    { background: rgba(234,179,8,0.12); border: 1px solid rgba(234,179,8,0.3); color: #FDE68A; }
        .d-delivering { background: rgba(0,48,135,0.15); border: 1px solid rgba(0,48,135,0.35); color: #93C5FD; }
        .d-delivered  { background: rgba(22,163,74,0.12); border: 1px solid rgba(22,163,74,0.3); color: #86EFAC; }

        /* Payment status badge */
        .status-badge { display: inline-flex; align-items: center; gap: 5px; padding: 3px 10px; border-radius: 999px; font-size: 11px; font-weight: 700; white-space: nowrap; }
        .s-pending    { background: rgba(234,179,8,0.12); border: 1px solid rgba(234,179,8,0.3); color: #FDE68A; }
        .s-paid       { background: rgba(22,163,74,0.12); border: 1px solid rgba(22,163,74,0.3); color: #86EFAC; }
        .s-cancelled  { background: rgba(204,0,1,0.12); border: 1px solid rgba(204,0,1,0.3); color: #FCA5A5; }

        .amount { font-family: 'IBM Plex Mono', monospace; font-size: 14px; font-weight: 600; color: #fff; }
        .date-cell { font-size: 12.5px; color: #9CA3AF; }
        .date-cell .time { color: #6B7280; font-size: 11px; }

        .action-btn { display: inline-flex; align-items: center; gap: 5px; padding: 5px 12px; border-radius: 7px; font-size: 12px; font-weight: 500; font-family: 'DM Sans', sans-serif; cursor: pointer; border: none; text-decoration: none; transition: all 0.15s; }
        .action-view   { background: rgba(0,48,135,0.2); border: 1px solid rgba(0,48,135,0.35); color: #93C5FD; }
        .action-view:hover { background: rgba(0,48,135,0.35); }
        .action-update { background: rgba(234,179,8,0.12); border: 1px solid rgba(234,179,8,0.3); color: #FDE68A; }
        .action-update:hover { background: rgba(234,179,8,0.22); }
        .action-print  { background: rgba(22,163,74,0.15); border: 1px solid rgba(22,163,74,0.3); color: #86EFAC; }
        .action-print:hover { background: rgba(22,163,74,0.25); }
        .action-delete { background: rgba(204,0,1,0.12); border: 1px solid rgba(204,0,1,0.25); color: #FCA5A5; }
        .action-delete:hover { background: rgba(204,0,1,0.25); }

        .empty-state { padding: 60px 24px; text-align: center; }
        .empty-icon  { font-size: 48px; margin-bottom: 16px; opacity: 0.4; }
        .empty-title { font-size: 16px; font-weight: 600; color: #fff; margin-bottom: 6px; }
        .empty-sub   { font-size: 13px; color: #6B7280; }

        .pagination-wrap { padding: 16px 24px; border-top: 1px solid rgba(255,255,255,0.06); }
        .alert-success { display: flex; align-items: center; gap: 10px; background: rgba(22,163,74,0.1); border: 1px solid rgba(22,163,74,0.3); border-radius: 10px; padding: 12px 16px; margin-bottom: 20px; font-size: 13px; color: #86EFAC; }

        /* Modal */
        .modal-overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.7); z-index: 999; align-items: center; justify-content: center; }
        .modal-overlay.open { display: flex; }
        .modal-box { background: #1C1C2E; border: 1px solid rgba(255,255,255,0.1); border-radius: 18px; padding: 28px; width: 100%; max-width: 460px; margin: 16px; }
        .modal-title { font-family: 'Playfair Display', serif; font-size: 18px; font-weight: 700; color: #fff; margin-bottom: 4px; }
        .modal-sub   { font-size: 13px; color: #6B7280; margin-bottom: 20px; }
        .modal-label { font-size: 12px; font-weight: 600; color: #9CA3AF; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 6px; display: block; }
        .modal-select, .modal-input { width: 100%; background: rgba(255,255,255,0.04); border: 1px solid rgba(255,255,255,0.08); border-radius: 9px; padding: 10px 14px; font-size: 13px; font-family: 'DM Sans', sans-serif; color: #E8E4DC; outline: none; margin-bottom: 14px; }
        .modal-select option { background: #1C1C2E; }
        .modal-file { width: 100%; font-size: 13px; color: #9CA3AF; margin-bottom: 14px; }
        .modal-preview { display: none; margin-bottom: 14px; }
        .modal-preview img { max-height: 120px; border-radius: 10px; border: 1px solid rgba(255,255,255,0.1); }
        .modal-actions { display: flex; gap: 10px; margin-top: 4px; }
        .modal-btn { flex: 1; height: 42px; border-radius: 10px; font-size: 13px; font-weight: 600; font-family: 'DM Sans', sans-serif; cursor: pointer; border: none; transition: all 0.2s; }
        .modal-btn.confirm { background: linear-gradient(135deg, #003087, #1a4db3); color: #fff; }
        .modal-btn.confirm:hover { transform: translateY(-1px); }
        .modal-btn.cancel  { background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.08); color: #9CA3AF; }
        #paymentFields { display: none; }
        .modal-tabs { display: flex; gap: 8px; margin-bottom: 18px; }
        .modal-tab { flex: 1; padding: 8px; border-radius: 9px; font-size: 12px; font-weight: 600; font-family: 'DM Sans', sans-serif; cursor: pointer; border: 1px solid rgba(255,255,255,0.08); background: rgba(255,255,255,0.04); color: #9CA3AF; text-align: center; transition: all 0.2s; }
        .modal-tab.active { background: rgba(0,48,135,0.3); border-color: rgba(0,48,135,0.5); color: #93C5FD; }
    </style>
@endpush

@section('content')

    @if(session('success'))
        <div class="alert-success">✅ {{ session('success') }}</div>
    @endif

    {{-- Pending delivery alert --}}
    @if($pendingDeliveryCount > 0)
    <div class="cod-alert">
        <span class="cod-alert-icon">🔔</span>
        <div>
            <strong>{{ $pendingDeliveryCount }} order{{ $pendingDeliveryCount > 1 ? 's' : '' }}</strong>
            pending delivery confirmation.
            <a href="{{ route('admin.sales.index', ['delivery_status' => 'pending']) }}">View →</a>
        </div>
    </div>
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

    {{-- Delivery status filter pills --}}
    <div class="status-pills">
        <a href="{{ route('admin.sales.index', request()->except('delivery_status')) }}"
            class="status-pill pill-all {{ !request('delivery_status') ? 'active' : '' }}">All Orders</a>
        <a href="{{ route('admin.sales.index', array_merge(request()->all(), ['delivery_status' => 'pending'])) }}"
            class="status-pill pill-pending {{ request('delivery_status') === 'pending' ? 'active' : '' }}">📦 Preparing</a>
        <a href="{{ route('admin.sales.index', array_merge(request()->all(), ['delivery_status' => 'delivering'])) }}"
            class="status-pill pill-delivering {{ request('delivery_status') === 'delivering' ? 'active' : '' }}">🚚 Delivering</a>
        <a href="{{ route('admin.sales.index', array_merge(request()->all(), ['delivery_status' => 'delivered'])) }}"
            class="status-pill pill-delivered {{ request('delivery_status') === 'delivered' ? 'active' : '' }}">✅ Delivered</a>
    </div>

    {{-- Filters --}}
    <form method="GET" action="{{ route('admin.sales.index') }}">
        @if(request('delivery_status'))
            <input type="hidden" name="delivery_status" value="{{ request('delivery_status') }}">
        @endif
        <div class="filters-bar">
            <div class="search-wrap">
                <span class="search-icon">🔍</span>
                <input type="text" name="search" class="filter-input" placeholder="Search invoice number..." value="{{ request('search') }}">
            </div>
            <select name="payment" class="filter-input" style="min-width:150px;">
                <option value="">All Payments</option>
                <option value="cash"     {{ request('payment') == 'cash'     ? 'selected' : '' }}>💵 Cash</option>
                <option value="khqr_usd" {{ request('payment') == 'khqr_usd' ? 'selected' : '' }}>🇺🇸 KHQR USD</option>
                <option value="khqr_khr" {{ request('payment') == 'khqr_khr' ? 'selected' : '' }}>🇰🇭 KHQR KHR</option>
            </select>
            <input type="date" name="from" class="filter-input" value="{{ request('from') }}">
            <input type="date" name="to"   class="filter-input" value="{{ request('to') }}">
            <button type="submit" class="btn-filter primary">🔍 Filter</button>
            @if(request()->hasAny(['search', 'payment', 'from', 'to']))
                <a href="{{ route('admin.sales.index', request()->only('delivery_status')) }}" class="btn-filter ghost">✕ Clear</a>
            @endif
        </div>
    </form>

    {{-- Table --}}
    <div class="table-wrap">
        <div class="table-head">
            <div class="table-head-title">🧾 Sales Records</div>
            <span class="table-count">{{ $sales->total() }} records</span>
        </div>

        @if($sales->count())
        <table>
            <thead>
                <tr>
                    <th>Invoice</th>
                    <th>Date & Time</th>
                    <th>Customer</th>
                    <th>Payment</th>
                    <th>Payment Status</th>
                    <th>Delivery</th>
                    <th>Total</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($sales as $sale)
                <tr>
                    <td><span class="invoice-num">{{ $sale->invoice_no }}</span></td>
                    <td>
                        <div class="date-cell">
                            {{ \Carbon\Carbon::parse($sale->created_at)->timezone('Asia/Phnom_Penh')->format('d M Y') }}
                            <div class="time">{{ \Carbon\Carbon::parse($sale->created_at)->timezone('Asia/Phnom_Penh')->format('h:i A') }}</div>
                        </div>
                    </td>
                    <td style="font-size:13px;color:#9CA3AF;">{{ $sale->user?->name ?? 'Guest' }}</td>
                    <td>
                        @if($sale->payment_method === 'cash')
                            <span class="payment-badge badge-cash">💵 COD</span>
                        @elseif($sale->payment_method === 'khqr_usd')
                            <span class="payment-badge badge-khqr-usd">🇺🇸 KHQR</span>
                        @elseif($sale->payment_method === 'khqr_khr')
                            <span class="payment-badge badge-khqr-khr">🇰🇭 KHQR</span>
                        @else
                            <span class="payment-badge badge-khqr-usd">🏦 KHQR</span>
                        @endif
                    </td>
                    <td>
                        @php $sc = match($sale->status) {
                            'pending'   => ['s-pending',   '🟡 Pending'],
                            'paid'      => ['s-paid',      '✅ Paid'],
                            'cancelled' => ['s-cancelled', '❌ Cancelled'],
                            default     => ['',            $sale->status],
                        }; @endphp
                        <span class="status-badge {{ $sc[0] }}">{{ $sc[1] }}</span>
                    </td>
                    <td>
                        @php $dc = match($sale->delivery_status ?? 'pending') {
                            'pending'    => ['d-pending',    '📦 Preparing'],
                            'delivering' => ['d-delivering', '🚚 Delivering'],
                            'delivered'  => ['d-delivered',  '✅ Delivered'],
                            default      => ['d-pending',    '📦 Preparing'],
                        }; @endphp
                        <span class="delivery-badge {{ $dc[0] }}">{{ $dc[1] }}</span>
                    </td>
                    <td><span class="amount">${{ number_format($sale->total_amount, 2) }}</span></td>
                    <td>
                        <div style="display:flex;gap:6px;align-items:center;flex-wrap:wrap;">
                            <a href="{{ route('admin.sales.show', $sale) }}" class="action-btn action-view">👁 View</a>
                            <a href="{{ route('admin.pos.receipt', $sale) }}" target="_blank" class="action-btn action-print">🖨️</a>
                            @if($sale->status !== 'cancelled' && ($sale->delivery_status ?? 'pending') !== 'delivered')
                            <button type="button" class="action-btn action-update"
                                onclick="openModal({{ $sale->id }}, '{{ $sale->invoice_no }}', '{{ $sale->status }}', '{{ $sale->delivery_status ?? 'pending' }}', '{{ $sale->payment_method }}')">
                                ⚙️ Update
                            </button>
                            @endif
                            <form method="POST" action="{{ route('admin.sales.destroy', $sale) }}">
                                @csrf @method('DELETE')
                                <button type="submit" class="action-btn action-delete" onclick="return confirm('Delete this sale?')">🗑</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div class="pagination-wrap">{{ $sales->links() }}</div>
        @else
        <div class="empty-state">
            <div class="empty-icon">🧾</div>
            <div class="empty-title">No sales found</div>
            <div class="empty-sub">{{ request()->hasAny(['search', 'payment', 'from', 'to', 'delivery_status']) ? 'Try adjusting your filters.' : 'Sales will appear here once transactions are made.' }}</div>
        </div>
        @endif
    </div>

    {{-- Update Modal --}}
    <div class="modal-overlay" id="statusModal">
        <div class="modal-box">
            <div class="modal-title">⚙️ Update Order</div>
            <div class="modal-sub">Invoice: <span id="modalInvoice" style="color:#93C5FD;font-family:'IBM Plex Mono',monospace;"></span></div>

            {{-- Tabs --}}
            <div class="modal-tabs">
                <div class="modal-tab active" id="tab-delivery" onclick="switchTab('delivery')">🚚 Delivery</div>
                <div class="modal-tab" id="tab-payment" onclick="switchTab('payment')">💳 Payment</div>
            </div>

            {{-- Delivery form --}}
            <form id="deliveryForm" method="POST" style="display:block;">
                @csrf @method('PATCH')
                <label class="modal-label">Delivery Status</label>
                <select name="delivery_status" id="modalDeliveryStatus" class="modal-select">
                    <option value="pending">📦 Preparing</option>
                    <option value="delivering">🚚 Out for Delivery</option>
                    <option value="delivered">✅ Delivered to Customer</option>
                </select>
                <div class="modal-actions">
                    <button type="button" class="modal-btn cancel" onclick="closeModal()">Cancel</button>
                    <button type="submit" class="modal-btn confirm">Update Delivery</button>
                </div>
            </form>

            {{-- Payment form (cash only) --}}
            <form id="paymentForm" method="POST" enctype="multipart/form-data" style="display:none;">
                @csrf @method('PATCH')
                <label class="modal-label">Payment Status</label>
                <select name="status" id="modalPaymentStatus" class="modal-select" onchange="togglePaymentFields()">
                    <option value="pending">🟡 Pending</option>
                    <option value="paid">✅ Paid — Payment Received</option>
                    <option value="cancelled">❌ Cancelled</option>
                </select>
                <div id="paymentFields">
                    <label class="modal-label">Payment Reference <span style="color:#4B5563;text-transform:none;font-weight:400;">(optional)</span></label>
                    <input type="text" name="payment_reference" class="modal-input" placeholder="ABA TXN #123456, Cash collected..." />
                    <label class="modal-label">Payment Photo <span style="color:#4B5563;text-transform:none;font-weight:400;">(optional)</span></label>
                    <input type="file" name="payment_proof" class="modal-file" accept="image/*" onchange="previewPhoto(this)" />
                    <div class="modal-preview" id="photoPreview">
                        <img id="previewImg" src="" />
                    </div>
                </div>
                <div class="modal-actions">
                    <button type="button" class="modal-btn cancel" onclick="closeModal()">Cancel</button>
                    <button type="submit" class="modal-btn confirm">Update Payment</button>
                </div>
            </form>

            <div id="khqrNote" style="display:none;font-size:12px;color:#6B7280;text-align:center;padding:12px 0;">
                KHQR payment is confirmed automatically.<br>Only delivery can be updated.
            </div>
        </div>
    </div>

@endsection

@push('scripts')
<script>
let currentPaymentMethod = 'cash';

function openModal(saleId, invoiceNo, paymentStatus, deliveryStatus, paymentMethod) {
    currentPaymentMethod = paymentMethod;
    document.getElementById('modalInvoice').textContent = invoiceNo;
    document.getElementById('modalDeliveryStatus').value = deliveryStatus;
    document.getElementById('modalPaymentStatus').value  = paymentStatus;
    document.getElementById('deliveryForm').action  = `/admin/sales/${saleId}/delivery`;
    document.getElementById('paymentForm').action   = `/admin/sales/${saleId}/status`;

    // Show payment tab only for cash
    const paymentTab = document.getElementById('tab-payment');
    if (paymentMethod === 'cash') {
        paymentTab.style.display = 'flex';
        document.getElementById('khqrNote').style.display = 'none';
    } else {
        paymentTab.style.display = 'none';
        document.getElementById('khqrNote').style.display = 'block';
    }

    switchTab('delivery');
    togglePaymentFields();
    document.getElementById('statusModal').classList.add('open');
}

function closeModal() {
    document.getElementById('statusModal').classList.remove('open');
}

function switchTab(tab) {
    document.getElementById('tab-delivery').classList.toggle('active', tab === 'delivery');
    document.getElementById('tab-payment').classList.toggle('active', tab === 'payment');
    document.getElementById('deliveryForm').style.display  = tab === 'delivery' ? 'block' : 'none';
    document.getElementById('paymentForm').style.display   = tab === 'payment'  ? 'block' : 'none';
}

function togglePaymentFields() {
    const status = document.getElementById('modalPaymentStatus').value;
    document.getElementById('paymentFields').style.display = status === 'paid' ? 'block' : 'none';
}

function previewPhoto(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => {
            document.getElementById('previewImg').src = e.target.result;
            document.getElementById('photoPreview').style.display = 'block';
        };
        reader.readAsDataURL(input.files[0]);
    }
}

document.getElementById('statusModal').addEventListener('click', function(e) {
    if (e.target === this) closeModal();
});
</script>
@endpush