@extends('layouts.app')
@section('title', 'POS Terminal')

@push('styles')
    <style>
        .content-area {
            padding: 0 !important;
            overflow: hidden !important;
        }

        /* ── Design Tokens — Light default, dark: overrides ─── */
        :root {
            --bg: #F0F2F8;
            --panel: #FFFFFF;
            --surface: #F7F8FC;
            --surface-2: #EEF0F7;
            --glass: rgba(0, 0, 0, 0.03);
            --glass-2: rgba(0, 0, 0, 0.05);
            --border: rgba(0, 0, 0, 0.07);
            --border-2: rgba(0, 0, 0, 0.11);
            --blue: #3B82F6;
            --blue-dim: #1D4ED8;
            --green: #22C55E;
            --red: #EF4444;
            --amber: #F59E0B;
            --text: #1A1A2E;
            --text-2: #4B5563;
            --text-3: #9CA3AF;
            --radius: 14px;
        }

        html.dark {
            --bg: #07080F;
            --panel: #0D0E1A;
            --surface: #12131F;
            --surface-2: #181929;
            --glass: rgba(255, 255, 255, 0.03);
            --glass-2: rgba(255, 255, 255, 0.055);
            --border: rgba(255, 255, 255, 0.06);
            --border-2: rgba(255, 255, 255, 0.11);
            --text: #EEEAE2;
            --text-2: #8B909E;
            --text-3: #555968;
        }

        /* ── Scrollbars ─────────────────────────────────────── */
        #product-grid::-webkit-scrollbar,
        #cart-items::-webkit-scrollbar {
            width: 3px;
        }

        #product-grid::-webkit-scrollbar-thumb,
        #cart-items::-webkit-scrollbar-thumb {
            background: rgba(0, 0, 0, 0.1);
            border-radius: 3px;
        }

        html.dark #product-grid::-webkit-scrollbar-thumb,
        html.dark #cart-items::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.07);
        }

        ::-webkit-scrollbar-track {
            background: transparent;
        }

        /* ── Keyframes ──────────────────────────────────────── */
        @keyframes fadeSlideUp {
            from {
                opacity: 0;
                transform: translateY(12px) scale(0.98);
            }

            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        @keyframes itemIn {
            from {
                opacity: 0;
                transform: translateX(14px);
            }

            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        @keyframes pulse {

            0%,
            100% {
                opacity: 1;
                transform: scale(1);
            }

            50% {
                opacity: .3;
                transform: scale(.65);
            }
        }

        @keyframes float {

            0%,
            100% {
                transform: translateY(0px);
            }

            50% {
                transform: translateY(-4px);
            }
        }

        .modal-anim {
            animation: fadeSlideUp .28s cubic-bezier(.34, 1.3, .64, 1);
        }

        .cart-item {
            animation: itemIn .22s cubic-bezier(.34, 1.3, .64, 1);
        }

        .pulse-dot {
            animation: pulse 1.4s ease infinite;
        }

        .countdown-bar {
            transition: width 1s linear, background-color .8s;
        }

        /* ── Product Grid ────────────────────────────────────── */
        #product-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(158px, 1fr));
            gap: 10px;
            overflow-y: auto;
            flex: 1;
            align-content: start;
        }

        @media (max-width:1024px) {
            #product-grid:not(.list-view) {
                grid-template-columns: repeat(auto-fill, minmax(135px, 1fr));
            }
        }

        /* ── Product Card ────────────────────────────────────── */
        .product-card {
            background: var(--panel);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            overflow: hidden;
            cursor: pointer;
            display: flex;
            flex-direction: column;
            position: relative;
            transition: border-color .2s, box-shadow .2s, transform .2s cubic-bezier(.34, 1.4, .64, 1);
            box-shadow: 0 1px 4px rgba(0, 0, 0, 0.06);
        }

        .product-card:hover {
            border-color: rgba(59, 130, 246, .45);
            transform: translateY(-3px);
            box-shadow: 0 10px 32px rgba(0, 0, 0, .12), 0 0 0 1px rgba(59, 130, 246, .18);
        }

        html.dark .product-card {
            background: var(--surface);
            box-shadow: none;
        }

        html.dark .product-card:hover {
            box-shadow: 0 12px 36px rgba(0, 0, 0, .5), 0 0 0 1px rgba(59, 130, 246, .15);
        }

        .product-card:active {
            transform: translateY(-1px);
        }

        .product-card::before {
            content: '';
            position: absolute;
            inset: 0;
            border-radius: var(--radius);
            background: linear-gradient(135deg, rgba(59, 130, 246, .06) 0%, transparent 55%);
            opacity: 0;
            transition: opacity .2s;
            pointer-events: none;
            z-index: 1;
        }

        .product-card:hover::before {
            opacity: 1;
        }

        .product-img-wrap {
            width: 100%;
            aspect-ratio: 1;
            overflow: hidden;
            background: var(--glass-2);
            position: relative;
            flex-shrink: 0;
        }

        .product-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform .35s cubic-bezier(.34, 1.2, .64, 1);
        }

        .product-card:hover .product-img {
            transform: scale(1.08);
        }

        /* Product name clamp */
        .product-name {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        /* ── List View ───────────────────────────────────────── */
        #product-grid.list-view {
            grid-template-columns: 1fr;
            gap: 5px;
        }

        #product-grid.list-view .product-card {
            flex-direction: row;
            align-items: stretch;
            height: 60px;
            border-radius: 10px;
        }

        #product-grid.list-view .product-img-wrap {
            width: 60px;
            height: 60px;
            aspect-ratio: unset;
            flex-shrink: 0;
            border-radius: 0;
        }

        #product-grid.list-view .product-card:hover .product-img {
            transform: scale(1.12);
        }

        #product-grid.list-view .product-info {
            flex-direction: row;
            align-items: center;
            padding: 0 12px;
            gap: 0;
            flex: 1;
            min-width: 0;
        }

        #product-grid.list-view .product-name {
            flex: 1;
            -webkit-line-clamp: 1;
            font-size: 13px;
            min-width: 0;
        }

        #product-grid.list-view .product-price {
            font-size: 13px;
            min-width: 58px;
            text-align: right;
            padding: 0 10px;
        }

        #product-grid.list-view .product-stock {
            font-size: 10.5px;
            min-width: 62px;
            text-align: right;
            padding-right: 10px;
            white-space: nowrap;
        }

        #product-grid.list-view .btn-add {
            margin: 0;
            border-radius: 0 10px 10px 0;
            padding: 0 14px;
            height: 100%;
            width: auto;
            align-self: stretch;
            display: flex;
            align-items: center;
            justify-content: center;
            white-space: nowrap;
            font-size: 11px;
        }

        /* ── KHQR backdrop open ──────────────────────────────── */
        .qr-backdrop.open {
            display: flex;
        }

        /* ── KHQR header shimmer ─────────────────────────────── */
        .khqr-header::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 8%;
            right: 8%;
            height: 1px;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, .25), transparent);
        }

        /* ── Cart items separator ───────────────────────────── */
        .cart-item-sep {
            border-bottom: 1px solid var(--border);
        }

        /* ── Mobile ──────────────────────────────────────────── */
        @media (max-width:700px) {
            .pos-shell {
                flex-direction: column;
            }

            .pos-right {
                width: 100%;
                height: 370px;
                border-left: none;
                border-top: 1px solid var(--border);
            }

            .pos-left {
                flex: none;
                height: calc(100% - 370px);
            }

            .qr-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
@endpush

@section('content')
    <div class="pos-shell flex h-full overflow-hidden" style="background:var(--bg);">

        {{-- ══════ LEFT PANEL: CATALOGUE ══════════════════════ --}}
        <div class="pos-left flex-1 flex flex-col min-w-0 overflow-hidden" style="border-right:1px solid var(--border);">

            {{-- Top bar ──────────────────────────────────────── --}}
            <div class="flex items-center gap-3 px-5 py-3.5 flex-shrink-0"
                style="background:var(--panel);border-bottom:1px solid var(--border);">

                {{-- Search ──────────────────────────────────── --}}
                <div class="relative flex-1">
                    <div class="absolute left-3.5 top-1/2 -translate-y-1/2 pointer-events-none transition-colors duration-200"
                        style="color:var(--text-3);" id="searchIconWrap">
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.2"
                            viewBox="0 0 24 24">
                            <circle cx="11" cy="11" r="8" />
                            <path d="m21 21-4.35-4.35" />
                        </svg>
                    </div>
                    <input type="text" id="search" autofocus placeholder="Search product or scan barcode…"
                        style="width:100%;background:var(--glass);border:1px solid var(--border);
                           color:var(--text);border-radius:10px;padding:9px 14px 9px 36px;
                           font-size:13.5px;font-family:'DM Sans',sans-serif;
                           outline:none;transition:all .22s;"
                        onfocus="this.style.borderColor='rgba(59,130,246,.5)';this.style.background='rgba(59,130,246,.06)';this.style.boxShadow='0 0 0 3px rgba(59,130,246,.1)';document.getElementById('searchIconWrap').style.color='var(--blue)'"
                        onblur="this.style.borderColor='';this.style.background='var(--glass)';this.style.boxShadow='';document.getElementById('searchIconWrap').style.color='var(--text-3)'">
                </div>

                {{-- View Toggle ─────────────────────────────── --}}
                <div class="flex gap-0.5 p-0.5 rounded-[9px] flex-shrink-0"
                    style="background:var(--glass);border:1px solid var(--border);">
                    <button id="btn-grid" onclick="setView('grid')" title="Grid"
                        class="w-[30px] h-[30px] rounded-lg border-0 flex items-center justify-center cursor-pointer transition-all duration-150">
                        <svg width="13" height="13" fill="currentColor" viewBox="0 0 16 16">
                            <rect x="1" y="1" width="6" height="6" rx="1.5" />
                            <rect x="9" y="1" width="6" height="6" rx="1.5" />
                            <rect x="1" y="9" width="6" height="6" rx="1.5" />
                            <rect x="9" y="9" width="6" height="6" rx="1.5" />
                        </svg>
                    </button>
                    <button id="btn-list" onclick="setView('list')" title="List"
                        class="w-[30px] h-[30px] rounded-lg border-0 flex items-center justify-center cursor-pointer transition-all duration-150">
                        <svg width="13" height="13" fill="currentColor" viewBox="0 0 16 16">
                            <rect x="1" y="2" width="14" height="2.5" rx="1.2" />
                            <rect x="1" y="6.75" width="14" height="2.5" rx="1.2" />
                            <rect x="1" y="11.5" width="14" height="2.5" rx="1.2" />
                        </svg>
                    </button>
                </div>
            </div>

            {{-- Product Grid ─────────────────────────────────── --}}
            <div id="product-grid" class="p-4">
                @foreach ($products as $product)
                    @php
                        $oos = $product->stock <= 0;
                        $low = !$oos && $product->stock <= 5;
                    @endphp
                    <div class="product-card">
                        <div class="product-img-wrap">
                            <img class="product-img"
                                src="{{ $product->image ? asset('storage/' . $product->image) : asset('images/no-image.png') }}"
                                alt="{{ $product->name }}" loading="lazy">
                            @if ($oos)
                                <div class="absolute inset-0 flex items-center justify-center"
                                    style="background:rgba(0,0,0,.65);backdrop-filter:blur(3px);z-index:2;">
                                    <span
                                        style="font-size:9.5px;font-weight:800;color:#FCA5A5;letter-spacing:1.2px;text-transform:uppercase;">Out
                                        of Stock</span>
                                </div>
                            @endif
                            @if ($low)
                                <div class="absolute top-2 right-2 rounded-md px-1.5 py-0.5"
                                    style="background:rgba(245,158,11,.18);border:1px solid rgba(245,158,11,.4);font-size:8.5px;font-weight:800;color:#D97706;letter-spacing:.5px;z-index:2;">
                                    LOW</div>
                            @endif
                        </div>
                        <div class="product-info flex-1 flex flex-col" style="padding:9px 10px 0;">
                            <div class="product-name"
                                style="font-size:12px;font-weight:600;color:var(--text);line-height:1.35;margin-bottom:3px;">
                                {{ $product->name }}</div>
                            <div class="product-price font-mono"
                                style="font-size:13px;font-weight:700;color:#16A34A;margin-bottom:2px;">
                                ${{ number_format($product->sell_price, 2) }}</div>
                            <div class="product-stock" style="font-size:10px;color:var(--text-3);margin-bottom:9px;">
                                {{ $product->stock }} in stock</div>
                        </div>
                        <button class="btn-add" onclick="addToCart({{ $product->id }})" {{ $oos ? 'disabled' : '' }}
                            style="margin:0 9px 9px;padding:7px;border:0;border-radius:9px;font-size:11.5px;font-weight:700;
                           font-family:'DM Sans',sans-serif;cursor:{{ $oos ? 'not-allowed' : 'pointer' }};transition:all .18s;
                           {{ $oos
                               ? 'background:rgba(0,0,0,.05);color:var(--text-3);'
                               : 'background:linear-gradient(135deg,#1D4ED8,#3B82F6);color:#fff;box-shadow:0 2px 12px rgba(59,130,246,.25);' }}">
                            {{ $oos ? 'Unavailable' : '+ Add to Cart' }}
                        </button>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- ══════ RIGHT PANEL: CART ═══════════════════════════ --}}
        <div class="pos-right xl:w-[500px] flex flex-col overflow-hidden flex-shrink-0"
            style="background:var(--panel);border-left:1px solid var(--border);">

            {{-- Cart Header ──────────────────────────────────── --}}
            <div class="flex items-center justify-between px-5 py-4 flex-shrink-0"
                style="border-bottom:1px solid var(--border);">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl flex items-center justify-center text-lg flex-shrink-0"
                        style="background:linear-gradient(135deg,#1D4ED8,#3B82F6);box-shadow:0 4px 14px rgba(59,130,246,.3);">
                        🛒
                    </div>
                    <div>
                        <div
                            style="font-family:'Playfair Display',serif;font-size:14px;font-weight:700;color:var(--text);line-height:1.1;">
                            Order</div>
                        <div id="cartSubtitle" style="font-size:10px;color:var(--text-3);margin-top:1px;">0 items</div>
                    </div>
                </div>
                <button onclick="clearCart()"
                    style="background:none;border:1px solid rgba(239,68,68,.2);color:rgba(239,68,68,.5);
                       font-size:11px;padding:4px 10px;border-radius:6px;cursor:pointer;
                       font-family:'DM Sans',sans-serif;transition:all .15s;"
                    onmouseover="this.style.borderColor='rgba(239,68,68,.5)';this.style.color='#EF4444';this.style.background='rgba(239,68,68,.06)'"
                    onmouseout="this.style.borderColor='rgba(239,68,68,.2)';this.style.color='rgba(239,68,68,.5)';this.style.background='none'">
                    Clear
                </button>
            </div>

            {{-- Cart Items ───────────────────────────────────── --}}
            <div id="cart-items" class="flex-1 overflow-y-auto" style="padding:10px 14px;">
                <div class="flex flex-col items-center justify-center h-full gap-3">
                    <div style="font-size:36px;opacity:.15;animation:float 3s ease infinite;">🛒</div>
                    <div style="font-size:12.5px;color:var(--text-3);">Cart is empty</div>
                </div>
            </div>

            {{-- Cart Footer ──────────────────────────────────── --}}
            <div class="flex-shrink-0"
                style="border-top:1px solid var(--border);padding:14px 14px 16px;background:var(--surface);">

                {{-- Order total --}}
                <div class="flex justify-between items-end mb-4">
                    <span
                        style="font-size:11px;color:var(--text-3);font-weight:600;letter-spacing:.5px;text-transform:uppercase;">Total</span>
                    <div id="totalAmount"
                        style="font-family:'IBM Plex Mono',monospace;font-size:28px;font-weight:700;color:var(--text);line-height:1;">
                        $0.00</div>
                </div>

                {{-- Payment method tabs --}}
                <div class="flex gap-1 mb-3 p-[3px] rounded-[11px]"
                    style="background:var(--glass);border:1px solid var(--border);">
                    <button id="tab-cash" onclick="setPayment('cash')"
                        class="payment-tab flex-1 py-[7px] px-1 rounded-[8px] border-0 text-[11.5px] font-bold cursor-pointer text-center transition-all duration-200"
                        style="font-family:'DM Sans',sans-serif;color:var(--text-3);background:none;">💵 Cash</button>
                    <button id="tab-khqr" onclick="setPayment('khqr')"
                        class="payment-tab flex-1 py-[7px] px-1 rounded-[8px] border-0 text-[11.5px] font-bold cursor-pointer text-center transition-all duration-200"
                        style="font-family:'DM Sans',sans-serif;color:var(--text-3);background:none;">📱 KHQR</button>
                    <button id="tab-aba" onclick="setPayment('aba')"
                        class="payment-tab flex-1 py-[7px] px-1 rounded-[8px] border-0 text-[11.5px] font-bold cursor-pointer text-center transition-all duration-200"
                        style="font-family:'DM Sans',sans-serif;color:var(--text-3);background:none;">🏦 ABA</button>
                </div>

                {{-- Cash section --}}
                <div id="cashSection" class="flex flex-col gap-2 mb-3">
                    <div
                        style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.9px;color:var(--text-3);">
                        Cash Received</div>
                    <input type="number" id="cashInput" placeholder="0.00" step="0.01"
                        style="width:100%;background:var(--panel);border:1px solid var(--border);border-radius:9px;
                           padding:10px 13px;font-size:15px;font-family:'IBM Plex Mono',monospace;
                           color:var(--text);outline:none;transition:all .2s;box-sizing:border-box;"
                        onfocus="this.style.borderColor='rgba(34,197,94,.5)';this.style.background='rgba(34,197,94,.04)';this.style.boxShadow='0 0 0 3px rgba(34,197,94,.08)'"
                        onblur="this.style.borderColor='';this.style.background='var(--panel)';this.style.boxShadow=''">
                    <div class="flex justify-between items-center rounded-[9px] py-2 px-3"
                        style="background:rgba(34,197,94,.08);border:1px solid rgba(34,197,94,.2);">
                        <span style="font-size:11.5px;color:#16A34A;font-weight:700;">Change</span>
                        <span id="changeAmount"
                            style="font-family:'IBM Plex Mono',monospace;font-size:14px;font-weight:700;color:#16A34A;">$0.00</span>
                    </div>
                </div>

                {{-- Checkout button --}}
                <button id="checkoutBtn" onclick="checkout()" disabled
                    style="width:100%;padding:13px;background:linear-gradient(135deg,#15803D,#22C55E);color:#fff;border:0;
                       border-radius:12px;font-size:14px;font-weight:700;font-family:'DM Sans',sans-serif;
                       cursor:pointer;display:flex;align-items:center;justify-content:center;gap:7px;
                       transition:all .22s;box-shadow:0 4px 16px rgba(34,197,94,.2);">
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5"
                        viewBox="0 0 24 24">
                        <polyline points="20 6 9 17 4 12" />
                    </svg>
                    Checkout
                </button>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════
     KHQR MODAL
══════════════════════════════════════════════════════════ --}}
    <div id="qrBackdrop" class="qr-backdrop hidden fixed inset-0 items-center justify-center p-5"
        style="background:rgba(15,20,40,.75);backdrop-filter:blur(14px);z-index:200;">
        <div class="modal-anim w-full overflow-hidden"
            style="max-width:560px;background:var(--panel);border:1px solid var(--border-2);
                border-radius:22px;box-shadow:0 40px 100px rgba(0,0,0,.35);">

            {{-- KHQR Header --}}
            <div class="khqr-header relative text-center"
                style="background:linear-gradient(135deg,#0A1645 0%,#1D4ED8 55%,#3B82F6 100%);padding:22px 28px 18px;">
                <div class="flex h-[3px] w-12 rounded overflow-hidden gap-px mx-auto mb-3">
                    <span class="flex-1" style="background:#CC0001;"></span>
                    <span style="flex:2;background:#5B9BD5;"></span>
                    <span class="flex-1" style="background:#CC0001;"></span>
                </div>
                <div
                    style="font-family:'Playfair Display',serif;font-size:18px;font-weight:900;color:#fff;margin-bottom:3px;letter-spacing:-.2px;">
                    Scan to Pay · KHQR Bakong
                </div>
                <div style="font-size:11.5px;color:rgba(255,255,255,.55);">
                    Any currency · All NBC Bakong-registered banks
                </div>
            </div>

            {{-- KHQR Body --}}
            <div style="padding:18px 20px 20px;">
                <div class="qr-grid grid grid-cols-2 gap-3 mb-4">
                    {{-- USD --}}
                    <div class="rounded-xl p-4 text-center"
                        style="background:rgba(59,130,246,.07);border:1px solid rgba(59,130,246,.2);">
                        <div style="font-size:11.5px;font-weight:700;color:#2563EB;margin-bottom:4px;">🇺🇸 US Dollar</div>
                        <div id="usdAmount"
                            style="font-family:'IBM Plex Mono',monospace;font-size:17px;font-weight:700;color:#1D4ED8;margin-bottom:10px;">
                        </div>
                        <div style="background:#fff;border-radius:9px;padding:9px;border:1px solid rgba(0,0,0,.06);">
                            <img id="qrImageUSD" src="" alt="USD QR"
                                style="width:100%;display:block;border-radius:4px;">
                        </div>
                        <div style="font-size:9px;color:var(--text-3);margin-top:7px;">ABA · Wing · ACLEDA · all banks
                        </div>
                    </div>
                    {{-- KHR --}}
                    <div class="rounded-xl p-4 text-center"
                        style="background:rgba(204,0,1,.06);border:1px solid rgba(204,0,1,.18);">
                        <div style="font-size:11.5px;font-weight:700;color:#B91C1C;margin-bottom:4px;">🇰🇭 Khmer Riel
                        </div>
                        <div id="khrAmount"
                            style="font-family:'IBM Plex Mono',monospace;font-size:17px;font-weight:700;color:#DC2626;margin-bottom:10px;">
                        </div>
                        <div style="background:#fff;border-radius:9px;padding:9px;border:1px solid rgba(0,0,0,.06);">
                            <img id="qrImageKHR" src="" alt="KHR QR"
                                style="width:100%;display:block;border-radius:4px;">
                        </div>
                        <div style="font-size:9px;color:var(--text-3);margin-top:7px;">ABA · Wing · ACLEDA · all banks
                        </div>
                    </div>
                </div>

                {{-- Status rows --}}
                <div id="statusWaiting"
                    style="display:block;text-align:center;padding:11px;background:rgba(59,130,246,.08);border:1px solid rgba(59,130,246,.18);border-radius:10px;font-size:12.5px;color:#2563EB;font-weight:500;margin-bottom:12px;">
                    <span class="pulse-dot inline-block w-[6px] h-[6px] rounded-full mr-2 align-middle"
                        style="background:#3B82F6;"></span>
                    Waiting for payment on either QR…
                </div>
                <div id="statusSuccess"
                    style="display:none;padding:14px;background:rgba(34,197,94,.08);border:1px solid rgba(34,197,94,.22);border-radius:10px;text-align:center;margin-bottom:12px;">
                    <div style="font-size:26px;margin-bottom:4px;">✅</div>
                    <div style="font-size:15px;font-weight:700;color:#15803D;margin-bottom:3px;">Payment Received!</div>
                    <div id="successCurrency" style="font-size:11px;color:var(--text-3);"></div>
                    <div style="font-size:11px;color:var(--text-3);margin-top:2px;">Redirecting to receipt…</div>
                </div>
                <div id="statusExpired"
                    style="display:none;padding:11px;background:rgba(239,68,68,.07);border:1px solid rgba(239,68,68,.2);border-radius:10px;text-align:center;font-size:12.5px;color:#DC2626;margin-bottom:12px;">
                    ⏰ QR Expired — cancel and try again.
                </div>

                {{-- Countdown --}}
                <div id="countdownArea" class="mb-3">
                    <div class="flex justify-between mb-1.5" style="font-size:11px;color:var(--text-3);">
                        <span>Expires in</span>
                        <span id="countdownTimer"
                            style="font-family:'IBM Plex Mono',monospace;font-weight:700;color:var(--amber);">5:00</span>
                    </div>
                    <div style="height:3px;background:var(--glass-2);border-radius:999px;overflow:hidden;">
                        <div id="countdownBar" class="countdown-bar"
                            style="height:100%;border-radius:999px;background:var(--green);width:100%;"></div>
                    </div>
                </div>

                <div id="exchangeNote" style="text-align:center;font-size:10.5px;color:var(--text-3);margin-bottom:12px;">
                </div>

                <button id="cancelQrBtn" onclick="closeQrPopup()"
                    style="width:100%;padding:10px;background:rgba(239,68,68,.07);border:1px solid rgba(239,68,68,.2);
                       border-radius:9px;color:#DC2626;font-size:12.5px;font-weight:600;
                       font-family:'DM Sans',sans-serif;cursor:pointer;transition:background .18s;"
                    onmouseover="this.style.background='rgba(239,68,68,.14)'"
                    onmouseout="this.style.background='rgba(239,68,68,.07)'">
                    Cancel Transaction
                </button>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════
     ABA PAYWAY MODAL
══════════════════════════════════════════════════════════ --}}
    <div id="abaBackdrop"
        style="display:none;position:fixed;inset:0;background:rgba(15,20,40,.75);backdrop-filter:blur(14px);z-index:300;align-items:center;justify-content:center;padding:20px;">
        <div class="modal-anim text-center"
            style="background:var(--panel);border:1px solid var(--border-2);border-radius:22px;
                width:100%;max-width:330px;padding:28px 24px;
                box-shadow:0 40px 100px rgba(0,0,0,.3);">

            <div
                style="width:50px;height:50px;background:linear-gradient(135deg,#E1232E,#3B82F6);border-radius:15px;
                    display:flex;align-items:center;justify-content:center;font-size:23px;
                    margin:0 auto 14px;box-shadow:0 8px 24px rgba(59,130,246,.3);">
                🏦</div>

            <div
                style="font-family:'Playfair Display',serif;font-size:18px;font-weight:900;color:var(--text);margin-bottom:3px;">
                ABA PayWay</div>
            <div style="font-size:12px;color:var(--text-3);margin-bottom:16px;">Scan with ABA Mobile to pay</div>

            <div id="abaAmount"
                style="font-family:'IBM Plex Mono',monospace;font-size:22px;font-weight:700;color:#E1232E;margin-bottom:16px;">
            </div>

            <div
                style="background:#fff;border-radius:14px;padding:11px;margin:0 auto 16px;
                    width:200px;height:200px;display:flex;align-items:center;justify-content:center;
                    border:1px solid rgba(0,0,0,.08);box-shadow:0 4px 16px rgba(0,0,0,.1);">
                <img id="abaQrImage" src="" alt="ABA QR"
                    style="width:100%;height:100%;object-fit:contain;border-radius:4px;">
            </div>

            <div id="abaWaiting"
                style="display:flex;align-items:center;justify-content:center;gap:7px;
                    padding:9px 14px;background:rgba(59,130,246,.08);border:1px solid rgba(59,130,246,.2);
                    border-radius:9px;font-size:12px;color:#2563EB;font-weight:500;margin-bottom:10px;">
                <span class="pulse-dot inline-block rounded-full" style="width:6px;height:6px;background:#3B82F6;"></span>
                Waiting for payment…
            </div>

            <div id="abaTimerWrap"
                style="display:flex;align-items:center;justify-content:center;gap:8px;margin-bottom:14px;">
                <svg width="18" height="18" viewBox="0 0 18 18" style="flex-shrink:0;transform:rotate(-90deg)">
                    <circle cx="9" cy="9" r="7" fill="none" stroke="rgba(59,130,246,.15)"
                        stroke-width="2" />
                    <circle id="abaTimerArc" cx="9" cy="9" r="7" fill="none" stroke="#3B82F6"
                        stroke-width="2" stroke-dasharray="43.98" stroke-dashoffset="0" stroke-linecap="round" />
                </svg>
                <span id="abaTimerText"
                    style="font-family:'IBM Plex Mono',monospace;font-size:12.5px;font-weight:700;color:#2563EB;">3:00</span>
            </div>

            <div id="abaSuccess"
                style="display:none;padding:14px;background:rgba(34,197,94,.08);border:1px solid rgba(34,197,94,.2);border-radius:11px;margin-bottom:14px;">
                <div style="font-size:24px;margin-bottom:5px;">✅</div>
                <div style="font-size:14px;font-weight:700;color:#15803D;">Payment Confirmed!</div>
                <div style="font-size:11px;color:var(--text-3);margin-top:3px;">Redirecting to receipt…</div>
            </div>

            <button onclick="closeAbaModal()"
                style="width:100%;padding:10px;background:rgba(239,68,68,.07);border:1px solid rgba(239,68,68,.2);
                   border-radius:9px;color:#DC2626;font-size:12.5px;font-weight:600;
                   font-family:'DM Sans',sans-serif;cursor:pointer;transition:background .18s;"
                onmouseover="this.style.background='rgba(239,68,68,.14)'"
                onmouseout="this.style.background='rgba(239,68,68,.07)'">
                Cancel Transaction
            </button>
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        /* ─────────────────────────────── STATE ──────────────── */
        const POS = {
            paymentInterval: null,
            countdownInterval: null,
            abaInterval: null,
            abaTimerInterval: null,
            total: 0,
            method: 'cash',
            view: localStorage.getItem('pos_view') || 'grid',
        };

        /* ─────────────────────── DARK MODE AWARE COLORS ─────── */
        function isDark() {
            return document.documentElement.classList.contains('dark');
        }

        function getTokenColor(token) {
            const map = {
                'text-3': isDark() ? '#555968' : '#9CA3AF',
                'blue': '#3B82F6',
                'text': isDark() ? '#EEEAE2' : '#1A1A2E',
            };
            return map[token] || token;
        }

        /* ─────────────────────────── VIEW TOGGLE ────────────── */
        function setView(mode) {
            POS.view = mode;
            localStorage.setItem('pos_view', mode);
            document.getElementById('product-grid').classList.toggle('list-view', mode === 'list');
            ['grid', 'list'].forEach(m => {
                const b = document.getElementById(`btn-${m}`);
                b.style.background = m === mode ? 'rgba(59,130,246,.18)' : 'transparent';
                b.style.color = m === mode ? '#2563EB' : 'var(--text-3)';
            });
        }

        /* ─────────────────────────── PAYMENT TABS ───────────── */
        function setPayment(method) {
            POS.method = method;
            ['cash', 'khqr', 'aba'].forEach(m => {
                const t = document.getElementById(`tab-${m}`);
                if (m === method) {
                    t.style.background = 'linear-gradient(135deg,#1D4ED8,#3B82F6)';
                    t.style.color = '#fff';
                    t.style.boxShadow = '0 2px 10px rgba(59,130,246,.3)';
                } else {
                    t.style.background = 'none';
                    t.style.color = 'var(--text-3)';
                    t.style.boxShadow = '';
                }
            });
            document.getElementById('cashSection').style.display = method === 'cash' ? 'flex' : 'none';
        }

        /* ────────────────────────────── INIT ────────────────── */
        document.addEventListener('DOMContentLoaded', () => {
            setView(POS.view);
            setPayment('cash');
            document.getElementById('cashInput').addEventListener('input', calcChange);
            let st;
            document.getElementById('search').addEventListener('keyup', function() {
                clearTimeout(st);
                st = setTimeout(() => {
                    fetch(
                            `{{ route('admin.pos.search') }}?search=${encodeURIComponent(this.value)}`)
                        .then(r => r.json()).then(renderProducts);
                }, 280);
            });
            loadCart();
        });

        /* ──────────────────────── RENDER PRODUCTS ───────────── */
        function renderProducts(products) {
            const g = document.getElementById('product-grid');
            if (!products.length) {
                g.innerHTML =
                    `<div style="grid-column:1/-1;text-align:center;padding:48px 20px;color:var(--text-3);font-size:13px;">No products found</div>`;
                return;
            }
            g.innerHTML = products.map(p => {
                const img = p.image ? `/storage/${p.image}` : '/images/no-image.png';
                const oos = p.stock <= 0,
                    low = !oos && p.stock <= 5;
                return `<div class="product-card">
                <div class="product-img-wrap">
                    <img class="product-img" src="${img}" alt="${p.name}" loading="lazy">
                    ${oos ? `<div class="absolute inset-0 flex items-center justify-center" style="background:rgba(0,0,0,.65);backdrop-filter:blur(3px);z-index:2;"><span style="font-size:9.5px;font-weight:800;color:#FCA5A5;letter-spacing:1.2px;text-transform:uppercase;">Out of Stock</span></div>` : ''}
                    ${low ? `<div class="absolute top-2 right-2 rounded-md px-1.5 py-0.5" style="background:rgba(245,158,11,.18);border:1px solid rgba(245,158,11,.4);font-size:8.5px;font-weight:800;color:#D97706;z-index:2;">LOW</div>` : ''}
                </div>
                <div class="product-info flex-1 flex flex-col" style="padding:9px 10px 0;">
                    <div class="product-name" style="font-size:12px;font-weight:600;color:var(--text);line-height:1.35;margin-bottom:3px;">${p.name}</div>
                    <div class="product-price font-mono" style="font-size:13px;font-weight:700;color:#16A34A;margin-bottom:2px;">$${parseFloat(p.sell_price).toFixed(2)}</div>
                    <div style="font-size:10px;color:var(--text-3);margin-bottom:9px;">${p.stock} in stock</div>
                </div>
                <button class="btn-add" onclick="addToCart(${p.id})" ${oos?'disabled':''}
                    style="margin:0 9px 9px;padding:7px;border:0;border-radius:9px;font-size:11.5px;font-weight:700;
                           font-family:'DM Sans',sans-serif;transition:all .18s;
                           ${oos ? 'background:rgba(0,0,0,.05);color:var(--text-3);cursor:not-allowed;' : 'background:linear-gradient(135deg,#1D4ED8,#3B82F6);color:#fff;box-shadow:0 2px 12px rgba(59,130,246,.25);cursor:pointer;'}">
                    ${oos ? 'Unavailable' : '+ Add to Cart'}
                </button>
            </div>`;
            }).join('');
        }

        /* ─────────────────────────────── CART ───────────────── */
        function addToCart(id) {
            fetch("{{ route('admin.pos.add') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    product_id: id
                })
            }).then(r => r.json()).then(d => {
                if (d.error) {
                    showToast(d.error, 'error');
                    return;
                }
                loadCart();
            });
        }

        function updateQty(id, qty) {
            if (qty <= 0) return removeItem(id);
            fetch("{{ route('admin.pos.update') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    product_id: id,
                    quantity: qty
                })
            }).then(() => loadCart());
        }

        function removeItem(id) {
            fetch("{{ route('admin.pos.remove') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    product_id: id
                })
            }).then(() => loadCart());
        }

        function clearCart() {
            if (!confirm('Clear all items from cart?')) return;
            fetch("{{ url('/admin/pos-cart-data') }}").then(r => r.json()).then(data => {
                const ids = Object.keys(data);
                if (!ids.length) return;
                const next = i => {
                    if (i >= ids.length) {
                        loadCart();
                        return;
                    }
                    fetch("{{ route('admin.pos.remove') }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            product_id: ids[i]
                        })
                    }).then(() => next(i + 1));
                };
                next(0);
            });
        }

        function loadCart() {
            fetch("{{ url('/admin/pos-cart-data') }}").then(r => r.json()).then(data => {
                const cartDiv = document.getElementById('cart-items');
                const totalEl = document.getElementById('totalAmount');
                const subtitle = document.getElementById('cartSubtitle');
                const btn = document.getElementById('checkoutBtn');
                const keys = Object.keys(data);
                let sub = 0,
                    qty = 0;

                if (!keys.length) {
                    cartDiv.innerHTML = `<div class="flex flex-col items-center justify-center h-full gap-3">
                    <div style="font-size:36px;opacity:.15;animation:float 3s ease infinite;">🛒</div>
                    <div style="font-size:12.5px;color:var(--text-3);">Cart is empty</div></div>`;
                    subtitle.textContent = '0 items';
                    totalEl.textContent = '$0.00';
                    POS.total = 0;
                    btn.disabled = true;
                    btn.style.background = 'rgba(0,0,0,.05)';
                    btn.style.color = 'var(--text-3)';
                    btn.style.boxShadow = 'none';
                    calcChange();
                    return;
                }

                cartDiv.innerHTML = keys.map(id => {
                    const item = data[id];
                    const lt = item.price * item.quantity;
                    sub += lt;
                    qty += item.quantity;
                    return `<div class="cart-item cart-item-sep flex items-center gap-2 py-[9px] last:border-b-0">
                    <div class="flex-1 min-w-0">
                        <div style="font-size:13px;font-weight:600;color:var(--text);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">${item.name}</div>
                        <div style="font-family:'IBM Plex Mono',monospace;font-size:10.5px;color:var(--text-3);margin-top:1px;">$${parseFloat(item.price).toFixed(2)} each</div>
                    </div>
                    <div class="flex items-center overflow-hidden rounded-lg" style="background:var(--glass);border:1px solid var(--border);">
                        <button onclick="updateQty(${id},${item.quantity-1})"
                            style="width:25px;height:25px;background:none;border:0;color:var(--text-3);cursor:pointer;font-size:14px;font-weight:700;display:flex;align-items:center;justify-content:center;transition:all .1s;"
                            onmouseover="this.style.background='rgba(0,0,0,.05)';this.style.color='var(--text)'"
                            onmouseout="this.style.background='none';this.style.color='var(--text-3)'">−</button>
                        <span style="width:26px;text-align:center;font-size:12px;font-weight:700;color:var(--text);font-family:'IBM Plex Mono',monospace;border-left:1px solid var(--border);border-right:1px solid var(--border);line-height:25px;">${item.quantity}</span>
                        <button onclick="updateQty(${id},${item.quantity+1})"
                            style="width:25px;height:25px;background:none;border:0;color:var(--text-3);cursor:pointer;font-size:14px;font-weight:700;display:flex;align-items:center;justify-content:center;transition:all .1s;"
                            onmouseover="this.style.background='rgba(0,0,0,.05)';this.style.color='var(--text)'"
                            onmouseout="this.style.background='none';this.style.color='var(--text-3)'">+</button>
                    </div>
                    <span style="font-family:'IBM Plex Mono',monospace;font-size:12.5px;font-weight:700;color:var(--text);min-width:46px;text-align:right;">$${lt.toFixed(2)}</span>
                    <button onclick="removeItem(${id})"
                        style="background:none;border:0;color:rgba(239,68,68,.35);cursor:pointer;font-size:15px;padding:2px 3px;transition:color .1s;line-height:1;"
                        onmouseover="this.style.color='#EF4444'"
                        onmouseout="this.style.color='rgba(239,68,68,.35)'">×</button>
                </div>`;
                }).join('');

                POS.total = sub;
                totalEl.textContent = '$' + sub.toFixed(2);
                subtitle.textContent = `${qty} item${qty!==1?'s':''}`;
                btn.disabled = false;
                btn.style.background = 'linear-gradient(135deg,#15803D,#22C55E)';
                btn.style.color = '#fff';
                btn.style.boxShadow = '0 4px 16px rgba(34,197,94,.2)';
                calcChange();
            });
        }

        function calcChange() {
            const c = parseFloat(document.getElementById('cashInput').value) || 0;
            document.getElementById('changeAmount').textContent = '$' + Math.max(0, c - POS.total).toFixed(2);
        }

        /* ───────────────────────────── CHECKOUT ─────────────── */
        function checkout() {
            const m = POS.method;
            if (m === 'cash') {
                const cash = parseFloat(document.getElementById('cashInput').value) || 0;
                if (cash < POS.total) {
                    showToast('Insufficient cash amount!', 'error');
                    return;
                }
                fetch("{{ route('admin.pos.checkout') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        paid_amount: cash
                    })
                }).then(r => r.json()).then(d => {
                    if (d.error) {
                        showToast(d.error, 'error');
                        return;
                    }
                    window.open(`/admin/pos/receipt/${d.sale_id}`, '_blank');
                    location.reload();
                });
                return;
            }
            if (POS.total <= 0) {
                showToast('Cart is empty!', 'error');
                return;
            }
            const btn = document.getElementById('checkoutBtn');
            const resetBtn = () => {
                btn.disabled = false;
                btn.innerHTML =
                    '<svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg> Checkout';
            };

            if (m === 'khqr') {
                btn.disabled = true;
                btn.innerHTML = '⏳ Generating…';
                fetch("{{ route('admin.pos.generateKhqr') }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            amount: POS.total
                        })
                    })
                    .then(async r => {
                        const d = await r.json();
                        if (!r.ok || d.error) throw new Error(d.error || 'Failed');
                        return d;
                    })
                    .then(d => {
                        showQrModal(d);
                        startCountdown(d.expires_at);
                        pollBothPayments(d.usd.md5, d.khr.md5, d.expires_at);
                    })
                    .catch(e => showToast('QR error: ' + e.message, 'error'))
                    .finally(resetBtn);
                return;
            }
            if (m === 'aba') {
                btn.disabled = true;
                btn.innerHTML = '⏳ Generating…';
                fetch("{{ route('admin.pos.payway.generate') }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            amount: POS.total
                        })
                    })
                    .then(r => r.json())
                    .then(d => {
                        if (d.error) {
                            showToast(d.error, 'error');
                            return;
                        }
                        showAbaModal(d);
                        pollAbaPayment(d.tran_id);
                    })
                    .catch(e => showToast('ABA error: ' + e.message, 'error'))
                    .finally(resetBtn);
            }
        }

        /* ──────────────────────── KHQR POLLING ──────────────── */
        function pollBothPayments(usd, khr, exp) {
            if (POS.paymentInterval) clearInterval(POS.paymentInterval);
            let paid = false;
            POS.paymentInterval = setInterval(() => {
                if (paid) return;
                if (Math.floor(Date.now() / 1000) >= exp) {
                    clearInterval(POS.paymentInterval);
                    clearInterval(POS.countdownInterval);
                    showExpiredState();
                    return;
                }
                pollSingle(usd, 'usd', () => {
                    paid = true;
                });
                setTimeout(() => {
                    if (!paid) pollSingle(khr, 'khr', () => {
                        paid = true;
                    });
                }, 500);
            }, 3000);
        }

        function pollSingle(md5, currency, onSuccess) {
            fetch("{{ route('admin.pos.verifyKhqr') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    md5,
                    currency
                })
            }).then(r => r.json()).then(d => {
                if (d.success) {
                    onSuccess();
                    clearInterval(POS.paymentInterval);
                    clearInterval(POS.countdownInterval);
                    showSuccessState(d.currency);
                    setTimeout(() => {
                        window.location.href = d.receipt_url || `/admin/pos/receipt/${d.sale_id}`;
                    }, 1500);
                }
            }).catch(() => {});
        }

        function startCountdown(exp) {
            if (POS.countdownInterval) clearInterval(POS.countdownInterval);
            const total = exp - Math.floor(Date.now() / 1000);
            const bar = document.getElementById('countdownBar');
            const timer = document.getElementById('countdownTimer');
            POS.countdownInterval = setInterval(() => {
                const rem = exp - Math.floor(Date.now() / 1000);
                if (rem <= 0) {
                    clearInterval(POS.countdownInterval);
                    timer.textContent = '0:00';
                    bar.style.width = '0%';
                    bar.style.background = 'var(--red)';
                    return;
                }
                timer.textContent = `${Math.floor(rem/60)}:${(rem%60).toString().padStart(2,'0')}`;
                const pct = (rem / total) * 100;
                bar.style.width = pct + '%';
                bar.style.background = pct > 50 ? 'var(--green)' : pct > 25 ? 'var(--amber)' : 'var(--red)';
                if (rem <= 30) timer.style.color = 'var(--red)';
            }, 1000);
        }

        /* ──────────────────────── KHQR MODAL ────────────────── */
        function showQrModal(data) {
            document.getElementById('qrImageUSD').src = data.usd.qr_image;
            document.getElementById('qrImageKHR').src = data.khr.qr_image;
            document.getElementById('usdAmount').textContent = data.usd.label;
            document.getElementById('khrAmount').textContent = data.khr.label;
            document.getElementById('exchangeNote').textContent =
                `Exchange rate: $1 = ${Math.round(data.khr.amount/data.usd.amount).toLocaleString()} ៛`;
            document.getElementById('statusWaiting').style.display = 'block';
            document.getElementById('statusSuccess').style.display = 'none';
            document.getElementById('statusExpired').style.display = 'none';
            document.getElementById('countdownArea').style.display = 'block';
            document.getElementById('cancelQrBtn').style.display = 'block';
            document.getElementById('countdownTimer').style.color = 'var(--amber)';
            document.getElementById('countdownBar').style.width = '100%';
            document.getElementById('countdownBar').style.background = 'var(--green)';
            const bd = document.getElementById('qrBackdrop');
            bd.classList.remove('hidden');
            bd.classList.add('flex');
        }

        function showSuccessState(currency) {
            ['statusWaiting', 'statusExpired', 'countdownArea', 'cancelQrBtn'].forEach(id => document.getElementById(id)
                .style.display = 'none');
            document.getElementById('statusSuccess').style.display = 'block';
            document.getElementById('successCurrency').textContent =
                `Paid with ${currency==='KHR'?'🇰🇭 Khmer Riel':'🇺🇸 US Dollar'}`;
        }

        function showExpiredState() {
            ['statusWaiting', 'statusSuccess', 'countdownArea'].forEach(id => document.getElementById(id).style.display =
                'none');
            document.getElementById('statusExpired').style.display = 'block';
            document.getElementById('qrImageUSD').src = document.getElementById('qrImageKHR').src = '';
        }

        function closeQrPopup() {
            if (POS.paymentInterval) clearInterval(POS.paymentInterval);
            if (POS.countdownInterval) clearInterval(POS.countdownInterval);
            const bd = document.getElementById('qrBackdrop');
            bd.classList.add('hidden');
            bd.classList.remove('flex');
            document.getElementById('qrImageUSD').src = document.getElementById('qrImageKHR').src = '';
        }

        /* ───────────────────────── ABA MODAL ────────────────── */
        function showAbaModal(data) {
            document.getElementById('abaQrImage').src = data.qr_image ||
                `https://api.qrserver.com/v1/create-qr-code/?size=280x280&data=${encodeURIComponent(data.qr_string)}`;
            document.getElementById('abaAmount').textContent = '$' + parseFloat(POS.total).toFixed(2);
            document.getElementById('abaWaiting').style.display = 'flex';
            document.getElementById('abaSuccess').style.display = 'none';
            document.getElementById('abaTimerWrap').style.display = 'flex';
            document.getElementById('abaBackdrop').style.display = 'flex';
            startAbaTimer(180);
        }

        function startAbaTimer(totalSecs) {
            if (POS.abaTimerInterval) clearInterval(POS.abaTimerInterval);
            let rem = totalSecs;
            const arc = document.getElementById('abaTimerArc');
            arc.style.transition = 'none';
            arc.style.strokeDashoffset = '0';
            POS.abaTimerInterval = setInterval(() => {
                rem--;
                const pct = rem / totalSecs;
                const mins = Math.floor(rem / 60);
                const secs = (rem % 60).toString().padStart(2, '0');
                document.getElementById('abaTimerText').textContent = `${mins}:${secs}`;
                arc.style.transition = 'stroke-dashoffset 1s linear, stroke .5s';
                arc.style.strokeDashoffset = String(43.98 * (1 - pct));
                arc.style.stroke = pct > .5 ? '#3B82F6' : pct > .25 ? 'var(--amber)' : 'var(--red)';
                document.getElementById('abaTimerText').style.color = pct > .25 ? '#2563EB' : 'var(--red)';
                if (rem <= 0) {
                    clearInterval(POS.abaTimerInterval);
                    clearInterval(POS.abaInterval);
                    showToast('ABA QR expired — please try again', 'error');
                    closeAbaModal();
                }
            }, 1000);
        }

        function closeAbaModal() {
            if (POS.abaInterval) clearInterval(POS.abaInterval);
            if (POS.abaTimerInterval) clearInterval(POS.abaTimerInterval);
            document.getElementById('abaBackdrop').style.display = 'none';
            document.getElementById('abaQrImage').src = '';
        }

        /* ───────────────────────── ABA POLLING ──────────────── */
        function pollAbaPayment(tranId) {
            if (POS.abaInterval) clearInterval(POS.abaInterval);
            POS.abaInterval = setInterval(async () => {
                try {
                    const r = await fetch("{{ route('admin.pos.payway.verify') }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    });
                    const d = await r.json();
                    if (d.success) {
                        clearInterval(POS.abaInterval);
                        clearInterval(POS.abaTimerInterval);
                        document.getElementById('abaTimerWrap').style.display = 'none';
                        document.getElementById('abaWaiting').style.display = 'none';
                        document.getElementById('abaSuccess').style.display = 'block';
                        setTimeout(() => {
                            window.location.href = d.receipt_url;
                        }, 1500);
                    }
                } catch (e) {}
            }, 5000);
        }

        /* ─────────────────────────── TOAST ──────────────────── */
        function showToast(msg, type = 'info') {
            const t = document.createElement('div');
            t.style.cssText = `position:fixed;bottom:24px;right:24px;z-index:9999;padding:11px 18px;
            border-radius:10px;font-size:13px;font-weight:600;font-family:'DM Sans',sans-serif;
            background:${type==='error'?'rgba(220,38,38,.95)':'rgba(34,197,94,.95)'};
            color:#fff;box-shadow:0 4px 24px rgba(0,0,0,.25);backdrop-filter:blur(10px);
            animation:itemIn .2s ease;`;
            t.textContent = msg;
            document.body.appendChild(t);
            setTimeout(() => {
                t.style.opacity = '0';
                t.style.transform = 'translateY(4px)';
                t.style.transition = 'all .2s';
                setTimeout(() => t.remove(), 200);
            }, 2800);
        }
    </script>
@endpush
