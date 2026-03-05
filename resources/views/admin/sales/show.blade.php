@extends('layouts.app')

@section('title', 'Sale — ' . $sale->invoice_number)

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
    .back-link:hover { color: #E8E4DC; }

    .detail-grid {
        display: grid;
        grid-template-columns: 1fr 340px;
        gap: 20px;
        align-items: start;
    }

    .card {
        background: rgba(255,255,255,0.03);
        border: 1px solid rgba(255,255,255,0.07);
        border-radius: 16px;
        overflow: hidden;
    }
    .card-header {
        padding: 18px 24px;
        border-bottom: 1px solid rgba(255,255,255,0.06);
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
    .card-body { padding: 24px; }

    /* Invoice meta */
    .meta-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 16px;
        margin-bottom: 24px;
    }
    .meta-item {}
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
    .meta-value.large {
        font-family: 'IBM Plex Mono', monospace;
        font-size: 22px;
        font-weight: 700;
        color: #fff;
    }

    /* Payment badge */
    .payment-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 4px 12px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 700;
    }
    .badge-cash { background: rgba(22,163,74,0.15); border: 1px solid rgba(22,163,74,0.3); color: #86EFAC; }
    .badge-khqr-usd { background: rgba(0,48,135,0.2); border: 1px solid rgba(0,48,135,0.4); color: #93C5FD; }
    .badge-khqr-khr { background: rgba(204,0,1,0.15); border: 1px solid rgba(204,0,1,0.3); color: #FCA5A5; }

    /* Items table */
    table { width: 100%; border-collapse: collapse; }
    thead th {
        padding: 10px 16px;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.7px;
        color: #6B7280;
        text-align: left;
        border-bottom: 1px solid rgba(255,255,255,0.05);
        background: rgba(255,255,255,0.02);
    }
    tbody tr { border-bottom: 1px solid rgba(255,255,255,0.04); }
    tbody tr:last-child { border-bottom: none; }
    tbody td {
        padding: 12px 16px;
        font-size: 13.5px;
        color: #E8E4DC;
        vertical-align: middle;
    }
    tfoot td {
        padding: 10px 16px;
        font-size: 13px;
        border-top: 1px solid rgba(255,255,255,0.07);
    }

    .product-name { font-weight: 500; color: #fff; }
    .product-cat  { font-size: 11px; color: #6B7280; margin-top: 2px; }
    .qty  { font-family: 'IBM Plex Mono', monospace; font-size: 13px; color: #9CA3AF; }
    .price { font-family: 'IBM Plex Mono', monospace; font-size: 13px; color: #9CA3AF; }
    .subtotal { font-family: 'IBM Plex Mono', monospace; font-size: 14px; font-weight: 600; color: #fff; }

    /* Totals */
    .totals-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 8px 0;
        font-size: 13px;
        color: #9CA3AF;
        border-bottom: 1px solid rgba(255,255,255,0.04);
    }
    .totals-row:last-child { border-bottom: none; }
    .totals-row.grand {
        font-size: 16px;
        font-weight: 700;
        color: #fff;
        padding-top: 14px;
    }
    .totals-row .val { font-family: 'IBM Plex Mono', monospace; font-size: 14px; font-weight: 600; }
    .totals-row.grand .val { font-size: 18px; color: #fff; }
    .totals-row.change .val { color: #86EFAC; }

    /* KHQR info box */
    .khqr-box {
        background: rgba(0,48,135,0.1);
        border: 1px solid rgba(0,48,135,0.25);
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
        display: flex;
        align-items: center;
        gap: 6px;
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

    /* Action buttons */
    .action-row {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
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
        box-shadow: 0 2px 10px rgba(0,48,135,0.3);
    }
    .btn-primary:hover { transform: translateY(-1px); }
    .btn-green {
        background: rgba(22,163,74,0.2);
        border: 1px solid rgba(22,163,74,0.35);
        color: #86EFAC;
    }
    .btn-green:hover { background: rgba(22,163,74,0.3); }
    .btn-danger {
        background: rgba(204,0,1,0.12);
        border: 1px solid rgba(204,0,1,0.25);
        color: #FCA5A5;
    }
    .btn-danger:hover { background: rgba(204,0,1,0.25); }

    @media (max-width: 900px) {
        .detail-grid { grid-template-columns: 1fr; }
        .meta-grid { grid-template-columns: 1fr; }
    }
</style>
@endpush

@section('content')

<a href="{{ route('admin.sales.index') }}" class="back-link">← Back to Sales History</a>

<div class="detail-grid">

    {{-- Left: Items --}}
    <div>
        <div class="card">
            <div class="card-header">
                <div class="card-title">🧾 Items Purchased</div>
                <span style="font-size:12px;color:#6B7280;">{{ $sale->items->count() }} item{{ $sale->items->count() != 1 ? 's' : '' }}</span>
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
                    @foreach($sale->items as $item)
                    <tr>
                        <td>
                            <div class="product-name">{{ $item->product->name ?? 'Deleted Product' }}</div>
                            @if($item->product?->category)
                            <div class="product-cat">{{ $item->product->category->name }}</div>
                            @endif
                        </td>
                        <td><span class="qty">x {{ $item->quantity }}</span></td>
                        <td><span class="price">${{ number_format($item->price, 2) }}</span></td>
                        <td><span class="subtotal">${{ number_format($item->price * $item->quantity, 2) }}</span></td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="3" style="text-align:right;color:#6B7280;font-size:12px;font-weight:600;letter-spacing:0.5px;text-transform:uppercase;">Grand Total</td>
                        <td style="font-family:'IBM Plex Mono',monospace;font-size:16px;font-weight:700;color:#fff;">${{ number_format($sale->total_amount, 2) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    {{-- Right: Summary --}}
    <div style="display:flex;flex-direction:column;gap:16px;">

        {{-- Invoice Info --}}
        <div class="card">
            <div class="card-header">
                <div class="card-title">📋 Invoice Details</div>
            </div>
            <div class="card-body">
                <div class="meta-grid">
                    <div class="meta-item">
                        <div class="meta-label">Invoice #</div>
                        <div class="meta-value mono">{{ $sale->invoice_number }}</div>
                    </div>
                    <div class="meta-item">
                        <div class="meta-label">Date</div>
                        <div class="meta-value">{{ \Carbon\Carbon::parse($sale->created_at)->timezone('Asia/Phnom_Penh')->format('d M Y') }}</div>
                    </div>
                    <div class="meta-item">
                        <div class="meta-label">Time</div>
                        <div class="meta-value">{{ \Carbon\Carbon::parse($sale->created_at)->timezone('Asia/Phnom_Penh')->format('h:i:s A') }}</div>
                    </div>
                    <div class="meta-item">
                        <div class="meta-label">Payment</div>
                        <div class="meta-value">
                            @if($sale->payment_method === 'cash')
                                <span class="payment-badge badge-cash">💵 Cash</span>
                            @elseif($sale->payment_method === 'khqr_usd')
                                <span class="payment-badge badge-khqr-usd">🇺🇸 KHQR USD</span>
                            @elseif($sale->payment_method === 'khqr_khr')
                                <span class="payment-badge badge-khqr-khr">🇰🇭 KHQR KHR</span>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Totals --}}
                <div style="background:rgba(255,255,255,0.02);border:1px solid rgba(255,255,255,0.06);border-radius:10px;padding:16px;">
                    <div class="totals-row">
                        <span>Subtotal</span>
                        <span class="val">${{ number_format($sale->total_amount, 2) }}</span>
                    </div>
                    @if($sale->payment_method === 'cash')
                    <div class="totals-row">
                        <span>Cash Given</span>
                        <span class="val">${{ number_format($sale->cash_given ?? $sale->total_amount, 2) }}</span>
                    </div>
                    <div class="totals-row change">
                        <span>Change</span>
                        <span class="val">${{ number_format($sale->change_amount ?? 0, 2) }}</span>
                    </div>
                    @endif
                    <div class="totals-row grand">
                        <span>Total Paid</span>
                        <span class="val">${{ number_format($sale->total_amount, 2) }}</span>
                    </div>
                </div>

                {{-- KHQR block --}}
                @if(in_array($sale->payment_method, ['khqr_usd', 'khqr_khr']) && $sale->bakong_hash)
                <div class="khqr-box">
                    <div class="khqr-box-title">📱 KHQR Transaction</div>
                    <div class="khqr-row">
                        <span>Network</span>
                        <span class="v">NBC Bakong</span>
                    </div>
                    <div class="khqr-row">
                        <span>Currency</span>
                        <span class="v">{{ $sale->payment_method === 'khqr_usd' ? '🇺🇸 USD' : '🇰🇭 KHR' }}</span>
                    </div>
                    <div class="khqr-row">
                        <span>Hash</span>
                        <span class="v" title="{{ $sale->bakong_hash }}">{{ substr($sale->bakong_hash, 0, 20) }}…</span>
                    </div>
                </div>
                @endif
            </div>
        </div>

        {{-- Actions --}}
        <div class="card">
            <div class="card-header">
                <div class="card-title">⚡ Actions</div>
            </div>
            <div class="card-body">
                <div class="action-row">
                    <a href="{{ route('admin.pos.receipt', $sale) }}" target="_blank" class="btn btn-green" style="flex:1;justify-content:center;">
                        🖨️ Print Receipt
                    </a>
                </div>
                <div class="action-row" style="margin-top:10px;">
                    <a href="{{ route('admin.sales.index') }}" class="btn btn-primary" style="flex:1;justify-content:center;">
                        ← Back to List
                    </a>
                    <form method="POST" action="{{ route('admin.sales.destroy', $sale) }}"
                          onsubmit="return confirm('Permanently delete this sale?')" style="flex:1;">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-danger" style="width:100%;justify-content:center;">
                            🗑 Delete Sale
                        </button>
                    </form>
                </div>
            </div>
        </div>

    </div>
</div>

@endsection