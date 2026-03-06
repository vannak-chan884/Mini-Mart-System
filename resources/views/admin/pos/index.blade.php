@extends('layouts.app')
@section('title', 'POS Terminal')

@push('styles')
    <style>
        .content-area {
            padding: 0 !important;
            overflow: hidden !important;
        }

        /* Scrollbars — cannot be done in Tailwind */
        #product-grid::-webkit-scrollbar {
            width: 5px;
        }

        #product-grid::-webkit-scrollbar-track {
            background: transparent;
        }

        #product-grid::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.07);
            border-radius: 3px;
        }

        #cart-items::-webkit-scrollbar {
            width: 4px;
        }

        #cart-items::-webkit-scrollbar-track {
            background: transparent;
        }

        #cart-items::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.07);
            border-radius: 2px;
        }

        /* Animations */
        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateX(8px);
            }

            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        @keyframes modalIn {
            from {
                opacity: 0;
                transform: scale(0.95) translateY(10px);
            }

            to {
                opacity: 1;
                transform: scale(1) translateY(0);
            }
        }

        @keyframes pulseDot {

            0%,
            100% {
                opacity: 1;
                transform: scale(1);
            }

            50% {
                opacity: 0.3;
                transform: scale(0.7);
            }
        }

        .cart-item {
            animation: slideIn 0.18s ease;
        }

        .modal-in {
            animation: modalIn 0.3s ease;
        }

        .pulse-dot {
            animation: pulseDot 1.4s ease infinite;
        }

        /* Product image zoom */
        .product-card:hover .product-img {
            transform: scale(1.05);
        }

        /* Product name clamp */
        .product-name {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        /* Grid layout — auto-fill not possible in Tailwind */
        #product-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
            gap: 12px;
            overflow-y: auto;
            flex: 1;
            padding-right: 4px;
            align-content: start;
        }

        @media (max-width: 900px) {
            #product-grid:not(.list-view) {
                grid-template-columns: repeat(auto-fill, minmax(130px, 1fr));
            }
        }

        /* List view — structural changes not possible with Tailwind alone */
        #product-grid.list-view {
            grid-template-columns: 1fr;
            gap: 6px;
        }

        #product-grid.list-view .product-card {
            flex-direction: row;
            align-items: stretch;
            height: 66px;
        }

        #product-grid.list-view .product-img-wrap {
            width: 66px;
            height: 66px;
            aspect-ratio: unset;
            border-radius: 0;
            flex-shrink: 0;
        }

        #product-grid.list-view .product-card:hover .product-img {
            transform: scale(1.1);
        }

        #product-grid.list-view .product-info {
            flex-direction: row;
            align-items: center;
            padding: 0 14px;
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
            font-size: 14px;
            min-width: 60px;
            text-align: right;
            padding: 0 10px;
        }

        #product-grid.list-view .product-stock {
            font-size: 11px;
            min-width: 68px;
            text-align: right;
            padding-right: 10px;
            white-space: nowrap;
        }

        #product-grid.list-view .btn-add {
            margin: 0;
            border-radius: 0 10px 10px 0;
            padding: 0 16px;
            height: 100%;
            width: auto;
            align-self: stretch;
            display: flex;
            align-items: center;
            justify-content: center;
            white-space: nowrap;
        }

        /* KHQR modal open state */
        .qr-backdrop.open {
            display: flex;
        }

        /* KHQR modal header shimmer line */
        .qr-modal-header::before {
            content: '';
            position: absolute;
            bottom: 0;
            left: 10%;
            right: 10%;
            height: 1px;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
        }

        /* Countdown bar transition */
        .countdown-bar {
            transition: width 1s linear, background 1s ease;
        }

        /* Mobile layout */
        @media (max-width: 700px) {
            .pos-shell {
                flex-direction: column;
            }

            .pos-right {
                width: 100%;
                height: 380px;
                border-right: none;
                border-top: 1px solid rgba(255, 255, 255, 0.07);
            }

            .pos-left {
                flex: none;
                height: calc(100% - 380px);
            }

            .qr-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
@endpush

@section('content')
    <div class="pos-shell flex h-full overflow-hidden">

        {{-- ── LEFT: PRODUCTS ───────────────────────────── --}}
        <div class="pos-left flex-1 flex flex-col p-4 gap-3 min-w-0 overflow-hidden border-r border-white/[0.07]">

            {{-- Search + View Toggle --}}
            <div class="flex items-center gap-2.5 flex-shrink-0">
                <div class="relative flex-1">
                    <span
                        class="absolute left-3.5 top-1/2 -translate-y-1/2 text-[15px] text-gray-500 pointer-events-none">🔍</span>
                    <input type="text" id="search"
                        class="w-full bg-white/[0.05] border border-white/[0.07] rounded-[11px]
                           py-[11px] pr-4 pl-[42px] text-sm text-[#E8E4DC] outline-none
                           placeholder:text-gray-500/40
                           focus:border-blue-500/50 focus:bg-[rgba(0,48,135,0.08)] focus:shadow-[0_0_0_3px_rgba(0,48,135,0.12)]
                           transition-all duration-200"
                        placeholder="Scan barcode or search product..." autofocus>
                </div>
                {{-- View toggle --}}
                <div class="flex gap-[3px] bg-white/[0.03] border border-white/[0.07] rounded-[10px] p-[3px] flex-shrink-0">
                    <button id="btn-grid" onclick="setView('grid')" title="Grid view"
                        class="view-btn w-[34px] h-[34px] rounded-[7px] border-0 bg-transparent text-gray-500
                           flex items-center justify-center cursor-pointer transition-all duration-150
                           hover:bg-white/[0.06] hover:text-gray-400">
                        <svg width="16" height="16" viewBox="0 0 16 16" fill="currentColor">
                            <rect x="1" y="1" width="6" height="6" rx="1" />
                            <rect x="9" y="1" width="6" height="6" rx="1" />
                            <rect x="1" y="9" width="6" height="6" rx="1" />
                            <rect x="9" y="9" width="6" height="6" rx="1" />
                        </svg>
                    </button>
                    <button id="btn-list" onclick="setView('list')" title="List view"
                        class="view-btn w-[34px] h-[34px] rounded-[7px] border-0 bg-transparent text-gray-500
                           flex items-center justify-center cursor-pointer transition-all duration-150
                           hover:bg-white/[0.06] hover:text-gray-400">
                        <svg width="16" height="16" viewBox="0 0 16 16" fill="currentColor">
                            <rect x="1" y="2" width="14" height="2.5" rx="1" />
                            <rect x="1" y="6.75" width="14" height="2.5" rx="1" />
                            <rect x="1" y="11.5" width="14" height="2.5" rx="1" />
                        </svg>
                    </button>
                </div>
            </div>

            {{-- Product Grid --}}
            <div id="product-grid">
                @foreach ($products as $product)
                    <div
                        class="product-card bg-white/[0.03] border border-white/[0.07] rounded-[13px]
                             overflow-hidden cursor-pointer flex flex-col
                             transition-all duration-[180ms] ease
                             hover:border-[rgba(0,80,200,0.4)] hover:bg-[rgba(0,48,135,0.1)]
                             hover:-translate-y-0.5 hover:shadow-[0_6px_20px_rgba(0,0,0,0.3)]
                             active:translate-y-0">
                        <div
                            class="product-img-wrap w-full aspect-square overflow-hidden bg-white/[0.04] relative flex-shrink-0">
                            <img class="product-img w-full h-full object-cover transition-transform duration-300"
                                src="{{ $product->image ? asset('storage/' . $product->image) : asset('images/no-image.png') }}"
                                alt="{{ $product->name }}" loading="lazy">
                            @if ($product->stock <= 0)
                                <div
                                    class="absolute inset-0 bg-black/65 flex items-center justify-center
                                        text-[11px] font-bold text-red-300 tracking-wide uppercase">
                                    Out of Stock
                                </div>
                            @endif
                        </div>
                        <div class="product-info p-[9px_11px_11px] flex-1 flex flex-col gap-[3px]">
                            <div class="product-name text-[12.5px] font-semibold text-[#E8E4DC] leading-[1.3]">
                                {{ $product->name }}</div>
                            <div class="product-price font-mono text-[13px] font-bold text-green-400">
                                ${{ number_format($product->sell_price, 2) }}</div>
                            <div class="product-stock text-[10.5px] text-gray-500">Stock: {{ $product->stock }}</div>
                        </div>
                        <button
                            class="btn-add mx-[10px] mb-[10px] py-[7px]
                                   bg-gradient-to-br from-[#003087] to-[#1a4db3] text-white
                                   border-0 rounded-lg text-xs font-semibold cursor-pointer
                                   shadow-[0_2px_8px_rgba(0,48,135,0.3)]
                                   hover:shadow-[0_4px_14px_rgba(0,48,135,0.45)]
                                   disabled:bg-white/[0.06] disabled:text-gray-500 disabled:cursor-not-allowed disabled:shadow-none
                                   transition-all duration-150"
                            onclick="addToCart({{ $product->id }})" {{ $product->stock <= 0 ? 'disabled' : '' }}>
                            + Add to Cart
                        </button>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- ── RIGHT: CART ───────────────────────────────── --}}
        <div class="pos-right w-80 flex-shrink-0 flex flex-col bg-white dark:bg-gray-900 overflow-hidden">

            {{-- Cart Header --}}
            <div
                class="flex items-center justify-between px-5 pt-[18px] pb-[14px] border-b border-gray-800/50 dark:border-white/[0.07] flex-shrink-0">
                <div class="flex items-center gap-2 font-serif text-[17px] font-bold text-gray-800 dark:text-white">
                    🛒 Cart
                    <span id="cartCount"
                        class="bg-[rgba(0,48,135,0.3)] border border-[rgba(0,48,135,0.45)] dark:text-blue-300
                           text-[11px] font-bold py-[2px] px-2 rounded-full">0</span>
                </div>
                <button onclick="clearCart()"
                    class="bg-transparent border border-red-800 dark:border-[rgba(204,0,1,0.25)] text-red-600 dark:text-red-400/60
                       text-[11px] py-1 px-2.5 rounded-md cursor-pointer
                       hover:border-[rgba(204,0,1,0.5)] dark:hover:text-red-300 dark:hover:bg-[rgba(204,0,1,0.08)]
                       transition-all duration-150">Clear</button>
            </div>

            {{-- Cart Items --}}
            <div id="cart-items" class="flex-1 overflow-y-auto px-4 py-3">
                <div class="cart-empty flex flex-col items-center justify-center h-full gap-2.5 text-gray-500">
                    <div class="text-4xl opacity-30">🛒</div>
                    <div class="text-[13px]">Cart is empty</div>
                </div>
            </div>

            {{-- Cart Footer --}}
            <div class="border-t border-gray-800/50 dark:border-white/[0.07] p-4 flex-shrink-0 flex flex-col gap-3">

                {{-- Total --}}
                <div class="flex justify-between items-center">
                    <span class="text-[13px] dark:text-gray-400 font-medium">Total</span>
                    <span id="totalAmount" class="font-mono text-2xl font-bold dark:text-white">$0.00</span>
                </div>

                {{-- Payment tabs --}}
                <div class="flex gap-1.5 dark:bg-white/[0.03] border dark:border-white/[0.07] rounded-[10px] p-1">
                    <button
                        class="payment-tab flex-1 py-2 px-1.5 rounded-[7px] border-0 bg-transparent
                               dark:text-gray-400 text-xs font-semibold cursor-pointer text-center
                               transition-all duration-[180ms]"
                        id="tab-cash" onclick="setPayment('cash')">💵 Cash</button>
                    <button
                        class="payment-tab flex-1 py-2 px-1.5 rounded-[7px] border-0 bg-transparent
                               dark:text-gray-400 text-xs font-semibold cursor-pointer text-center
                               transition-all duration-[180ms]"
                        id="tab-khqr" onclick="setPayment('khqr')">📱 KHQR</button>
                    <button
                        class="payment-tab flex-1 py-2 px-1.5 rounded-[7px] border-0 bg-transparent
                               dark:text-gray-400 text-xs font-semibold cursor-pointer text-center
                               transition-all duration-[180ms]"
                        id="tab-aba" onclick="setPayment('aba')">🏦 ABA</button>
                </div>

                {{-- Cash section --}}
                <div id="cashSection" class="flex flex-col gap-2">
                    <div class="text-[11px] font-bold uppercase tracking-[0.7px] dark:text-gray-500">Cash Received</div>
                    <input type="number" id="cashInput" placeholder="0.00" step="0.01"
                        class="w-full dark:bg-white/[0.05] border dark:border-white/[0.07] rounded-[9px]
                           py-[10px] px-3.5 text-[15px] font-mono text-white outline-none
                           dark:focus:border-green-500/50 dark:focus:bg-[rgba(22,163,74,0.05)] focus:shadow-[0_0_0_3px_rgba(22,163,74,0.1)]
                           transition-all duration-200">
                    <div
                        class="flex justify-between items-center dark:bg-[rgba(22,163,74,0.08)] border dark:border-[rgba(22,163,74,0.2)] rounded-lg py-2 px-3">
                        <span class="text-xs dark:text-green-300 font-semibold">Change</span>
                        <span id="changeAmount" class="font-mono text-[15px] font-bold dark:text-green-300">$0.00</span>
                    </div>
                </div>

                {{-- Checkout button --}}
                <button id="checkoutBtn" onclick="checkout()" disabled
                    class="w-full py-3.5 bg-green-600 text-white border-0
                       rounded-xl text-[15px] font-bold cursor-pointer flex items-center justify-content-center gap-2
                       dark:shadow-[0_4px_16px_rgba(22,163,74,0.3)] justify-center
                       hover:not(:disabled):-translate-y-0.5 hover:not(:disabled):shadow-[0_8px_24px_rgba(22,163,74,0.45)]
                       disabled:bg-white/[0.06] disabled:text-gray-500 disabled:cursor-not-allowed disabled:shadow-none
                       transition-all duration-[220ms]">
                    Checkout
                </button>
            </div>
        </div>
    </div>

    {{-- ══ KHQR MODAL ════════════════════════════════════════ --}}
    <div class="qr-backdrop hidden fixed inset-0 bg-black/85 backdrop-blur-lg items-center justify-center z-[200] p-5"
        id="qrBackdrop">
        <div
            class="modal-in bg-[#13131F] border border-white/[0.1] rounded-3xl w-full max-width-[600px] max-w-[600px]
                overflow-hidden shadow-[0_24px_80px_rgba(0,0,0,0.6)]">

            {{-- Header --}}
            <div
                class="qr-modal-header relative bg-gradient-to-br from-[#001B5C] via-[#003087] to-[#1a4db3] px-7 py-[22px] text-center">
                <div class="flex h-[3px] w-[50px] rounded-sm overflow-hidden gap-px mx-auto mb-3.5">
                    <span class="bg-[#CC0001] flex-1"></span>
                    <span class="bg-[#4a90d9]" style="flex:2"></span>
                    <span class="bg-[#CC0001] flex-1"></span>
                </div>
                <div class="font-serif text-xl font-black text-white mb-1">Scan to Pay with KHQR</div>
                <div class="text-[13px] text-white/55">Choose either currency — all NBC Bakong banks supported</div>
            </div>

            {{-- Body --}}
            <div class="p-6">
                <div class="qr-grid grid grid-cols-2 gap-3.5 mb-5">
                    {{-- USD --}}
                    <div
                        class="qr-card usd rounded-2xl p-4 text-center bg-[rgba(0,48,135,0.1)] border border-[rgba(0,48,135,0.3)]">
                        <div class="flex items-center justify-center gap-1.5 text-[13px] font-bold text-blue-300 mb-1.5">
                            🇺🇸 US Dollar</div>
                        <div id="usdAmount" class="font-mono text-xl font-bold text-blue-400 mb-3"></div>
                        <div class="bg-white rounded-[10px] p-2.5 w-full"><img id="qrImageUSD" src=""
                                alt="USD QR" class="w-full block rounded-sm"></div>
                        <div class="text-[10px] text-gray-500 mt-2">ABA · Wing · ACLEDA · all banks</div>
                    </div>
                    {{-- KHR --}}
                    <div
                        class="qr-card khr rounded-2xl p-4 text-center bg-[rgba(204,0,1,0.08)] border border-[rgba(204,0,1,0.25)]">
                        <div class="flex items-center justify-center gap-1.5 text-[13px] font-bold text-red-300 mb-1.5">
                            🇰🇭 Khmer Riel</div>
                        <div id="khrAmount" class="font-mono text-xl font-bold text-red-400 mb-3"></div>
                        <div class="bg-white rounded-[10px] p-2.5 w-full"><img id="qrImageKHR" src=""
                                alt="KHR QR" class="w-full block rounded-sm"></div>
                        <div class="text-[10px] text-gray-500 mt-2">ABA · Wing · ACLEDA · all banks</div>
                    </div>
                </div>

                {{-- Status --}}
                <div class="mb-4">
                    <div id="statusWaiting"
                        class="text-center py-3 px-3 bg-[rgba(0,48,135,0.1)] border border-[rgba(0,48,135,0.25)] rounded-xl text-[13px] text-blue-300 font-medium">
                        <span
                            class="pulse-dot inline-block w-[7px] h-[7px] bg-blue-300 rounded-full mr-2 align-middle"></span>Waiting
                        for payment on either QR...
                    </div>
                    <div id="statusSuccess"
                        class="hidden py-4 px-4 bg-[rgba(22,163,74,0.12)] border border-[rgba(22,163,74,0.3)] rounded-xl text-center">
                        <div class="text-3xl mb-1.5">✅</div>
                        <div class="text-base font-bold text-green-300 mb-1">Payment Received!</div>
                        <div id="successCurrency" class="text-xs text-gray-400"></div>
                        <div class="text-xs text-gray-500 mt-1">Redirecting to receipt...</div>
                    </div>
                    <div id="statusExpired"
                        class="hidden py-3 px-3 bg-[rgba(204,0,1,0.1)] border border-[rgba(204,0,1,0.25)] rounded-xl text-center text-[13px] text-red-300">
                        ❌ QR Codes Expired — click cancel and try again.
                    </div>
                </div>

                {{-- Countdown --}}
                <div id="countdownArea" class="mb-4">
                    <div class="flex justify-between text-xs text-gray-400 mb-1.5">
                        <span>Expires in</span>
                        <span id="countdownTimer" class="font-mono font-bold text-orange-400">5:00</span>
                    </div>
                    <div class="h-[5px] bg-white/[0.07] rounded-full overflow-hidden">
                        <div id="countdownBar" class="countdown-bar h-full rounded-full bg-green-500" style="width:100%">
                        </div>
                    </div>
                </div>

                <div id="exchangeNote" class="text-center text-[11px] text-gray-500 mb-3.5"></div>

                <button id="cancelQrBtn" onclick="closeQrPopup()"
                    class="w-full py-[11px] bg-[rgba(204,0,1,0.12)] border border-[rgba(204,0,1,0.3)] rounded-[10px]
                       text-red-300 text-[13px] font-semibold cursor-pointer
                       hover:bg-[rgba(204,0,1,0.22)] transition-all duration-[180ms]">
                    Cancel Transaction
                </button>
            </div>
        </div>
    </div>

    {{-- ── ABA PAYWAY MODAL ── --}}
    <div id="abaBackdrop" style="display:none;"
        class="fixed inset-0 bg-black/85 backdrop-blur-lg z-[300] items-center justify-center p-5">
        <div
            class="modal-in bg-[#1C1C2E] border border-white/[0.1] rounded-3xl w-full max-w-[360px]
                px-7 py-8 text-center shadow-[0_24px_80px_rgba(0,0,0,0.6)]">

            <div
                class="w-14 h-14 bg-gradient-to-br from-[#003087] to-[#1a4db3] rounded-2xl
                    flex items-center justify-center text-[26px] mx-auto mb-4
                    shadow-[0_8px_24px_rgba(0,48,135,0.4)]">
                🏦</div>
            <div class="font-serif text-xl font-black text-white mb-1">ABA PayWay</div>
            <div class="text-[13px] text-white/45 mb-5">Scan with ABA Mobile app to pay</div>

            <div id="abaAmount" class="font-mono text-[22px] font-bold text-blue-400 mb-4"></div>

            <div
                class="bg-white rounded-2xl p-3 mx-auto mb-5 w-[220px] h-[220px] flex items-center justify-content-center justify-center">
                <img id="abaQrImage" src="" alt="ABA QR Code" class="w-full h-full object-contain rounded-lg">
            </div>

            <div id="abaWaiting"
                class="flex items-center justify-center gap-2 py-[10px] px-4
                    bg-[rgba(0,48,135,0.15)] border border-[rgba(0,48,135,0.3)] rounded-[10px]
                    text-[13px] text-blue-300 font-medium mb-3">
                <span class="pulse-dot w-[7px] h-[7px] bg-blue-300 rounded-full inline-block"></span>
                Waiting for payment...
            </div>

            {{-- ABA 3-min countdown timer --}}
            <div id="abaTimerWrap" class="flex items-center justify-center gap-2 mb-4">
                <svg width="18" height="18" viewBox="0 0 18 18" style="flex-shrink:0;transform:rotate(-90deg)">
                    <circle cx="9" cy="9" r="7" fill="none" stroke="rgba(147,197,253,0.2)"
                        stroke-width="2" />
                    <circle id="abaTimerArc" cx="9" cy="9" r="7" fill="none" stroke="#93C5FD"
                        stroke-width="2" stroke-dasharray="43.98" stroke-dashoffset="0" stroke-linecap="round" />
                </svg>
                <span id="abaTimerText" class="font-mono text-[13px] font-bold text-blue-300">3:00</span>
            </div>

            <div id="abaSuccess"
                class="hidden py-4 px-4 bg-[rgba(22,163,74,0.12)] border border-[rgba(22,163,74,0.3)] rounded-xl mb-4">
                <div class="text-[28px] mb-1.5">✅</div>
                <div class="text-[15px] font-bold text-green-300">Payment Confirmed!</div>
                <div class="text-xs text-gray-500 mt-1">Redirecting to receipt...</div>
            </div>

            <button onclick="closeAbaModal()"
                class="w-full py-[11px] bg-[rgba(204,0,1,0.12)] border border-[rgba(204,0,1,0.3)] rounded-[10px]
                   text-red-300 text-[13px] font-semibold cursor-pointer
                   hover:bg-[rgba(204,0,1,0.22)] transition-all duration-[180ms]">
                Cancel Transaction
            </button>
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        const POS_STATE = {
            paymentInterval: null,
            countdownInterval: null,
            abaInterval: null,
            abaTimerInterval: null,
            totalAmount: 0,
            paymentMethod: 'cash',
            viewMode: localStorage.getItem('pos_view') || 'grid',
        };

        // ── View toggle ───────────────────────────────────────
        function setView(mode) {
            POS_STATE.viewMode = mode;
            localStorage.setItem('pos_view', mode);
            document.getElementById('product-grid').classList.toggle('list-view', mode === 'list');
            document.getElementById('btn-grid').classList.toggle('active', mode === 'grid');
            document.getElementById('btn-list').classList.toggle('active', mode === 'list');
            // Active style via JS (Tailwind active: doesn't apply to custom class)
            ['grid', 'list'].forEach(m => {
                const btn = document.getElementById(`btn-${m}`);
                if (m === mode) {
                    btn.style.background = 'rgba(0,48,135,0.4)';
                    btn.style.color = '#93C5FD';
                } else {
                    btn.style.background = '';
                    btn.style.color = '';
                }
            });
        }

        // ── Payment tabs ──────────────────────────────────────
        function setPayment(method) {
            POS_STATE.paymentMethod = method;
            ['cash', 'khqr', 'aba'].forEach(m => {
                const tab = document.getElementById(`tab-${m}`);
                if (m === method) {
                    tab.style.background = 'linear-gradient(135deg,#003087,#1a4db3)';
                    tab.style.color = '#fff';
                    tab.style.boxShadow = '0 2px 8px rgba(0,48,135,0.35)';
                } else {
                    tab.style.background = '';
                    tab.style.color = '';
                    tab.style.boxShadow = '';
                }
            });
            document.getElementById('cashSection').style.display = method === 'cash' ? 'flex' : 'none';
        }

        // ── Init ──────────────────────────────────────────────
        document.addEventListener('DOMContentLoaded', function() {
            setView(POS_STATE.viewMode);
            setPayment('cash');
            document.getElementById('cashInput').addEventListener('input', calculateChange);

            let searchTimeout = null;
            document.getElementById('search').addEventListener('keyup', function() {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    fetch(
                            `{{ route('admin.pos.search') }}?search=${encodeURIComponent(this.value)}`
                            )
                        .then(r => r.json()).then(renderProducts);
                }, 280);
            });

            loadCart();
        });

        // ── Render products ───────────────────────────────────
        function renderProducts(products) {
            const grid = document.getElementById('product-grid');
            if (!products.length) {
                grid.innerHTML =
                    `<div style="grid-column:1/-1;text-align:center;padding:40px;color:#6B7280;font-size:13px;">No products found.</div>`;
                return;
            }
            grid.innerHTML = products.map(p => {
                const img = p.image ? `/storage/${p.image}` : '/images/no-image.png';
                const oos = p.stock <= 0;
                return `<div class="product-card bg-white/[0.03] border border-white/[0.07] rounded-[13px] overflow-hidden cursor-pointer flex flex-col transition-all duration-[180ms]">
                <div class="product-img-wrap w-full aspect-square overflow-hidden bg-white/[0.04] relative flex-shrink-0">
                    <img class="product-img w-full h-full object-cover transition-transform duration-300" src="${img}" alt="${p.name}" loading="lazy">
                    ${oos ? '<div class="absolute inset-0 bg-black/65 flex items-center justify-center text-[11px] font-bold text-red-300 tracking-wide uppercase">Out of Stock</div>' : ''}
                </div>
                <div class="product-info p-[9px_11px_11px] flex-1 flex flex-col gap-[3px]">
                    <div class="product-name text-[12.5px] font-semibold text-[#E8E4DC] leading-[1.3]">${p.name}</div>
                    <div class="product-price font-mono text-[13px] font-bold text-green-400">$${parseFloat(p.sell_price).toFixed(2)}</div>
                    <div class="product-stock text-[10.5px] text-gray-500">Stock: ${p.stock}</div>
                </div>
                <button class="btn-add mx-[10px] mb-[10px] py-[7px] bg-gradient-to-br from-[#003087] to-[#1a4db3] text-white border-0 rounded-lg text-xs font-semibold cursor-pointer shadow-[0_2px_8px_rgba(0,48,135,0.3)] transition-all duration-150"
                        onclick="addToCart(${p.id})" ${oos ? 'disabled' : ''}>+ Add to Cart</button>
            </div>`;
            }).join('');
        }

        // ── Cart ──────────────────────────────────────────────
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
                const countEl = document.getElementById('cartCount');
                const checkBtn = document.getElementById('checkoutBtn');
                const keys = Object.keys(data);
                let subtotal = 0,
                    totalQty = 0;

                if (!keys.length) {
                    cartDiv.innerHTML = `<div class="cart-empty flex flex-col items-center justify-center h-full gap-2.5 text-gray-500">
                    <div class="text-4xl opacity-30">🛒</div>
                    <div class="text-[13px]">Cart is empty</div></div>`;
                    countEl.textContent = '0';
                    totalEl.textContent = '$0.00';
                    POS_STATE.totalAmount = 0;
                    checkBtn.disabled = true;
                    calculateChange();
                    return;
                }

                cartDiv.innerHTML = keys.map(id => {
                    const item = data[id];
                    const lt = item.price * item.quantity;
                    subtotal += lt;
                    totalQty += item.quantity;
                    return `<div class="cart-item flex items-center gap-2.5 py-2.5 border-b dark:border-white/[0.04] last:border-b-0">
                                <div class="flex-1 min-w-0">
                                    <div class="text-[13px] font-semibold dark:text-[#E8E4DC] truncate">${item.name}</div>
                                    <div class="font-mono text-[11px] dark:text-gray-400 mt-px">$${parseFloat(item.price).toFixed(2)} each</div>
                                </div>
                                <div class="flex items-center dark:bg-white/[0.04] border dark:border-white/[0.07] rounded-lg overflow-hidden">
                                    <button class="w-[26px] h-[26px] bg-transparent border-0 dark:text-gray-400 cursor-pointer text-sm font-bold flex items-center justify-center dark:hover:bg-white/[0.08] dark:hover:text-white transition-all duration-100"
                                            onclick="updateQty(${id}, ${item.quantity - 1})">-</button>
                                    <span class="w-7 text-center text-[13px] font-bold dark:text-white font-mono border-x dark:border-white/[0.07] leading-[26px]">${item.quantity}</span>
                                    <button class="w-[26px] h-[26px] bg-transparent border-0 dark:text-gray-400 cursor-pointer text-sm font-bold flex items-center justify-center dark:hover:bg-white/[0.08] dark:hover:text-white transition-all duration-100"
                                            onclick="updateQty(${id}, ${item.quantity + 1})">+</button>
                                </div>
                                <span class="font-mono text-[13px] font-bold dark:text-white min-w-[52px] text-right">$${lt.toFixed(2)}</span>
                                <button class="bg-transparent border-0 text-red-600/40 dark:text-red-400/40 cursor-pointer text-base px-1 hover:text-red-500 dark:hover:text-red-300 transition-colors duration-100"
                                        onclick="removeItem(${id})">x</button>
                            </div>`;
                }).join('');

                POS_STATE.totalAmount = subtotal;
                totalEl.textContent = '$' + subtotal.toFixed(2);
                countEl.textContent = totalQty;
                checkBtn.disabled = false;
                calculateChange();
            });
        }

        function calculateChange() {
            const cash = parseFloat(document.getElementById('cashInput').value) || 0;
            document.getElementById('changeAmount').textContent =
                '$' + Math.max(0, cash - POS_STATE.totalAmount).toFixed(2);
        }

        // ── Checkout ──────────────────────────────────────────
        function checkout() {
            const method = POS_STATE.paymentMethod;

            if (method === 'cash') {
                const cash = parseFloat(document.getElementById('cashInput').value) || 0;
                if (cash < POS_STATE.totalAmount) {
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

            if (POS_STATE.totalAmount <= 0) {
                showToast('Cart is empty!', 'error');
                return;
            }
            const btn = document.getElementById('checkoutBtn');

            if (method === 'khqr') {
                btn.disabled = true;
                btn.innerHTML = '⏳ Generating QR...';
                fetch("{{ route('admin.pos.generateKhqr') }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            amount: POS_STATE.totalAmount
                        })
                    })
                    .then(async res => {
                        const d = await res.json();
                        if (!res.ok || d.error) throw new Error(d.error || 'QR generation failed');
                        return d;
                    })
                    .then(d => {
                        showQrModal(d);
                        startCountdown(d.expires_at);
                        pollBothPayments(d.usd.md5, d.khr.md5, d.expires_at);
                    })
                    .catch(err => showToast('Could not generate QR: ' + err.message, 'error'))
                    .finally(() => {
                        btn.disabled = false;
                        btn.innerHTML = '✓ Checkout';
                    });
                return;
            }

            if (method === 'aba') {
                btn.disabled = true;
                btn.innerHTML = '⏳ Generating ABA QR...';
                fetch("{{ route('admin.pos.payway.generate') }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            amount: POS_STATE.totalAmount
                        })
                    })
                    .then(r => r.json())
                    .then(data => {
                        if (data.error) {
                            showToast(data.error, 'error');
                            return;
                        }
                        showAbaModal(data);
                        pollAbaPayment(data.tran_id);
                    })
                    .catch(err => showToast('ABA PayWay error: ' + err.message, 'error'))
                    .finally(() => {
                        btn.disabled = false;
                        btn.innerHTML = '✓ Checkout';
                    });
                return;
            }
        }

        // ── KHQR polling & countdown ──────────────────────────
        function pollBothPayments(md5USD, md5KHR, expiresAt) {
            if (POS_STATE.paymentInterval) clearInterval(POS_STATE.paymentInterval);
            let paid = false;
            POS_STATE.paymentInterval = setInterval(() => {
                if (paid) return;
                if (Math.floor(Date.now() / 1000) >= expiresAt) {
                    clearInterval(POS_STATE.paymentInterval);
                    clearInterval(POS_STATE.countdownInterval);
                    showExpiredState();
                    return;
                }
                pollSingle(md5USD, 'usd', () => {
                    paid = true;
                });
                setTimeout(() => {
                    if (!paid) pollSingle(md5KHR, 'khr', () => {
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
                    clearInterval(POS_STATE.paymentInterval);
                    clearInterval(POS_STATE.countdownInterval);
                    showSuccessState(d.currency);
                    setTimeout(() => {
                        window.location.href = d.receipt_url || `/admin/pos/receipt/${d.sale_id}`;
                    }, 1500);
                }
            }).catch(() => {});
        }

        function startCountdown(expiresAt) {
            if (POS_STATE.countdownInterval) clearInterval(POS_STATE.countdownInterval);
            const total = expiresAt - Math.floor(Date.now() / 1000);
            const bar = document.getElementById('countdownBar');
            const timer = document.getElementById('countdownTimer');
            POS_STATE.countdownInterval = setInterval(() => {
                const rem = expiresAt - Math.floor(Date.now() / 1000);
                if (rem <= 0) {
                    clearInterval(POS_STATE.countdownInterval);
                    timer.textContent = '0:00';
                    bar.style.width = '0%';
                    bar.style.background = '#ef4444';
                    return;
                }
                timer.textContent = `${Math.floor(rem/60)}:${(rem%60).toString().padStart(2,'0')}`;
                const pct = (rem / total) * 100;
                bar.style.width = pct + '%';
                bar.style.background = pct > 50 ? '#22C55E' : pct > 25 ? '#F97316' : '#ef4444';
                if (rem <= 30) timer.style.color = '#ef4444';
            }, 1000);
        }

        // ── KHQR modal helpers ────────────────────────────────
        function showQrModal(data) {
            document.getElementById('qrImageUSD').src = data.usd.qr_image;
            document.getElementById('qrImageKHR').src = data.khr.qr_image;
            document.getElementById('usdAmount').textContent = data.usd.label;
            document.getElementById('khrAmount').textContent = data.khr.label;
            document.getElementById('exchangeNote').textContent =
                `Exchange rate: $1 = ${Math.round(data.khr.amount / data.usd.amount).toLocaleString()} ៛`;
            document.getElementById('statusWaiting').style.display = 'block';
            document.getElementById('statusSuccess').style.display = 'none';
            document.getElementById('statusExpired').style.display = 'none';
            document.getElementById('countdownArea').style.display = 'block';
            document.getElementById('cancelQrBtn').style.display = 'block';
            document.getElementById('countdownTimer').style.color = '#F97316';
            document.getElementById('countdownBar').style.width = '100%';
            document.getElementById('countdownBar').style.background = '#22C55E';
            const bd = document.getElementById('qrBackdrop');
            bd.classList.remove('hidden');
            bd.classList.add('flex');
        }

        function showSuccessState(currency) {
            document.getElementById('statusWaiting').style.display = 'none';
            document.getElementById('statusExpired').style.display = 'none';
            document.getElementById('statusSuccess').style.display = 'block';
            document.getElementById('countdownArea').style.display = 'none';
            document.getElementById('cancelQrBtn').style.display = 'none';
            document.getElementById('successCurrency').textContent =
                `Paid with ${currency === 'KHR' ? '🇰🇭 Khmer Riel (KHR)' : '🇺🇸 US Dollar (USD)'}`;
        }

        function showExpiredState() {
            document.getElementById('statusWaiting').style.display = 'none';
            document.getElementById('statusSuccess').style.display = 'none';
            document.getElementById('statusExpired').style.display = 'block';
            document.getElementById('countdownArea').style.display = 'none';
            document.getElementById('qrImageUSD').src = '';
            document.getElementById('qrImageKHR').src = '';
        }

        function closeQrPopup() {
            if (POS_STATE.paymentInterval) clearInterval(POS_STATE.paymentInterval);
            if (POS_STATE.countdownInterval) clearInterval(POS_STATE.countdownInterval);
            const bd = document.getElementById('qrBackdrop');
            bd.classList.add('hidden');
            bd.classList.remove('flex');
            document.getElementById('qrImageUSD').src = '';
            document.getElementById('qrImageKHR').src = '';
        }

        // ── ABA modal helpers ─────────────────────────────────
        function showAbaModal(data) {
            const qrSrc = data.qr_image ||
                `https://api.qrserver.com/v1/create-qr-code/?size=280x280&data=${encodeURIComponent(data.qr_string)}`;
            document.getElementById('abaQrImage').src = qrSrc;
            document.getElementById('abaAmount').textContent = '$' + parseFloat(POS_STATE.totalAmount).toFixed(2);
            document.getElementById('abaWaiting').style.display = 'flex';
            document.getElementById('abaSuccess').style.display = 'none';
            document.getElementById('abaTimerWrap').style.display = 'flex';
            document.getElementById('abaBackdrop').style.display = 'flex';
            startAbaTimer(3 * 60);
        }

        function startAbaTimer(totalSecs) {
            if (POS_STATE.abaTimerInterval) clearInterval(POS_STATE.abaTimerInterval);
            const circ = 43.98;
            let rem = totalSecs;
            const arc = document.getElementById('abaTimerArc');
            arc.style.transition = 'none';
            arc.style.strokeDashoffset = '0';

            POS_STATE.abaTimerInterval = setInterval(() => {
                rem--;
                const pct = rem / totalSecs;
                const mins = Math.floor(rem / 60);
                const secs = (rem % 60).toString().padStart(2, '0');
                document.getElementById('abaTimerText').textContent = `${mins}:${secs}`;
                arc.style.transition = 'stroke-dashoffset 1s linear, stroke 0.5s';
                arc.style.strokeDashoffset = String(circ * (1 - pct));
                arc.style.stroke = pct > 0.5 ? '#93C5FD' : pct > 0.25 ? '#F97316' : '#ef4444';
                document.getElementById('abaTimerText').style.color = pct > 0.25 ? '#93C5FD' : '#ef4444';
                if (rem <= 0) {
                    clearInterval(POS_STATE.abaTimerInterval);
                    clearInterval(POS_STATE.abaInterval);
                    showToast('ABA QR expired — please try again', 'error');
                    closeAbaModal();
                }
            }, 1000);
        }

        function closeAbaModal() {
            if (POS_STATE.abaInterval) clearInterval(POS_STATE.abaInterval);
            if (POS_STATE.abaTimerInterval) clearInterval(POS_STATE.abaTimerInterval);
            document.getElementById('abaBackdrop').style.display = 'none';
            document.getElementById('abaQrImage').src = '';
        }

        // ── ABA polling ───────────────────────────────────────
        function pollAbaPayment(tranId) {
            if (POS_STATE.abaInterval) clearInterval(POS_STATE.abaInterval);
            POS_STATE.abaInterval = setInterval(async () => {
                try {
                    const res = await fetch("{{ route('admin.pos.payway.verify') }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    });
                    const data = await res.json();
                    if (data.success) {
                        clearInterval(POS_STATE.abaInterval);
                        clearInterval(POS_STATE.abaTimerInterval);
                        document.getElementById('abaTimerWrap').style.display = 'none';
                        document.getElementById('abaWaiting').style.display = 'none';
                        document.getElementById('abaSuccess').style.display = 'block';
                        setTimeout(() => {
                            window.location.href = data.receipt_url;
                        }, 1500);
                    }
                } catch (e) {}
            }, 5000);
        }

        // ── Toast ─────────────────────────────────────────────
        function showToast(msg, type = 'info') {
            const t = document.createElement('div');
            t.style.cssText =
                `position:fixed;bottom:24px;right:24px;z-index:9999;padding:12px 20px;border-radius:10px;font-size:13px;font-weight:600;font-family:'DM Sans',sans-serif;background:${type==='error'?'rgba(204,0,1,0.9)':'rgba(22,163,74,0.9)'};color:#fff;box-shadow:0 4px 20px rgba(0,0,0,0.4);`;
            t.textContent = msg;
            document.body.appendChild(t);
            setTimeout(() => t.remove(), 3000);
        }
    </script>
@endpush
