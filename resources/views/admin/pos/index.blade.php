@extends('layouts.app')
@section('title', 'POS Terminal')

@push('styles')
    <style>
        /* ── Only what Tailwind cannot do ───────────────── */

        :root {
            --bg: #F0F2F8;
            --panel: #FFFFFF;
            --surface: #F7F8FC;
            --glass: rgba(0, 0, 0, .03);
            --glass-2: rgba(0, 0, 0, .05);
            --border: rgba(0, 0, 0, .07);
            --border-2: rgba(0, 0, 0, .11);
            --blue: #3B82F6;
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
            --glass: rgba(255, 255, 255, .03);
            --glass-2: rgba(255, 255, 255, .055);
            --border: rgba(255, 255, 255, .06);
            --border-2: rgba(255, 255, 255, .11);
            --text: #EEEAE2;
            --text-2: #8B909E;
            --text-3: #555968;
        }

        @keyframes fadeSlideUp {
            from {
                opacity: 0;
                transform: translateY(12px) scale(.98)
            }

            to {
                opacity: 1;
                transform: translateY(0) scale(1)
            }
        }

        @keyframes itemIn {
            from {
                opacity: 0;
                transform: translateX(14px)
            }

            to {
                opacity: 1;
                transform: translateX(0)
            }
        }

        @keyframes pulse {

            0%,
            100% {
                opacity: 1;
                transform: scale(1)
            }

            50% {
                opacity: .3;
                transform: scale(.65)
            }
        }

        @keyframes float {

            0%,
            100% {
                transform: translateY(0)
            }

            50% {
                transform: translateY(-4px)
            }
        }

        @keyframes scanLine {
            0% {
                top: 20%
            }

            50% {
                top: 80%
            }

            100% {
                top: 20%
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

        .float-anim {
            animation: float 3s ease infinite;
        }

        .scan-line {
            animation: scanLine 2s ease-in-out infinite;
        }

        .countdown-bar {
            transition: width 1s linear, background-color .8s;
        }

        /* ── Product Card ─────────────────────────────────── */
        .product-card {
            background: var(--panel);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            cursor: pointer;
            display: flex;
            flex-direction: column;
            position: relative;
            transition: border-color .22s, box-shadow .22s, transform .22s cubic-bezier(.34, 1.4, .64, 1);
            box-shadow: 0 2px 8px rgba(0, 0, 0, .06);
        }

        .product-card:hover {
            border-color: rgba(59, 130, 246, .5);
            transform: translateY(-4px);
            box-shadow: 0 16px 40px rgba(0, 0, 0, .13), 0 0 0 1px rgba(59, 130, 246, .2);
        }

        html.dark .product-card {
            background: var(--surface);
            box-shadow: none;
        }

        html.dark .product-card:hover {
            box-shadow: 0 16px 40px rgba(0, 0, 0, .5), 0 0 0 1px rgba(59, 130, 246, .18);
        }

        .product-card:active {
            transform: translateY(-1px) scale(.99);
        }

        .product-card::after {
            content: '';
            position: absolute;
            inset: 0;
            border-radius: var(--radius);
            background: linear-gradient(145deg, rgba(59, 130, 246, .07) 0%, transparent 60%);
            opacity: 0;
            transition: opacity .22s;
            pointer-events: none;
            z-index: 1;
        }

        .product-card:hover::after {
            opacity: 1;
        }

        /* Image */
        .product-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform .38s cubic-bezier(.34, 1.2, .64, 1);
        }

        .product-card:hover .product-img {
            transform: scale(1.07);
        }

        #product-grid.list-view .product-card:hover .product-img {
            transform: scale(1.12);
        }

        #product-grid:not(.list-view) .product-img-wrap {
            height: 150px;
            overflow: hidden;
            border-radius: var(--radius) var(--radius) 0 0;
            flex-shrink: 0;
        }

        /* Info */
        .product-info {
            padding: 11px 12px 0;
            display: flex;
            flex-direction: column;
            flex: 1;
        }

        .product-name {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            font-size: 12.5px;
            font-weight: 600;
            line-height: 1.4;
            color: var(--text);
            margin-bottom: 6px;
        }

        .product-price {
            font-family: 'IBM Plex Mono', monospace;
            font-size: 14px;
            font-weight: 700;
            color: #16A34A;
            margin-bottom: 4px;
        }

        .product-stock {
            font-size: 10px;
            color: var(--text-3);
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .stock-dot {
            width: 5px;
            height: 5px;
            border-radius: 50%;
            flex-shrink: 0;
            background: #22C55E;
        }

        .stock-dot.low {
            background: #F59E0B;
        }

        .stock-dot.zero {
            background: #EF4444;
        }

        /* Add to cart button */
        .btn-add {
            margin: 0 10px 10px;
            padding: 9px 10px;
            border: 0;
            border-radius: 10px;
            font-size: 12px;
            font-weight: 700;
            font-family: 'DM Sans', sans-serif;
            letter-spacing: .2px;
            cursor: pointer;
            transition: all .2s cubic-bezier(.34, 1.4, .64, 1);
            background: linear-gradient(135deg, #1D4ED8, #3B82F6);
            color: #fff;
            box-shadow: 0 3px 12px rgba(59, 130, 246, .3);
        }

        .btn-add:not(:disabled):hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(59, 130, 246, .45);
            filter: brightness(1.08);
        }

        .btn-add:not(:disabled):active {
            transform: scale(.97);
            filter: brightness(.97);
        }

        .btn-add:disabled {
            background: rgba(0, 0, 0, .06);
            color: var(--text-3);
            cursor: not-allowed;
            box-shadow: none;
        }

        html.dark .btn-add:disabled {
            background: rgba(255, 255, 255, .06);
        }

        /* Grid */
        #product-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
            gap: 14px;
            overflow-y: auto;
            flex: 1;
            align-content: start;
            padding: 16px;
        }

        @media (max-width:1200px) {
            #product-grid:not(.list-view) {
                grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
                gap: 12px;
            }
        }

        @media (max-width:1024px) {
            #product-grid:not(.list-view) {
                grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
                gap: 10px;
            }
        }

        /* List view */
        #product-grid.list-view {
            grid-template-columns: 1fr;
            gap: 5px;
        }

        #product-grid.list-view .product-card {
            flex-direction: row;
            align-items: stretch;
            height: 62px;
            border-radius: 10px;
        }

        #product-grid.list-view .product-img-wrap {
            width: 62px;
            height: 62px;
            overflow: hidden;
            flex-shrink: 0;
            border-radius: 10px 0 0 10px;
        }

        #product-grid.list-view .product-info {
            flex-direction: row;
            align-items: center;
            padding: 0 12px;
            flex: 1;
            min-width: 0;
        }

        #product-grid.list-view .product-name {
            -webkit-line-clamp: 1;
            font-size: 13px;
            flex: 1;
            min-width: 0;
            margin-bottom: 0;
        }

        #product-grid.list-view .product-price {
            font-size: 13px;
            min-width: 58px;
            text-align: right;
            padding: 0 10px;
            margin-bottom: 0;
        }

        #product-grid.list-view .product-stock {
            min-width: 62px;
            text-align: right;
            padding-right: 10px;
            white-space: nowrap;
            margin-bottom: 0;
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


        /* KHQR header shimmer */
        .khqr-header::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 8%;
            right: 8%;
            height: 1px;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, .25), transparent);
        }

        /* html5-qrcode cleanup */
        #qr-reader {
            border: none !important;
        }

        #qr-reader video {
            border-radius: 10px !important;
        }

        #qr-reader__scan_region {
            border: none !important;
        }

        #qr-reader__dashboard {
            display: none !important;
        }

        /* Scrollbars */
        #product-grid::-webkit-scrollbar,
        #cart-items::-webkit-scrollbar {
            width: 3px;
        }

        #product-grid::-webkit-scrollbar-thumb,
        #cart-items::-webkit-scrollbar-thumb {
            background: rgba(0, 0, 0, .1);
            border-radius: 3px;
        }

        html.dark #product-grid::-webkit-scrollbar-thumb,
        html.dark #cart-items::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, .07);
        }

        ::-webkit-scrollbar-track {
            background: transparent;
        }

        /* Mobile */
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

        .payment-tab {
            font-family: 'DM Sans', sans-serif;
            transition: all .2s;
        }
    </style>
@endpush

@section('content')
    <div class="pos-shell flex h-full overflow-hidden" style="background:var(--bg);">

        {{-- LEFT PANEL --}}
        <div class="pos-left flex-1 flex flex-col min-w-0 overflow-hidden" style="border-right:1px solid var(--border);">

            {{-- Top bar --}}
            <div class="flex items-center gap-3 px-5 py-3.5 flex-shrink-0"
                style="background:var(--panel);border-bottom:1px solid var(--border);">

                {{-- Search --}}
                <div class="relative flex-1">
                    <div id="searchIconWrap"
                        class="absolute left-3.5 top-1/2 -translate-y-1/2 pointer-events-none transition-colors duration-200"
                        style="color:var(--text-3);">
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.2"
                            viewBox="0 0 24 24">
                            <circle cx="11" cy="11" r="8" />
                            <path d="m21 21-4.35-4.35" />
                        </svg>
                    </div>
                    <input type="text" id="search" autofocus placeholder="Search product or scan barcode…"
                        class="w-full rounded-[10px] py-[9px] pr-[14px] pl-9 text-[13.5px] outline-none transition-all duration-[220ms]"
                        style="background:var(--glass);border:1px solid var(--border);color:var(--text);font-family:'DM Sans',sans-serif;"
                        onfocus="this.style.borderColor='rgba(59,130,246,.5)';this.style.background='rgba(59,130,246,.06)';this.style.boxShadow='0 0 0 3px rgba(59,130,246,.1)';document.getElementById('searchIconWrap').style.color='var(--blue)'"
                        onblur="this.style.borderColor='';this.style.background='var(--glass)';this.style.boxShadow='';document.getElementById('searchIconWrap').style.color='var(--text-3)'">
                </div>

                {{-- Camera button --}}
                <button onclick="openCameraScanner()" title="Scan barcode with camera"
                    class="flex-shrink-0 w-9 h-9 rounded-[10px] flex items-center justify-center text-base cursor-pointer transition-all duration-200"
                    style="border:1px solid var(--border);background:var(--glass);color:var(--text-3);"
                    onmouseover="this.style.borderColor='rgba(59,130,246,.5)';this.style.color='#3B82F6';this.style.background='rgba(59,130,246,.08)'"
                    onmouseout="this.style.borderColor='var(--border)';this.style.color='var(--text-3)';this.style.background='var(--glass)'">📷</button>

                {{-- View toggle --}}
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

            {{-- Product Grid --}}
            <div id="product-grid">
                @foreach ($products as $product)
                    @php
                        $oos = $product->stock <= 0;
                        $low = !$oos && $product->stock <= 5;
                    @endphp
                    <div class="product-card" onclick="addToCart({{ $product->id }})" {{ $oos ? 'disabled' : '' }}>
                        {{-- Image --}}
                        <div class="product-img-wrap w-full relative" style="background:var(--glass-2);">
                            <img class="product-img"
                                src="{{ $product->image ? asset('storage/' . $product->image) : asset('images/no-image.png') }}"
                                alt="{{ $product->name }}" loading="lazy">
                            @if ($oos)
                                <div class="absolute inset-0 flex items-center justify-center z-[2]"
                                    style="background:rgba(0,0,0,.7);backdrop-filter:blur(4px);">
                                    <span class="text-[9px] font-extrabold tracking-[1.5px] uppercase text-red-300">Out of
                                        Stock</span>
                                </div>
                            @endif
                            @if ($low)
                                <div class="absolute top-2 right-2 z-[2] rounded-[5px] px-[6px] py-[2px] text-[8px] font-extrabold tracking-[.5px]"
                                    style="background:rgba(245,158,11,.2);border:1px solid rgba(245,158,11,.5);color:#D97706;">
                                    LOW</div>
                            @endif
                        </div>

                        {{-- Info --}}
                        <div class="product-info">
                            <div class="product-name">{{ $product->name }}</div>
                            <div class="dark:text-white text-sm text-red-500">{{ $product->barcode }}</div>
                            <div class="product-price">${{ number_format($product->sell_price, 2) }}</div>
                            <div class="product-stock">
                                <span class="stock-dot {{ $oos ? 'zero' : ($low ? 'low' : '') }}"></span>
                                {{ $product->stock }} in stock
                            </div>
                        </div>

                        {{-- Button --}}
                        {{-- <button class="btn-add" onclick="addToCart({{ $product->id }})" {{ $oos ? 'disabled' : '' }}>
                            {{ $oos ? 'Out of Stock' : '+ Add to Cart' }}
                        </button> --}}
                    </div>
                @endforeach
            </div>
        </div>

        {{-- RIGHT PANEL: CART --}}
        <div class="pos-right xl:w-[500px] flex flex-col overflow-hidden flex-shrink-0"
            style="background:var(--panel);border-left:1px solid var(--border);">

            {{-- Cart Header --}}
            <div class="flex items-center justify-between px-5 py-4 flex-shrink-0"
                style="border-bottom:1px solid var(--border);">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl flex items-center justify-center text-lg flex-shrink-0"
                        style="background:linear-gradient(135deg,#1D4ED8,#3B82F6);box-shadow:0 4px 14px rgba(59,130,246,.3);">
                        🛒</div>
                    <div>
                        <div class="text-[14px] font-bold leading-none"
                            style="font-family:'Playfair Display',serif;color:var(--text);">Order</div>
                        <div id="cartSubtitle" class="text-[10px] mt-[1px]" style="color:var(--text-3);">0 items</div>
                    </div>
                </div>
                <button onclick="clearCart()"
                    class="text-[11px] px-[10px] py-1 rounded-md cursor-pointer transition-all duration-150 bg-transparent"
                    style="border:1px solid rgba(239,68,68,.2);color:rgba(239,68,68,.5);font-family:'DM Sans',sans-serif;"
                    onmouseover="this.style.borderColor='rgba(239,68,68,.5)';this.style.color='#EF4444';this.style.background='rgba(239,68,68,.06)'"
                    onmouseout="this.style.borderColor='rgba(239,68,68,.2)';this.style.color='rgba(239,68,68,.5)';this.style.background='transparent'">Clear</button>
            </div>

            {{-- Cart Items --}}
            <div id="cart-items" class="flex-1 overflow-y-auto px-[14px] py-[10px]">
                <div class="flex flex-col items-center justify-center h-full gap-3">
                    <div class="text-4xl float-anim" style="opacity:.15;">🛒</div>
                    <div class="text-[12.5px]" style="color:var(--text-3);">Cart is empty</div>
                </div>
            </div>

            {{-- Cart Footer --}}
            <div class="flex-shrink-0 px-[14px] pt-[14px] pb-[16px]"
                style="border-top:1px solid var(--border);background:var(--surface);">
                <div class="flex justify-between items-end mb-4">
                    <span class="text-[11px] font-semibold tracking-[.5px] uppercase"
                        style="color:var(--text-3);">Total</span>
                    <div id="totalAmount" class="text-[28px] font-bold leading-none"
                        style="font-family:'IBM Plex Mono',monospace;color:var(--text);">$0.00</div>
                </div>
                <div class="flex gap-1 mb-3 p-[3px] rounded-[11px]"
                    style="background:var(--glass);border:1px solid var(--border);">
                    <button id="tab-cash" onclick="setPayment('cash')"
                        class="payment-tab flex-1 py-[7px] px-1 rounded-[8px] border-0 text-[11.5px] font-bold cursor-pointer text-center"
                        style="color:var(--text-3);background:none;">💵 Cash</button>
                    <button id="tab-khqr" onclick="setPayment('khqr')"
                        class="payment-tab flex-1 py-[7px] px-1 rounded-[8px] border-0 text-[11.5px] font-bold cursor-pointer text-center"
                        style="color:var(--text-3);background:none;">📱 KHQR</button>
                    <button id="tab-aba" onclick="setPayment('aba')"
                        class="payment-tab flex-1 py-[7px] px-1 rounded-[8px] border-0 text-[11.5px] font-bold cursor-pointer text-center"
                        style="color:var(--text-3);background:none;">🏦 ABA</button>
                </div>
                <div id="cashSection" class="flex flex-col gap-2 mb-3">
                    <div class="text-[10px] font-bold uppercase tracking-[.9px]" style="color:var(--text-3);">Cash
                        Received</div>
                    <input type="number" id="cashInput" placeholder="0.00" step="0.01"
                        class="w-full rounded-[9px] px-[13px] py-[10px] text-[15px] outline-none transition-all duration-200 box-border"
                        style="background:var(--panel);border:1px solid var(--border);font-family:'IBM Plex Mono',monospace;color:var(--text);"
                        onfocus="this.style.borderColor='rgba(34,197,94,.5)';this.style.background='rgba(34,197,94,.04)';this.style.boxShadow='0 0 0 3px rgba(34,197,94,.08)'"
                        onblur="this.style.borderColor='';this.style.background='var(--panel)';this.style.boxShadow=''">
                    <div class="flex justify-between items-center rounded-[9px] py-2 px-3"
                        style="background:rgba(34,197,94,.08);border:1px solid rgba(34,197,94,.2);">
                        <span class="text-[11.5px] font-bold text-green-700">Change</span>
                        <span id="changeAmount" class="text-[14px] font-bold text-green-700"
                            style="font-family:'IBM Plex Mono',monospace;">$0.00</span>
                    </div>
                </div>
                <button id="checkoutBtn" onclick="checkout()" disabled
                    class="w-full py-[13px] text-white border-0 rounded-xl text-[14px] font-bold flex items-center justify-center gap-[7px] cursor-pointer transition-all duration-[220ms]"
                    style="background:linear-gradient(135deg,#15803D,#22C55E);box-shadow:0 4px 16px rgba(34,197,94,.2);font-family:'DM Sans',sans-serif;">
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5"
                        viewBox="0 0 24 24">
                        <polyline points="20 6 9 17 4 12" />
                    </svg>
                    Checkout
                </button>
            </div>
        </div>
    </div>

    {{-- KHQR MODAL --}}
    <div id="qrBackdrop" class="qr-backdrop hidden fixed inset-0 items-center justify-center p-5 z-[200]"
        style="background:rgba(15,20,40,.75);backdrop-filter:blur(14px);">
        <div class="modal-anim w-full overflow-hidden rounded-[22px]"
            style="max-width:560px;background:var(--panel);border:1px solid var(--border-2);box-shadow:0 40px 100px rgba(0,0,0,.35);">
            <div class="khqr-header relative text-center px-7 pt-[22px] pb-[18px]"
                style="background:linear-gradient(135deg,#0A1645 0%,#1D4ED8 55%,#3B82F6 100%);">
                <div class="flex h-[3px] w-12 rounded overflow-hidden gap-px mx-auto mb-3">
                    <span class="flex-1" style="background:#CC0001;"></span><span
                        style="flex:2;background:#5B9BD5;"></span><span class="flex-1"
                        style="background:#CC0001;"></span>
                </div>
                <div class="text-[18px] font-black text-white mb-[3px] tracking-[-0.2px]"
                    style="font-family:'Playfair Display',serif;">Scan to Pay · KHQR Bakong</div>
                <div class="text-[11.5px]" style="color:rgba(255,255,255,.55);">Any currency · All NBC Bakong-registered
                    banks</div>
            </div>
            <div class="p-[18px_20px_20px]">
                <div class="qr-grid grid grid-cols-2 gap-3 mb-4">
                    <div class="rounded-xl p-4 text-center"
                        style="background:rgba(59,130,246,.07);border:1px solid rgba(59,130,246,.2);">
                        <div class="text-[11.5px] font-bold text-blue-600 mb-1">🇺🇸 US Dollar</div>
                        <div id="usdAmount" class="text-[17px] font-bold mb-[10px]"
                            style="font-family:'IBM Plex Mono',monospace;color:#1D4ED8;"></div>
                        <div class="bg-white rounded-[9px] p-[9px]" style="border:1px solid rgba(0,0,0,.06);"><img
                                id="qrImageUSD" src="" alt="USD QR" class="w-full block rounded-[4px]"></div>
                        <div class="text-[9px] mt-[7px]" style="color:var(--text-3);">ABA · Wing · ACLEDA · all banks
                        </div>
                    </div>
                    <div class="rounded-xl p-4 text-center"
                        style="background:rgba(204,0,1,.06);border:1px solid rgba(204,0,1,.18);">
                        <div class="text-[11.5px] font-bold text-red-700 mb-1">🇰🇭 Khmer Riel</div>
                        <div id="khrAmount" class="text-[17px] font-bold text-red-600 mb-[10px]"
                            style="font-family:'IBM Plex Mono',monospace;"></div>
                        <div class="bg-white rounded-[9px] p-[9px]" style="border:1px solid rgba(0,0,0,.06);"><img
                                id="qrImageKHR" src="" alt="KHR QR" class="w-full block rounded-[4px]"></div>
                        <div class="text-[9px] mt-[7px]" style="color:var(--text-3);">ABA · Wing · ACLEDA · all banks
                        </div>
                    </div>
                </div>
                <div id="statusWaiting"
                    class="flex items-center justify-center gap-2 text-center px-[11px] py-[11px] rounded-[10px] text-[12.5px] font-medium text-blue-600 mb-3"
                    style="background:rgba(59,130,246,.08);border:1px solid rgba(59,130,246,.18);">
                    <span class="pulse-dot inline-block w-[6px] h-[6px] rounded-full"
                        style="background:#3B82F6;"></span>Waiting for payment on either QR…
                </div>
                <div id="statusSuccess" class="hidden px-[14px] py-[14px] rounded-[10px] text-center mb-3"
                    style="background:rgba(34,197,94,.08);border:1px solid rgba(34,197,94,.22);">
                    <div class="text-[26px] mb-1">✅</div>
                    <div class="text-[15px] font-bold text-green-700 mb-[3px]">Payment Received!</div>
                    <div id="successCurrency" class="text-[11px]" style="color:var(--text-3);"></div>
                    <div class="text-[11px] mt-[2px]" style="color:var(--text-3);">Redirecting to receipt…</div>
                </div>
                <div id="statusExpired"
                    class="hidden px-[11px] py-[11px] rounded-[10px] text-center text-[12.5px] text-red-600 mb-3"
                    style="background:rgba(239,68,68,.07);border:1px solid rgba(239,68,68,.2);">⏰ QR Expired — cancel and
                    try again.</div>
                <div id="countdownArea" class="mb-3">
                    <div class="flex justify-between mb-1.5 text-[11px]" style="color:var(--text-3);">
                        <span>Expires in</span><span id="countdownTimer" class="font-bold"
                            style="font-family:'IBM Plex Mono',monospace;color:var(--amber);">5:00</span>
                    </div>
                    <div class="h-[3px] rounded-full overflow-hidden" style="background:var(--glass-2);">
                        <div id="countdownBar" class="countdown-bar h-full rounded-full w-full"
                            style="background:var(--green);"></div>
                    </div>
                </div>
                <div id="exchangeNote" class="text-center text-[10.5px] mb-3" style="color:var(--text-3);"></div>
                <button id="cancelQrBtn" onclick="closeQrPopup()"
                    class="w-full py-[10px] rounded-[9px] text-[12.5px] font-semibold cursor-pointer transition-all duration-[180ms] text-red-600"
                    style="background:rgba(239,68,68,.07);border:1px solid rgba(239,68,68,.2);font-family:'DM Sans',sans-serif;"
                    onmouseover="this.style.background='rgba(239,68,68,.14)'"
                    onmouseout="this.style.background='rgba(239,68,68,.07)'">Cancel Transaction</button>
            </div>
        </div>
    </div>

    {{-- ABA PAYWAY MODAL --}}
    <div id="abaBackdrop" class="fixed inset-0 z-[300] items-center justify-center p-5"
        style="display:none;background:rgba(15,20,40,.75);backdrop-filter:blur(14px);">
        <div class="modal-anim text-center rounded-[22px] w-full max-w-[330px] px-6 py-7"
            style="background:var(--panel);border:1px solid var(--border-2);box-shadow:0 40px 100px rgba(0,0,0,.3);">
            <div class="w-[50px] h-[50px] rounded-[15px] flex items-center justify-center text-[23px] mx-auto mb-[14px]"
                style="background:linear-gradient(135deg,#E1232E,#3B82F6);box-shadow:0 8px 24px rgba(59,130,246,.3);">🏦
            </div>
            <div class="text-[18px] font-black mb-[3px]" style="font-family:'Playfair Display',serif;color:var(--text);">
                ABA PayWay</div>
            <div class="text-[12px] mb-4" style="color:var(--text-3);">Scan with ABA Mobile to pay</div>
            <div id="abaAmount" class="text-[22px] font-bold mb-4"
                style="font-family:'IBM Plex Mono',monospace;color:#E1232E;"></div>
            <div class="rounded-[14px] p-[11px] mx-auto mb-4 w-[200px] h-[200px] flex items-center justify-center bg-white"
                style="border:1px solid rgba(0,0,0,.08);box-shadow:0 4px 16px rgba(0,0,0,.1);">
                <img id="abaQrImage" src="" alt="ABA QR" class="w-full h-full object-contain rounded-[4px]">
            </div>
            <div id="abaWaiting"
                class="flex items-center justify-center gap-[7px] px-[14px] py-[9px] rounded-[9px] text-[12px] font-medium text-blue-600 mb-[10px]"
                style="background:rgba(59,130,246,.08);border:1px solid rgba(59,130,246,.2);">
                <span class="pulse-dot inline-block rounded-full w-[6px] h-[6px]"
                    style="background:#3B82F6;"></span>Waiting for payment…
            </div>
            <div id="abaTimerWrap" class="flex items-center justify-center gap-2 mb-[14px]">
                <svg width="18" height="18" viewBox="0 0 18 18" class="flex-shrink-0 -rotate-90">
                    <circle cx="9" cy="9" r="7" fill="none" stroke="rgba(59,130,246,.15)"
                        stroke-width="2" />
                    <circle id="abaTimerArc" cx="9" cy="9" r="7" fill="none" stroke="#3B82F6"
                        stroke-width="2" stroke-dasharray="43.98" stroke-dashoffset="0" stroke-linecap="round" />
                </svg>
                <span id="abaTimerText" class="text-[12.5px] font-bold text-blue-600"
                    style="font-family:'IBM Plex Mono',monospace;">3:00</span>
            </div>
            <div id="abaSuccess" class="hidden px-[14px] py-[14px] rounded-[11px] mb-[14px]"
                style="background:rgba(34,197,94,.08);border:1px solid rgba(34,197,94,.2);">
                <div class="text-[24px] mb-[5px]">✅</div>
                <div class="text-[14px] font-bold text-green-700">Payment Confirmed!</div>
                <div class="text-[11px] mt-[3px]" style="color:var(--text-3);">Redirecting to receipt…</div>
            </div>
            <button onclick="closeAbaModal()"
                class="w-full py-[10px] rounded-[9px] text-[12.5px] font-semibold cursor-pointer text-red-600 transition-all duration-[180ms]"
                style="background:rgba(239,68,68,.07);border:1px solid rgba(239,68,68,.2);font-family:'DM Sans',sans-serif;"
                onmouseover="this.style.background='rgba(239,68,68,.14)'"
                onmouseout="this.style.background='rgba(239,68,68,.07)'">Cancel Transaction</button>
        </div>
    </div>

    {{-- CAMERA SCANNER MODAL --}}
    <div id="cameraBackdrop" class="fixed inset-0 z-[400] items-center justify-center p-5"
        style="display:none;background:rgba(10,15,35,.82);backdrop-filter:blur(14px);">
        <div class="w-full max-w-[380px] overflow-hidden rounded-[22px]"
            style="background:var(--panel);border:1px solid var(--border-2);box-shadow:0 40px 100px rgba(0,0,0,.4);">
            <div class="flex items-center justify-between px-5 py-[18px]" style="border-bottom:1px solid var(--border);">
                <div class="flex items-center gap-[10px]">
                    <div class="w-9 h-9 rounded-[10px] flex items-center justify-center text-[18px]"
                        style="background:rgba(59,130,246,.12);border:1px solid rgba(59,130,246,.25);">📷</div>
                    <div>
                        <div class="text-[15px] font-bold"
                            style="font-family:'Playfair Display',serif;color:var(--text);">Camera Scanner</div>
                        <div class="text-[11px] mt-[1px]" style="color:var(--text-3);">Point camera at barcode</div>
                    </div>
                </div>
                <button onclick="closeCameraScanner()"
                    class="w-[30px] h-[30px] rounded-[8px] flex items-center justify-center text-[16px] text-red-500 cursor-pointer"
                    style="background:rgba(239,68,68,.08);border:1px solid rgba(239,68,68,.2);">✕</button>
            </div>
            <div class="p-4 pb-2">
                <div class="relative rounded-[14px] overflow-hidden bg-black aspect-square">
                    <div id="qr-reader" class="w-full"></div>
                    <div class="absolute inset-0 pointer-events-none z-10">
                        <div
                            class="absolute top-4 left-4 w-7 h-7 border-t-[3px] border-l-[3px] border-blue-500 rounded-tl-sm">
                        </div>
                        <div
                            class="absolute top-4 right-4 w-7 h-7 border-t-[3px] border-r-[3px] border-blue-500 rounded-tr-sm">
                        </div>
                        <div
                            class="absolute bottom-4 left-4 w-7 h-7 border-b-[3px] border-l-[3px] border-blue-500 rounded-bl-sm">
                        </div>
                        <div
                            class="absolute bottom-4 right-4 w-7 h-7 border-b-[3px] border-r-[3px] border-blue-500 rounded-br-sm">
                        </div>
                        <div class="scan-line absolute left-4 right-4 h-[2px]"
                            style="background:linear-gradient(90deg,transparent,#3B82F6,transparent);top:50%;"></div>
                    </div>
                </div>
            </div>
            <div id="scanResult"
                class="mx-4 mb-2 px-[14px] py-[10px] rounded-[10px] text-[12px] text-center min-h-[38px] flex items-center justify-center"
                style="background:rgba(59,130,246,.06);border:1px solid rgba(59,130,246,.15);color:var(--text-3);">Waiting
                for barcode…</div>
            <div class="px-4 pb-4">
                <button onclick="closeCameraScanner()"
                    class="w-full py-[10px] rounded-[9px] text-[13px] font-semibold cursor-pointer text-red-600 transition-all duration-[180ms]"
                    style="background:rgba(239,68,68,.07);border:1px solid rgba(239,68,68,.2);font-family:'DM Sans',sans-serif;"
                    onmouseover="this.style.background='rgba(239,68,68,.14)'"
                    onmouseout="this.style.background='rgba(239,68,68,.07)'">Close Scanner</button>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html5-qrcode/2.3.8/html5-qrcode.min.js"></script>
    <script>
        const POS = {
            paymentInterval: null,
            countdownInterval: null,
            abaInterval: null,
            abaTimerInterval: null,
            total: 0,
            method: 'cash',
            view: localStorage.getItem('pos_view') || 'grid'
        };

        function isDark() {
            return document.documentElement.classList.contains('dark');
        }

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

        function setPayment(method) {
            POS.method = method;
            ['cash', 'khqr', 'aba'].forEach(m => {
                const t = document.getElementById(`tab-${m}`);
                if (m === method) {
                    t.style.background = 'linear-gradient(135deg,#1D4ED8,#3B82F6)';
                    t.style.color = '#fff';
                    t.style.boxShadow = '0 2px 10px rgba(59,130,246,.3)'
                } else {
                    t.style.background = 'none';
                    t.style.color = 'var(--text-3)';
                    t.style.boxShadow = ''
                }
            });
            document.getElementById('cashSection').style.display = method === 'cash' ? 'flex' : 'none';
        }

        document.addEventListener('DOMContentLoaded', () => {
            setView(POS.view);
            setPayment('cash');
            document.getElementById('cashInput').addEventListener('input', calcChange);
            let st;
            document.getElementById('search').addEventListener('keyup', function(e) {
                if (e.key === 'Enter') {
                    const val = this.value.trim();
                    if (val.length >= 3) findProductByBarcode(val);
                    return;
                }
                clearTimeout(st);
                st = setTimeout(() => {
                    fetch(
                            `{{ route('admin.pos.search') }}?search=${encodeURIComponent(this.value)}`
                            )
                        .then(r => r.json()).then(renderProducts);
                }, 280);
            });
            loadCart();
        });

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
                const stockClass = oos ? 'zero' : low ? 'low' : '';
                return `<div class="product-card" onclick="addToCart(${p.id})" ${oos?'disabled':''}>
                            <div class="product-img-wrap w-full relative" style="background:var(--glass-2);">
                                <img class="product-img" src="${img}" alt="${p.name}" loading="lazy">
                                ${oos?`<div class="absolute inset-0 flex items-center justify-center z-[2]" style="background:rgba(0,0,0,.7);backdrop-filter:blur(4px);"><span class="text-[9px] font-extrabold tracking-[1.5px] uppercase text-red-300">Out of Stock</span></div>`:''}
                                ${low?`<div class="absolute top-2 right-2 z-[2] rounded-[5px] px-[6px] py-[2px] text-[8px] font-extrabold" style="background:rgba(245,158,11,.2);border:1px solid rgba(245,158,11,.5);color:#D97706;">LOW</div>`:''}
                            </div>
                            <div class="product-info">
                                <div class="product-name">${p.name}</div>
                                <div class="product-barcode">${p.barcode}</div>
                                <div class="product-price">$${parseFloat(p.sell_price).toFixed(2)}</div>
                                <div class="product-stock"><span class="stock-dot ${stockClass}"></span>${p.stock} in stock</div>
                            </div>
                            
                        </div>`;
            }).join('');
        }

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
                const cartDiv = document.getElementById('cart-items'),
                    totalEl = document.getElementById('totalAmount'),
                    subtitle = document.getElementById('cartSubtitle'),
                    btn = document.getElementById('checkoutBtn'),
                    keys = Object.keys(data);
                let sub = 0,
                    qty = 0;
                if (!keys.length) {
                    cartDiv.innerHTML =
                        `<div class="flex flex-col items-center justify-center h-full gap-3"><div class="text-4xl float-anim" style="opacity:.15;">🛒</div><div class="text-[12.5px]" style="color:var(--text-3);">Cart is empty</div></div>`;
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
                    const item = data[id],
                        lt = item.price * item.quantity;
                    sub += lt;
                    qty += item.quantity;
                    return `<div class="cart-item flex items-center gap-2 py-[9px] last:border-b-0" style="border-bottom:1px solid var(--border);">
            <div class="flex-1 min-w-0">
                <div class="text-[13px] font-semibold truncate" style="color:var(--text);">${item.name}</div>
                <div class="text-[10.5px] mt-[1px]" style="font-family:'IBM Plex Mono',monospace;color:var(--text-3);">$${parseFloat(item.price).toFixed(2)} each</div>
            </div>
            <div class="flex items-center overflow-hidden rounded-lg" style="background:var(--glass);border:1px solid var(--border);">
                <button onclick="updateQty(${id},${item.quantity-1})" class="w-[25px] h-[25px] bg-transparent border-0 text-[14px] font-bold flex items-center justify-center cursor-pointer transition-all duration-100" style="color:var(--text-3);" onmouseover="this.style.background='rgba(0,0,0,.05)';this.style.color='var(--text)'" onmouseout="this.style.background='transparent';this.style.color='var(--text-3)'">−</button>
                <span class="w-[26px] text-center text-[12px] font-bold leading-[25px]" style="color:var(--text);font-family:'IBM Plex Mono',monospace;border-left:1px solid var(--border);border-right:1px solid var(--border);">${item.quantity}</span>
                <button onclick="updateQty(${id},${item.quantity+1})" class="w-[25px] h-[25px] bg-transparent border-0 text-[14px] font-bold flex items-center justify-center cursor-pointer transition-all duration-100" style="color:var(--text-3);" onmouseover="this.style.background='rgba(0,0,0,.05)';this.style.color='var(--text)'" onmouseout="this.style.background='transparent';this.style.color='var(--text-3)'">+</button>
            </div>
            <span class="text-[12.5px] font-bold min-w-[46px] text-right" style="font-family:'IBM Plex Mono',monospace;color:var(--text);">$${lt.toFixed(2)}</span>
            <button onclick="removeItem(${id})" class="bg-transparent border-0 cursor-pointer text-[15px] px-[3px] py-[2px] leading-none transition-colors duration-100" style="color:rgba(239,68,68,.35);" onmouseover="this.style.color='#EF4444'" onmouseout="this.style.color='rgba(239,68,68,.35)'">×</button>
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
                    .catch(e => showToast('QR error: ' + e.message, 'error')).finally(resetBtn);
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
                    .then(r => r.json()).then(d => {
                        if (d.error) {
                            showToast(d.error, 'error');
                            return;
                        }
                        showAbaModal(d);
                        pollAbaPayment(d.tran_id);
                    })
                    .catch(e => showToast('ABA error: ' + e.message, 'error')).finally(resetBtn);
            }
        }

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
            const total = exp - Math.floor(Date.now() / 1000),
                bar = document.getElementById('countdownBar'),
                timer = document.getElementById('countdownTimer');
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

        function showQrModal(data) {
            document.getElementById('qrImageUSD').src = data.usd.qr_image;
            document.getElementById('qrImageKHR').src = data.khr.qr_image;
            document.getElementById('usdAmount').textContent = data.usd.label;
            document.getElementById('khrAmount').textContent = data.khr.label;
            document.getElementById('exchangeNote').textContent =
                `Exchange rate: $1 = ${Math.round(data.khr.amount/data.usd.amount).toLocaleString()} ៛`;
            document.getElementById('statusWaiting').style.display = 'flex';
            document.getElementById('statusSuccess').classList.add('hidden');
            document.getElementById('statusExpired').classList.add('hidden');
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
            document.getElementById('statusWaiting').style.display = 'none';
            document.getElementById('statusExpired').classList.add('hidden');
            document.getElementById('countdownArea').style.display = 'none';
            document.getElementById('cancelQrBtn').style.display = 'none';
            document.getElementById('statusSuccess').classList.remove('hidden');
            document.getElementById('successCurrency').textContent =
                `Paid with ${currency==='KHR'?'🇰🇭 Khmer Riel':'🇺🇸 US Dollar'}`;
        }

        function showExpiredState() {
            document.getElementById('statusWaiting').style.display = 'none';
            document.getElementById('statusSuccess').classList.add('hidden');
            document.getElementById('countdownArea').style.display = 'none';
            document.getElementById('statusExpired').classList.remove('hidden');
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

        function showAbaModal(data) {
            document.getElementById('abaQrImage').src = data.qr_image ||
                `https://api.qrserver.com/v1/create-qr-code/?size=280x280&data=${encodeURIComponent(data.qr_string)}`;
            document.getElementById('abaAmount').textContent = '$' + parseFloat(POS.total).toFixed(2);
            document.getElementById('abaWaiting').style.display = 'flex';
            document.getElementById('abaSuccess').classList.add('hidden');
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
                document.getElementById('abaTimerText').textContent =
                    `${Math.floor(rem/60)}:${(rem%60).toString().padStart(2,'0')}`;
                arc.style.transition = 'stroke-dashoffset 1s linear,stroke .5s';
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
                        document.getElementById('abaSuccess').classList.remove('hidden');
                        setTimeout(() => {
                            window.location.href = d.receipt_url;
                        }, 1500);
                    }
                } catch (e) {}
            }, 5000);
        }

        function showToast(msg, type = 'info') {
            const t = document.createElement('div');
            t.className =
                'fixed bottom-6 right-6 z-[9999] px-[18px] py-[11px] rounded-[10px] text-[13px] font-semibold text-white';
            t.style.cssText +=
                `background:${type==='error'?'rgba(220,38,38,.95)':'rgba(34,197,94,.95)'};box-shadow:0 4px 24px rgba(0,0,0,.25);backdrop-filter:blur(10px);animation:itemIn .2s ease;font-family:'DM Sans',sans-serif;`;
            t.textContent = msg;
            document.body.appendChild(t);
            setTimeout(() => {
                t.style.opacity = '0';
                t.style.transform = 'translateY(4px)';
                t.style.transition = 'all .2s';
                setTimeout(() => t.remove(), 200);
            }, 2800);
        }

        function findProductByBarcode(barcode) {
            fetch(`{{ route('admin.pos.findByBarcode') }}?barcode=${encodeURIComponent(barcode)}`).then(r => r.json())
                .then(d => {
                    if (d.found) {
                        addToCart(d.product.id);
                        const s = document.getElementById('search');
                        s.value = '';
                        s.style.borderColor = 'rgba(34,197,94,.6)';
                        s.style.background = 'rgba(34,197,94,.06)';
                        s.style.boxShadow = '0 0 0 3px rgba(34,197,94,.1)';
                        showToast('✅ ' + d.product.name + ' added to cart', 'success');
                        setTimeout(() => {
                            s.style.borderColor = '';
                            s.style.background = 'var(--glass)';
                            s.style.boxShadow = '';
                        }, 800);
                    } else {
                        fetch(`{{ route('admin.pos.search') }}?search=${encodeURIComponent(barcode)}`).then(r => r
                            .json()).then(renderProducts);
                        showToast('No product found for: ' + barcode, 'error');
                    }
                }).catch(() => {});
        }

        let html5QrScanner = null,
            scanCooldown = false;

        function openCameraScanner() {
            document.getElementById('cameraBackdrop').style.display = 'flex';
            const resultEl = document.getElementById('scanResult');
            resultEl.textContent = 'Waiting for barcode…';
            resultEl.style.color = 'var(--text-3)';
            html5QrScanner = new Html5Qrcode('qr-reader');
            html5QrScanner.start({
                facingMode: 'environment'
            }, {
                fps: 10,
                qrbox: {
                    width: 220,
                    height: 120
                },
                aspectRatio: 1.0
            }, (decodedText) => {
                if (scanCooldown) return;
                scanCooldown = true;
                resultEl.textContent = '🔍 Found: ' + decodedText;
                resultEl.style.color = '#3B82F6';
                fetch(`{{ route('admin.pos.findByBarcode') }}?barcode=${encodeURIComponent(decodedText)}`).then(
                    r => r.json()).then(d => {
                    if (d.found) {
                        resultEl.textContent = '✅ ' + d.product.name + ' — adding…';
                        resultEl.style.color = '#22C55E';
                        addToCart(d.product.id);
                        setTimeout(() => {
                            closeCameraScanner();
                            showToast('✅ ' + d.product.name + ' added to cart', 'success');
                        }, 700);
                    } else {
                        resultEl.textContent = '❌ No product found for: ' + decodedText;
                        resultEl.style.color = '#EF4444';
                        setTimeout(() => {
                            resultEl.textContent = 'Waiting for barcode…';
                            resultEl.style.color = 'var(--text-3)';
                            scanCooldown = false;
                        }, 2000);
                    }
                });
            }, () => {}).catch(err => {
                resultEl.textContent = '❌ Camera error: ' + err;
                resultEl.style.color = '#EF4444';
            });
        }

        function closeCameraScanner() {
            if (html5QrScanner) {
                html5QrScanner.stop().then(() => {
                    html5QrScanner.clear();
                    html5QrScanner = null;
                    scanCooldown = false;
                }).catch(() => {
                    html5QrScanner = null;
                    scanCooldown = false;
                });
            }
            document.getElementById('cameraBackdrop').style.display = 'none';
        }
    </script>
@endpush
