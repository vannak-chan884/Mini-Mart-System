@extends('layouts.app')

@section('title', 'Sale — ' . $sale->invoice_no)

@push('styles')
    <style>
        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            font-size: 13px;
            color: #6B7280;
            text-decoration: none;
            margin-bottom: 24px;
            transition: color 0.15s;
        }

        .back-link:hover {
            color: #E8E4DC;
        }

        .detail-grid {
            display: grid;
            grid-template-columns: 1fr 360px;
            gap: 20px;
            align-items: start;
        }

        .card {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.07);
            border-radius: 16px;
            overflow: hidden;
            margin-bottom: 16px;
        }

        .card-header {
            padding: 18px 24px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.06);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .card-title {
            font-family: 'Playfair Display', serif;
            font-size: 15px;
            font-weight: 700;
            color: #fff;
        }

        .card-body {
            padding: 24px;
        }

        /* Dual status banners */
        .status-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
            margin-bottom: 20px;
        }

        .status-banner {
            display: flex;
            align-items: center;
            gap: 12px;
            border-radius: 14px;
            padding: 14px 16px;
            border: 1px solid transparent;
        }

        .status-banner.payment-pending {
            background: rgba(234, 179, 8, 0.08);
            border-color: rgba(234, 179, 8, 0.25);
            color: #FDE68A;
        }

        .status-banner.payment-paid {
            background: rgba(22, 163, 74, 0.08);
            border-color: rgba(22, 163, 74, 0.25);
            color: #86EFAC;
        }

        .status-banner.payment-cancelled {
            background: rgba(204, 0, 1, 0.08);
            border-color: rgba(204, 0, 1, 0.25);
            color: #FCA5A5;
        }

        .status-banner.delivery-pending {
            background: rgba(234, 179, 8, 0.08);
            border-color: rgba(234, 179, 8, 0.25);
            color: #FDE68A;
        }

        .status-banner.delivery-delivering {
            background: rgba(0, 48, 135, 0.12);
            border-color: rgba(0, 48, 135, 0.3);
            color: #93C5FD;
        }

        .status-banner.delivery-delivered {
            background: rgba(22, 163, 74, 0.08);
            border-color: rgba(22, 163, 74, 0.25);
            color: #86EFAC;
        }

        .status-icon {
            font-size: 24px;
        }

        .status-label {
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            opacity: 0.6;
            margin-bottom: 2px;
        }

        .status-title {
            font-size: 13px;
            font-weight: 700;
        }

        /* Delivery timeline */
        .timeline {
            position: relative;
            padding-left: 28px;
        }

        .timeline::before {
            content: '';
            position: absolute;
            left: 10px;
            top: 8px;
            bottom: 8px;
            width: 2px;
            background: rgba(255, 255, 255, 0.06);
        }

        .timeline-step {
            position: relative;
            margin-bottom: 20px;
        }

        .timeline-step:last-child {
            margin-bottom: 0;
        }

        .timeline-dot {
            position: absolute;
            left: -24px;
            top: 2px;
            width: 20px;
            height: 20px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 10px;
            border: 2px solid rgba(255, 255, 255, 0.08);
            background: rgba(255, 255, 255, 0.04);
            transition: all 0.3s;
        }

        .timeline-dot.done {
            background: rgba(0, 48, 135, 0.5);
            border-color: #3B82F6;
        }

        .timeline-dot.current {
            background: rgba(234, 179, 8, 0.3);
            border-color: #FBBF24;
            box-shadow: 0 0 0 3px rgba(234, 179, 8, 0.15);
        }

        .timeline-label {
            font-size: 13px;
            font-weight: 600;
            color: #9CA3AF;
        }

        .timeline-label.done,
        .timeline-label.current {
            color: #E8E4DC;
        }

        .timeline-sub {
            font-size: 11px;
            color: #4B5563;
            margin-top: 2px;
        }

        /* Meta */
        .meta-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
            margin-bottom: 20px;
        }

        .meta-label {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.7px;
            color: #6B7280;
            margin-bottom: 4px;
        }

        .meta-value {
            font-size: 14px;
            color: #E8E4DC;
            font-weight: 500;
        }

        .meta-value.mono {
            font-family: 'IBM Plex Mono', monospace;
            font-size: 13px;
            color: #93C5FD;
        }

        .payment-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 4px 12px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 700;
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

        table {
            width: 100%;
            border-collapse: collapse;
        }

        thead th {
            padding: 10px 16px;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.7px;
            color: #6B7280;
            text-align: left;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
            background: rgba(255, 255, 255, 0.02);
        }

        tbody tr {
            border-bottom: 1px solid rgba(255, 255, 255, 0.04);
        }

        tbody tr:last-child {
            border-bottom: none;
        }

        tbody td {
            padding: 12px 16px;
            font-size: 13.5px;
            color: #E8E4DC;
            vertical-align: middle;
        }

        tfoot td {
            padding: 10px 16px;
            font-size: 13px;
            border-top: 1px solid rgba(255, 255, 255, 0.07);
        }

        .product-name {
            font-weight: 500;
            color: #fff;
        }

        .product-cat {
            font-size: 11px;
            color: #6B7280;
            margin-top: 2px;
        }

        .qty,
        .price {
            font-family: 'IBM Plex Mono', monospace;
            font-size: 13px;
            color: #9CA3AF;
        }

        .subtotal {
            font-family: 'IBM Plex Mono', monospace;
            font-size: 14px;
            font-weight: 600;
            color: #fff;
        }

        .totals-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 8px 0;
            font-size: 13px;
            color: #9CA3AF;
            border-bottom: 1px solid rgba(255, 255, 255, 0.04);
        }

        .totals-row:last-child {
            border-bottom: none;
        }

        .totals-row.grand {
            font-size: 16px;
            font-weight: 700;
            color: #fff;
            padding-top: 14px;
        }

        .totals-row .val {
            font-family: 'IBM Plex Mono', monospace;
            font-size: 14px;
            font-weight: 600;
        }

        .totals-row.grand .val {
            font-size: 18px;
            color: #fff;
        }

        .totals-row.change .val {
            color: #86EFAC;
        }

        .proof-img {
            max-height: 200px;
            border-radius: 10px;
            border: 1px solid rgba(255, 255, 255, 0.08);
            cursor: pointer;
            transition: opacity 0.2s;
        }

        .proof-img:hover {
            opacity: 0.85;
        }

        /* Update cards */
        .update-card {
            border-radius: 16px;
            overflow: hidden;
            margin-bottom: 16px;
        }

        .update-card.delivery {
            background: rgba(0, 48, 135, 0.05);
            border: 1px solid rgba(0, 48, 135, 0.2);
        }

        .update-card.payment {
            background: rgba(234, 179, 8, 0.04);
            border: 1px solid rgba(234, 179, 8, 0.2);
        }

        .update-header {
            padding: 14px 20px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.06);
        }

        .update-title.delivery {
            font-size: 14px;
            font-weight: 700;
            color: #93C5FD;
        }

        .update-title.payment {
            font-size: 14px;
            font-weight: 700;
            color: #FDE68A;
        }

        .update-body {
            padding: 20px;
        }

        .form-label {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #9CA3AF;
            margin-bottom: 6px;
            display: block;
        }

        .form-select,
        .form-input {
            width: 100%;
            background: rgba(255, 255, 255, 0.04);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 9px;
            padding: 10px 14px;
            font-size: 13px;
            font-family: 'DM Sans', sans-serif;
            color: #E8E4DC;
            outline: none;
            margin-bottom: 14px;
        }

        .form-select option {
            background: #1C1C2E;
        }

        .form-input::placeholder {
            color: rgba(156, 163, 175, 0.45);
        }

        .form-file {
            width: 100%;
            font-size: 13px;
            color: #9CA3AF;
            margin-bottom: 14px;
        }

        .form-preview {
            display: none;
            margin-bottom: 14px;
        }

        .form-preview img {
            max-height: 120px;
            border-radius: 10px;
            border: 1px solid rgba(255, 255, 255, 0.08);
        }

        #paymentFields {
            display: none;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            padding: 10px 18px;
            border-radius: 10px;
            font-size: 13px;
            font-weight: 600;
            font-family: 'DM Sans', sans-serif;
            cursor: pointer;
            border: none;
            text-decoration: none;
            transition: all 0.2s;
        }

        .btn-primary {
            background: linear-gradient(135deg, #003087, #1a4db3);
            color: #fff;
            box-shadow: 0 2px 10px rgba(0, 48, 135, 0.3);
        }

        .btn-primary:hover {
            transform: translateY(-1px);
        }

        .btn-blue {
            background: rgba(0, 48, 135, 0.2);
            border: 1px solid rgba(0, 48, 135, 0.35);
            color: #93C5FD;
        }

        .btn-blue:hover {
            background: rgba(0, 48, 135, 0.3);
        }

        .btn-amber {
            background: rgba(234, 179, 8, 0.15);
            border: 1px solid rgba(234, 179, 8, 0.3);
            color: #FDE68A;
        }

        .btn-amber:hover {
            background: rgba(234, 179, 8, 0.25);
        }

        .btn-green {
            background: rgba(22, 163, 74, 0.2);
            border: 1px solid rgba(22, 163, 74, 0.35);
            color: #86EFAC;
        }

        .btn-green:hover {
            background: rgba(22, 163, 74, 0.3);
        }

        .btn-danger {
            background: rgba(204, 0, 1, 0.12);
            border: 1px solid rgba(204, 0, 1, 0.25);
            color: #FCA5A5;
        }

        .btn-danger:hover {
            background: rgba(204, 0, 1, 0.25);
        }

        .btn-full {
            width: 100%;
            justify-content: center;
            margin-bottom: 10px;
        }

        .khqr-box {
            background: rgba(0, 48, 135, 0.1);
            border: 1px solid rgba(0, 48, 135, 0.25);
            border-radius: 12px;
            padding: 16px;
            margin-top: 16px;
        }

        .khqr-box-title {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.7px;
            color: #93C5FD;
            margin-bottom: 10px;
        }

        .khqr-row {
            display: flex;
            justify-content: space-between;
            font-size: 12px;
            color: #6B7280;
            padding: 4px 0;
        }

        .khqr-row .v {
            font-family: 'IBM Plex Mono', monospace;
            font-size: 11px;
            color: #9CA3AF;
            max-width: 180px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

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

        .lightbox {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.85);
            z-index: 9999;
            align-items: center;
            justify-content: center;
        }

        .lightbox.open {
            display: flex;
        }

        .lightbox img {
            max-width: 90vw;
            max-height: 90vh;
            border-radius: 12px;
        }

        @media (max-width: 900px) {
            .detail-grid {
                grid-template-columns: 1fr;
            }

            .meta-grid {
                grid-template-columns: 1fr;
            }

            .status-row {
                grid-template-columns: 1fr;
            }
        }
    </style>
@endpush

@section('content')

    <a href="{{ route('admin.sales.index') }}" class="back-link">← Back to Sales History</a>

    @if (session('success'))
        <div class="alert-success">✅ {{ session('success') }}</div>
    @endif

    {{-- Dual status banners: Payment + Delivery --}}
    @php
        $paymentClass = match ($sale->status) {
            'paid' => 'payment-paid',
            'cancelled' => 'payment-cancelled',
            default => 'payment-pending',
        };
        $paymentIcon = match ($sale->status) {
            'paid' => '✅',
            'cancelled' => '❌',
            default => '🟡',
        };
        $paymentTitle = match ($sale->status) {
            'paid' => 'Payment Confirmed',
            'cancelled' => 'Cancelled',
            default => 'Payment Pending',
        };
        $deliveryStatus = $sale->delivery_status ?? 'pending';
        $deliveryClass = match ($deliveryStatus) {
            'delivering' => 'delivery-delivering',
            'delivered' => 'delivery-delivered',
            default => 'delivery-pending',
        };
        $deliveryIcon = match ($deliveryStatus) {
            'delivering' => '🚚',
            'delivered' => '📬',
            default => '📦',
        };
        $deliveryTitle = match ($deliveryStatus) {
            'delivering' => 'Out for Delivery',
            'delivered' => 'Delivered',
            default => 'Preparing',
        };
    @endphp
    <div class="status-row">
        <div class="status-banner {{ $paymentClass }}">
            <span class="status-icon">{{ $paymentIcon }}</span>
            <div>
                <div class="status-label">Payment</div>
                <div class="status-title">{{ $paymentTitle }}</div>
            </div>
        </div>
        <div class="status-banner {{ $deliveryClass }}">
            <span class="status-icon">{{ $deliveryIcon }}</span>
            <div>
                <div class="status-label">Delivery</div>
                <div class="status-title">{{ $deliveryTitle }}</div>
            </div>
        </div>
    </div>

    <div class="detail-grid">

        {{-- LEFT --}}
        <div>
            {{-- Items --}}
            <div class="card">
                <div class="card-header">
                    <div class="card-title">🛍️ Items Purchased</div>
                    <span style="font-size:12px;color:#6B7280;">{{ $sale->items->count() }}
                        item{{ $sale->items->count() != 1 ? 's' : '' }}</span>
                </div>
                <table>
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Qty</th>
                            <th>Unit Price</th>
                            <th>Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($sale->items as $item)
                            <tr>
                                <td>
                                    <div class="product-name">{{ $item->product?->name ?? 'Deleted Product' }}</div>
                                    @if ($item->product?->category)
                                        <div class="product-cat">{{ $item->product->category->name }}</div>
                                    @endif
                                </td>
                                <td><span class="qty">× {{ $item->quantity }}</span></td>
                                <td><span class="price">${{ number_format($item->price, 2) }}</span></td>
                                <td><span class="subtotal">${{ number_format($item->price * $item->quantity, 2) }}</span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="3"
                                style="text-align:right;color:#6B7280;font-size:12px;font-weight:600;text-transform:uppercase;letter-spacing:0.5px;">
                                Grand Total</td>
                            <td style="font-family:'IBM Plex Mono',monospace;font-size:16px;font-weight:700;color:#fff;">
                                ${{ number_format($sale->total_amount, 2) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            {{-- Notes --}}
            @if ($sale->notes)
                <div class="card">
                    <div class="card-header">
                        <div class="card-title">📝 Order Notes</div>
                    </div>
                    <div class="card-body" style="font-size:13px;color:#9CA3AF;font-style:italic;">{{ $sale->notes }}
                    </div>
                </div>
            @endif

            {{-- Payment proof --}}
            @if ($sale->payment_proof || $sale->payment_reference)
                <div class="card">
                    <div class="card-header">
                        <div class="card-title">🧾 Payment Proof</div>
                    </div>
                    <div class="card-body">
                        @if ($sale->payment_reference)
                            <div style="margin-bottom:12px;">
                                <div class="meta-label">Reference</div>
                                <div
                                    style="font-family:'IBM Plex Mono',monospace;font-size:13px;color:#FDE68A;background:rgba(234,179,8,0.08);border:1px solid rgba(234,179,8,0.2);padding:8px 14px;border-radius:8px;display:inline-block;">
                                    {{ $sale->payment_reference }}
                                </div>
                            </div>
                        @endif
                        @if ($sale->payment_proof)
                            <div class="meta-label" style="margin-bottom:8px;">Transaction Photo</div>
                            <img src="{{ asset('storage/' . $sale->payment_proof) }}" class="proof-img"
                                onclick="document.getElementById('lightbox').classList.add('open')" />
                        @endif
                        @if ($sale->confirmed_at)
                            <div style="font-size:11px;color:#4B5563;margin-top:10px;">
                                Confirmed by {{ $sale->confirmedBy?->name }} on
                                {{ $sale->confirmed_at->timezone('Asia/Phnom_Penh')->format('d M Y, h:i A') }}
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            {{-- Delivery Timeline (ALL orders) --}}
            <div class="card">
                <div class="card-header">
                    <div class="card-title">📦 Delivery Timeline</div>
                </div>
                <div class="card-body">
                    @php
                        $steps = [
                            [
                                'key' => 'pending',
                                'icon' => '📦',
                                'label' => 'Preparing',
                                'sub' => 'Order confirmed, being packed',
                            ],
                            [
                                'key' => 'delivering',
                                'icon' => '🚚',
                                'label' => 'Out for Delivery',
                                'sub' => 'On the way to customer',
                            ],
                            [
                                'key' => 'delivered',
                                'icon' => '📬',
                                'label' => 'Delivered',
                                'sub' => 'Customer received the order',
                            ],
                        ];
                        $deliveryOrder = ['pending', 'delivering', 'delivered'];
                        $currentDeliveryIdx = array_search($deliveryStatus, $deliveryOrder);
                    @endphp
                    <div class="timeline">
                        @foreach ($steps as $step)
                            @php
                                $stepIdx = array_search($step['key'], $deliveryOrder);
                                $isDone = $currentDeliveryIdx !== false && $stepIdx <= $currentDeliveryIdx;
                                $isCurrent = $stepIdx === $currentDeliveryIdx;
                            @endphp
                            <div class="timeline-step">
                                <div class="timeline-dot {{ $isDone ? ($isCurrent ? 'current' : 'done') : '' }}">
                                    {{ $step['icon'] }}</div>
                                <div class="timeline-label {{ $isDone ? ($isCurrent ? 'current' : 'done') : '' }}">
                                    {{ $step['label'] }}</div>
                                <div class="timeline-sub">{{ $step['sub'] }}</div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        {{-- RIGHT --}}
        <div>
            {{-- Invoice details --}}
            <div class="card">
                <div class="card-header">
                    <div class="card-title">📋 Invoice Details</div>
                </div>
                <div class="card-body">
                    <div class="meta-grid">
                        <div>
                            <div class="meta-label">Invoice #</div>
                            <div class="meta-value mono">{{ $sale->invoice_no }}</div>
                        </div>
                        <div>
                            <div class="meta-label">Customer</div>
                            <div class="meta-value" style="font-size:13px;">{{ $sale->user?->name ?? 'Guest' }}</div>
                        </div>
                        <div>
                            <div class="meta-label">Date</div>
                            <div class="meta-value" style="font-size:13px;">
                                {{ \Carbon\Carbon::parse($sale->created_at)->timezone('Asia/Phnom_Penh')->format('d M Y') }}
                            </div>
                        </div>
                        <div>
                            <div class="meta-label">Time</div>
                            <div class="meta-value" style="font-size:13px;">
                                {{ \Carbon\Carbon::parse($sale->created_at)->timezone('Asia/Phnom_Penh')->format('h:i A') }}
                            </div>
                        </div>
                        <div>
                            <div class="meta-label">Payment Method</div>
                            <div class="meta-value">
                                @if ($sale->payment_method === 'cash')
                                    <span class="payment-badge badge-cash">💵 Cash on Delivery</span>
                                @elseif($sale->payment_method === 'khqr_usd')
                                    <span class="payment-badge badge-khqr-usd">🇺🇸 KHQR USD</span>
                                @elseif($sale->payment_method === 'khqr_khr')
                                    <span class="payment-badge badge-khqr-khr">🇰🇭 KHQR KHR</span>
                                @else
                                    <span class="payment-badge badge-khqr-usd">🏦 KHQR</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div
                        style="background:rgba(255,255,255,0.02);border:1px solid rgba(255,255,255,0.06);border-radius:10px;padding:16px;">
                        <div class="totals-row"><span>Subtotal</span><span
                                class="val">${{ number_format($sale->total_amount, 2) }}</span></div>
                        @if ($sale->payment_method === 'cash')
                            <div class="totals-row"><span>Cash Given</span><span
                                    class="val">${{ number_format($sale->paid_amount, 2) }}</span></div>
                            <div class="totals-row change"><span>Change</span><span
                                    class="val">${{ number_format($sale->change_amount, 2) }}</span></div>
                        @endif
                        <div class="totals-row grand"><span>Total</span><span
                                class="val">${{ number_format($sale->total_amount, 2) }}</span></div>
                    </div>

                    @if (in_array($sale->payment_method, ['khqr', 'khqr_usd', 'khqr_khr']) && $sale->bakong_hash)
                        <div class="khqr-box">
                            <div class="khqr-box-title">📱 KHQR Transaction</div>
                            <div class="khqr-row"><span>Network</span><span class="v">NBC Bakong</span></div>
                            <div class="khqr-row"><span>Currency</span><span
                                    class="v">{{ $sale->payment_method === 'khqr_khr' ? '🇰🇭 KHR' : '🇺🇸 USD' }}</span>
                            </div>
                            <div class="khqr-row"><span>Hash</span><span class="v"
                                    title="{{ $sale->bakong_hash }}">{{ substr($sale->bakong_hash, 0, 20) }}…</span></div>
                        </div>
                    @endif
                </div>
            </div>

            {{-- 🚚 Update Delivery Status (ALL orders not yet delivered) --}}
            @if ($deliveryStatus !== 'delivered' && $sale->status !== 'cancelled')
                <div class="update-card delivery">
                    <div class="update-header">
                        <div class="update-title delivery">🚚 Update Delivery Status</div>
                    </div>
                    <div class="update-body">
                        <form action="{{ route('admin.sales.updateDelivery', $sale) }}" method="POST">
                            @csrf @method('PATCH')
                            <label class="form-label">Delivery Status</label>
                            <select name="delivery_status" class="form-select">
                                <option value="pending" {{ $deliveryStatus === 'pending' ? 'selected' : '' }}>📦
                                    Preparing</option>
                                <option value="delivering" {{ $deliveryStatus === 'delivering' ? 'selected' : '' }}>🚚 Out
                                    for Delivery</option>
                                <option value="delivered">📬 Delivered to Customer</option>
                            </select>
                            <button type="submit" class="btn btn-blue btn-full">Update Delivery</button>
                        </form>
                    </div>
                </div>
            @endif

            {{-- 💳 Update Payment Status (cash only, not yet paid/cancelled) --}}
            @if ($sale->payment_method === 'cash' && !in_array($sale->status, ['paid', 'cancelled']))
                <div class="update-card payment">
                    <div class="update-header">
                        <div class="update-title payment">💳 Update Payment Status</div>
                    </div>
                    <div class="update-body">
                        <form action="{{ route('admin.sales.updateStatus', $sale) }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf @method('PATCH')
                            <label class="form-label">Payment Status</label>
                            <select name="status" class="form-select" id="statusSelect"
                                onchange="togglePaymentFields()">
                                <option value="pending" {{ $sale->status === 'pending' ? 'selected' : '' }}>🟡 Pending
                                </option>
                                <option value="paid">✅ Paid — Payment Received</option>
                                <option value="cancelled">❌ Cancelled</option>
                            </select>
                            <div id="paymentFields">
                                <label class="form-label">Payment Reference <span
                                        style="color:#4B5563;font-weight:400;text-transform:none;">(optional)</span></label>
                                <input type="text" name="payment_reference" class="form-input"
                                    value="{{ $sale->payment_reference }}"
                                    placeholder="ABA TXN #123456, Cash collected..." />
                                <label class="form-label">Payment Photo <span
                                        style="color:#4B5563;font-weight:400;text-transform:none;">(optional)</span></label>
                                <input type="file" name="payment_proof" class="form-file" accept="image/*"
                                    onchange="previewPhoto(this)" />
                                <div class="form-preview" id="formPreview"><img id="previewImg" src="" /></div>
                            </div>
                            <button type="submit" class="btn btn-amber btn-full" style="margin-top:4px;">Update
                                Payment</button>
                        </form>
                    </div>
                </div>
            @endif

            {{-- Actions --}}
            <div class="card">
                <div class="card-header">
                    <div class="card-title">⚡ Actions</div>
                </div>
                <div class="card-body">
                    <a href="{{ route('admin.pos.receipt', $sale) }}" target="_blank" class="btn btn-green btn-full">🖨️
                        Print Receipt</a>
                    <a href="{{ route('admin.sales.index') }}" class="btn btn-primary btn-full">← Back to List</a>
                    @if (auth()->user()->canDo('sales.delete'))
                        <form method="POST" action="{{ route('admin.sales.destroy', $sale) }}"
                            onsubmit="return confirm('Permanently delete this sale?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-full">🗑️ Delete Sale</button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @if ($sale->payment_proof)
        <div class="lightbox" id="lightbox" onclick="this.classList.remove('open')">
            <img src="{{ asset('storage/' . $sale->payment_proof) }}" />
        </div>
    @endif

@endsection

@push('scripts')
    <script>
        function togglePaymentFields() {
            const status = document.getElementById('statusSelect')?.value;
            const fields = document.getElementById('paymentFields');
            if (fields) fields.style.display = status === 'paid' ? 'block' : 'none';
        }

        function previewPhoto(input) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = e => {
                    document.getElementById('previewImg').src = e.target.result;
                    document.getElementById('formPreview').style.display = 'block';
                };
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
@endpush
