<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt — {{ $sale->invoice_no }}</title>
    <link
        href="https://fonts.googleapis.com/css2?family=Noto+Sans+Khmer:wght@400;600;700&family=IBM+Plex+Mono:wght@400;600&family=Outfit:wght@400;500;600;700&display=swap"
        rel="stylesheet">
    <style>
        :root {
            --red: #CC0001;
            --blue: #003087;
            --gold: #F4A900;
            --dark: #1A1A2E;
            --gray: #6B7280;
            --light: #F8F7F4;
            --border: #E5E0D8;
            --white: #FFFFFF;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Outfit', sans-serif;
            background: var(--light);
            min-height: 100vh;
            display: flex;
            align-items: flex-start;
            justify-content: center;
            padding: 24px 16px;
        }

        .receipt {
            background: var(--white);
            width: 100%;
            max-width: 420px;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 4px 24px rgba(0, 0, 0, 0.10);
            position: relative;
        }

        /* Header */
        .receipt-header {
            background: var(--blue);
            padding: 28px 24px 20px;
            position: relative;
            overflow: hidden;
        }

        .receipt-header::before {
            content: '';
            position: absolute;
            top: -30px;
            right: -30px;
            width: 120px;
            height: 120px;
            background: rgba(244, 169, 0, 0.15);
            border-radius: 50%;
        }

        .receipt-header::after {
            content: '';
            position: absolute;
            bottom: -20px;
            left: 60px;
            width: 80px;
            height: 80px;
            background: rgba(204, 0, 1, 0.12);
            border-radius: 50%;
        }

        .store-name {
            font-size: 24px;
            font-weight: 700;
            color: var(--white);
            letter-spacing: 0.5px;
        }

        .store-tagline {
            font-size: 12px;
            color: rgba(255, 255, 255, 0.65);
            margin-top: 2px;
            font-family: 'Noto Sans Khmer', sans-serif;
        }

        .flag-stripe {
            display: flex;
            height: 5px;
            margin-top: 16px;
            border-radius: 2px;
            overflow: hidden;
            gap: 2px;
        }

        .flag-stripe span:nth-child(1),
        .flag-stripe span:nth-child(3) {
            background: var(--red);
            flex: 1;
        }

        .flag-stripe span:nth-child(2) {
            background: var(--blue);
            flex: 2;
        }

        /* Invoice meta */
        .invoice-meta {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            padding: 18px 24px 14px;
            border-bottom: 1px dashed var(--border);
        }

        .invoice-label {
            font-size: 11px;
            color: var(--gray);
            text-transform: uppercase;
            letter-spacing: 0.8px;
            margin-bottom: 3px;
        }

        .invoice-no {
            font-family: 'IBM Plex Mono', monospace;
            font-size: 14px;
            font-weight: 600;
            color: var(--dark);
        }

        .invoice-date {
            font-size: 12px;
            color: var(--gray);
            text-align: right;
        }

        .invoice-date strong {
            display: block;
            font-size: 13px;
            color: var(--dark);
            font-weight: 600;
        }

        /* Payment badge */
        .payment-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 5px 12px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 600;
        }

        .payment-badge.cash {
            background: #F0FDF4;
            color: #16A34A;
            border: 1px solid #BBF7D0;
        }

        .payment-badge.khqr {
            background: #EFF6FF;
            color: var(--blue);
            border: 1px solid #BFDBFE;
        }

        .payment-badge .dot {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: currentColor;
        }

        /* Items */
        .items-section {
            padding: 0 24px;
        }

        .items-header {
            display: grid;
            grid-template-columns: 1fr auto auto;
            gap: 8px;
            padding: 10px 0 6px;
            border-bottom: 1px solid var(--border);
        }

        .items-header span {
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            color: var(--gray);
            font-weight: 600;
        }

        .items-header span:nth-child(2),
        .items-header span:nth-child(3) {
            text-align: right;
        }

        .item-row {
            display: grid;
            grid-template-columns: 1fr auto auto;
            gap: 8px;
            padding: 10px 0;
            border-bottom: 1px dashed var(--border);
            align-items: start;
        }

        .item-name {
            font-size: 13px;
            font-weight: 500;
            color: var(--dark);
            line-height: 1.4;
        }

        .item-qty {
            font-size: 12px;
            color: var(--gray);
            text-align: right;
            white-space: nowrap;
            font-family: 'IBM Plex Mono', monospace;
        }

        .item-total {
            font-size: 13px;
            font-weight: 600;
            color: var(--dark);
            text-align: right;
            font-family: 'IBM Plex Mono', monospace;
            white-space: nowrap;
        }

        /* Totals */
        .totals-section {
            padding: 14px 24px;
            background: var(--light);
            margin: 12px 0 0;
        }

        .total-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 5px 0;
        }

        .total-row span:first-child {
            font-size: 13px;
            color: var(--gray);
        }

        .total-row span:last-child {
            font-size: 13px;
            color: var(--dark);
            font-family: 'IBM Plex Mono', monospace;
            font-weight: 500;
        }

        .total-row.grand {
            border-top: 2px solid var(--border);
            margin-top: 8px;
            padding-top: 12px;
        }

        .total-row.grand span:first-child {
            font-size: 15px;
            font-weight: 700;
            color: var(--dark);
        }

        .total-row.grand span:last-child {
            font-size: 18px;
            font-weight: 700;
            color: var(--blue);
        }

        .total-row.change span:last-child {
            color: #16A34A;
            font-weight: 600;
        }

        /* KHQR txn */
        .txn-section {
            margin: 0 24px 14px;
            padding: 12px 14px;
            background: #F0F4FF;
            border-radius: 10px;
            border: 1px solid #DBEAFE;
        }

        .txn-title {
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            color: var(--blue);
            font-weight: 700;
            margin-bottom: 8px;
        }

        .txn-row {
            display: flex;
            justify-content: space-between;
            padding: 3px 0;
        }

        .txn-row span:first-child {
            font-size: 11px;
            color: var(--gray);
        }

        .txn-row span:last-child {
            font-size: 11px;
            font-weight: 600;
            color: var(--dark);
            font-family: 'IBM Plex Mono', monospace;
            word-break: break-all;
            text-align: right;
            max-width: 60%;
        }

        /* Footer */
        .receipt-footer {
            padding: 20px 24px 24px;
            text-align: center;
            border-top: 1px dashed var(--border);
        }

        .thank-you {
            font-size: 16px;
            font-weight: 700;
            color: var(--dark);
            margin-bottom: 2px;
        }

        .thank-you-kh {
            font-family: 'Noto Sans Khmer', sans-serif;
            font-size: 13px;
            color: var(--gray);
            margin-bottom: 12px;
        }

        .khqr-logo {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 11px;
            color: var(--gray);
        }

        .khqr-badge {
            background: var(--blue);
            color: white;
            font-size: 9px;
            font-weight: 700;
            padding: 2px 6px;
            border-radius: 4px;
            letter-spacing: 0.5px;
        }

        /* Tear edge */
        .tear-edge {
            display: flex;
            justify-content: space-between;
            padding: 0 10px;
            margin-top: -1px;
        }

        .tear-edge span {
            width: 18px;
            height: 18px;
            background: var(--light);
            border-radius: 50%;
            display: block;
            border: 1px solid var(--border);
        }

        /* Print */
        @media print {
            body {
                background: white;
                padding: 0;
            }

            .receipt {
                box-shadow: none;
                border-radius: 0;
                max-width: 80mm;
            }

            .no-print {
                display: none !important;
            }
        }

        /* Action buttons */
        .actions {
            display: flex;
            gap: 10px;
            padding: 0 24px 24px;
        }

        .btn {
            flex: 1;
            padding: 10px;
            border-radius: 10px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            border: none;
            font-family: 'Outfit', sans-serif;
            transition: opacity 0.2s;
        }

        .btn:hover {
            opacity: 0.85;
        }

        .btn-print {
            background: var(--blue);
            color: white;
        }

        .btn-close {
            background: var(--light);
            color: var(--dark);
            border: 1px solid var(--border);
        }
    </style>
</head>

<body>

    @php
        $method = strtolower($sale->payment_method);
        $isKhqr = str_contains($method, 'khqr');
        $paymentLabel = match (true) {
            $method === 'cash' => '💵 Cash',
            $method === 'khqr_usd' => '🇺🇸 KHQR — USD',
            $method === 'khqr_khr' => '🇰🇭 KHQR — KHR',
            str_contains($method, 'khqr') => '✅ KHQR Bakong',
            default => strtoupper($method),
        };
    @endphp

    <div class="receipt">

        {{-- HEADER --}}
        <div class="receipt-header">
            <div class="store-name">🏪 Mini Mart</div>
            <div class="store-tagline">ហាងលក់គ្រឿងទំនិញ • Phnom Penh, Cambodia</div>
            <div class="flag-stripe">
                <span></span><span></span><span></span>
            </div>
        </div>

        {{-- INVOICE META --}}
        <div class="invoice-meta">
            <div>
                <div class="invoice-label">Invoice No.</div>
                <div class="invoice-no">{{ $sale->invoice_no }}</div>
            </div>
            <div class="invoice-date">
                <strong>{{ $sale->created_at->setTimezone('Asia/Phnom_Penh')->format('d M Y') }}</strong>
                {{ $sale->created_at->setTimezone('Asia/Phnom_Penh')->format('h:i A') }}
            </div>
        </div>

        {{-- PAYMENT BADGE --}}
        <div style="padding: 0 24px 4px;">
            <span class="payment-badge {{ $isKhqr ? 'khqr' : 'cash' }}">
                <span class="dot"></span>
                {{ $paymentLabel }}
            </span>
        </div>

        {{-- ITEMS --}}
        <div class="items-section">
            <div class="items-header">
                <span>Item</span>
                <span>Qty × Price</span>
                <span>Amount</span>
            </div>
            @foreach ($sale->items as $item)
                <div class="item-row">
                    <div class="item-name">{{ $item->product->name }}</div>
                    <div class="item-qty">{{ $item->quantity }} × ${{ number_format($item->price, 2) }}</div>
                    <div class="item-total">${{ number_format($item->total, 2) }}</div>
                </div>
            @endforeach
        </div>

        {{-- TOTALS --}}
        <div class="totals-section">
            <div class="total-row grand">
                <span>Total</span>
                <span>${{ number_format($sale->total_amount, 2) }}</span>
            </div>
            <div class="total-row" style="padding-top:8px;">
                <span>Paid</span>
                <span>${{ number_format($sale->paid_amount, 2) }}</span>
            </div>
            @if ($sale->change_amount > 0)
                <div class="total-row change">
                    <span>Change</span>
                    <span>${{ number_format($sale->change_amount, 2) }}</span>
                </div>
            @endif
        </div>

        {{-- KHQR TRANSACTION DETAILS --}}
        @if ($isKhqr && $sale->bakong_hash)
            <div style="height:12px;"></div>
            <div class="txn-section">
                <div class="txn-title">🔐 Transaction Reference</div>
                <div class="txn-row">
                    <span>Bakong Hash</span>
                    <span>{{ substr($sale->bakong_hash, 0, 16) }}...</span>
                </div>
                <div class="txn-row">
                    <span>Network</span>
                    <span>NBC Bakong</span>
                </div>
                <div class="txn-row">
                    <span>Currency</span>
                    <span>{{ strtoupper(str_replace('khqr_', '', $method)) }}</span>
                </div>
            </div>
        @endif

        {{-- TEAR EDGE --}}
        <div class="tear-edge" style="margin-top:16px;">
            <span></span><span></span><span></span><span></span>
            <span></span><span></span><span></span><span></span>
            <span></span><span></span><span></span><span></span>
        </div>

        {{-- FOOTER --}}
        <div class="receipt-footer">
            <div class="thank-you">Thank you for shopping!</div>
            <div class="thank-you-kh">អរគុណសម្រាប់ការទិញទំនិញ!</div>
            <div class="khqr-logo">
                <span>Powered by</span>
                <span class="khqr-badge">KHQR</span>
                <span>National Bank of Cambodia</span>
            </div>
        </div>

        {{-- ACTION BUTTONS (screen only) --}}
        <div class="actions no-print">
            <button class="btn btn-print" onclick="window.print()">🖨️ Print Receipt</button>
            <button class="btn btn-close" onclick="window.location.href='{{ route('admin.pos.index') }}'">← New
                Sale</button>
        </div>

    </div>

    <script>
        window.onload = function() {
            if (window.opener) {
                window.print();
                setTimeout(function() {
                    window.close();
                }, 1500);
            }
        };
    </script>

</body>

</html>
