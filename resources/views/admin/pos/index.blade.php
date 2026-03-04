@extends('layouts.app')
@section('title', 'POS System')

@section('content')
    <div class="h-[85vh] flex gap-6">

        {{-- LEFT SIDE - PRODUCTS --}}
        <div class="w-2/3 bg-white shadow rounded-lg p-4 flex flex-col">
            <input type="text" id="search" placeholder="Scan barcode or search product..."
                class="w-full border rounded px-4 py-2 mb-4 focus:ring-2 focus:ring-blue-500" autofocus>

            <div id="product-grid" class="grid grid-cols-4 gap-4 overflow-y-auto">
                @foreach ($products as $product)
                    <div class="border rounded-lg p-3 hover:shadow">
                        <img src="{{ $product->image_url }}" class="h-40 w-full object-cover rounded mb-2">
                        <h3 class="font-semibold text-sm">{{ $product->name }}</h3>
                        <p class="text-green-600 font-bold">${{ number_format($product->sell_price, 2) }}</p>
                        <p class="text-xs text-gray-500 mb-2">Stock: {{ $product->stock }}</p>
                        <button onclick="addToCart({{ $product->id }})"
                            class="w-full bg-blue-600 text-white py-1 rounded text-sm hover:bg-blue-700">Add</button>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- RIGHT SIDE - CART --}}
        <div class="w-1/3 bg-white shadow rounded-lg p-4 flex flex-col">
            <h2 class="text-lg font-bold mb-4">Cart</h2>

            <div id="cart-items" class="flex-1 border rounded p-3 overflow-y-auto">
                <p class="text-gray-500 text-sm">Cart is empty.</p>
            </div>

            <div class="mt-4 border-t pt-4">
                <div class="flex justify-between mb-2">
                    <span>Total:</span>
                    <span id="totalAmount" class="font-bold text-xl">$0.00</span>
                </div>

                <div class="mb-3">
                    <label class="text-sm font-medium">Payment Method</label>
                    <select id="paymentMethod" class="w-full border rounded px-3 py-2">
                        <option value="cash">Cash</option>
                        <option value="khqr">KHQR Bakong (USD + KHR)</option>
                    </select>
                </div>

                <div id="cashSection" class="mb-2">
                    <label class="text-sm font-medium">Cash Received</label>
                    <input type="number" id="cashInput" class="w-full border rounded px-3 py-2" placeholder="0.00">
                </div>

                <div id="changeSection" class="flex justify-between mb-4 font-bold text-lg text-blue-600">
                    <span>Change:</span>
                    <span id="changeAmount">$0.00</span>
                </div>

                <button id="checkoutBtn" onclick="checkout()"
                    class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded w-full mt-3 font-bold">
                    Checkout
                </button>
            </div>
        </div>
    </div>

    {{-- ============================================================ --}}
    {{-- DUAL KHQR POPUP                                               --}}
    {{-- ============================================================ --}}
    <div id="qrPopup" class="hidden fixed inset-0 bg-black bg-opacity-75 items-center justify-center z-50 p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl">

            {{-- Header --}}
            <div class="bg-gradient-to-r from-blue-600 to-blue-800 rounded-t-2xl px-6 py-4 text-white text-center">
                <div class="flex items-center justify-center gap-2 mb-1">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path
                            d="M4 4a2 2 0 00-2 2v1h16V6a2 2 0 00-2-2H4zM2 9v7a2 2 0 002 2h12a2 2 0 002-2V9H2zm3 2h2a1 1 0 010 2H5a1 1 0 010-2zm0 3h2a1 1 0 010 2H5a1 1 0 010-2z" />
                    </svg>
                    <h3 class="text-lg font-bold">Scan to Pay with KHQR</h3>
                </div>
                <p class="text-blue-100 text-sm">Choose either currency — scan whichever your bank supports</p>
            </div>

            <div class="p-6">
                {{-- Two QR codes side by side --}}
                <div class="grid grid-cols-2 gap-4 mb-5">

                    {{-- USD QR --}}
                    <div class="border-2 border-green-200 rounded-xl p-4 text-center bg-green-50">
                        <div class="flex items-center justify-center gap-1 mb-2">
                            <span class="text-lg">🇺🇸</span>
                            <span class="font-bold text-green-700 text-sm">US Dollar</span>
                        </div>
                        <p id="usdAmount" class="text-2xl font-bold text-green-600 mb-3"></p>
                        <div class="bg-white rounded-lg p-2 border border-green-100">
                            <img id="qrImageUSD" src="" alt="USD QR" class="w-full h-auto mx-auto">
                        </div>
                        <p class="text-xs text-gray-400 mt-2">ABA • Wing • ACLEDA • all banks</p>
                    </div>

                    {{-- KHR QR --}}
                    <div class="border-2 border-red-200 rounded-xl p-4 text-center bg-red-50">
                        <div class="flex items-center justify-center gap-1 mb-2">
                            <span class="text-lg">🇰🇭</span>
                            <span class="font-bold text-red-700 text-sm">Khmer Riel</span>
                        </div>
                        <p id="khrAmount" class="text-2xl font-bold text-red-600 mb-3"></p>
                        <div class="bg-white rounded-lg p-2 border border-red-100">
                            <img id="qrImageKHR" src="" alt="KHR QR" class="w-full h-auto mx-auto">
                        </div>
                        <p class="text-xs text-gray-400 mt-2">ABA • Wing • ACLEDA • all banks</p>
                    </div>
                </div>

                {{-- Status area --}}
                <div class="text-center mb-4">
                    <div id="statusWaiting">
                        <p class="text-blue-600 font-medium animate-pulse">⏳ Waiting for payment on either QR...</p>
                    </div>
                    <div id="statusSuccess" class="hidden">
                        <div class="bg-green-50 border border-green-200 rounded-xl p-4">
                            <p class="text-green-600 font-bold text-xl">✅ Payment Received!</p>
                            <p id="successCurrency" class="text-gray-500 text-sm mt-1"></p>
                            <p class="text-gray-400 text-xs mt-1">Redirecting to receipt...</p>
                        </div>
                    </div>
                    <div id="statusExpired" class="hidden">
                        <div class="bg-red-50 border border-red-200 rounded-xl p-4">
                            <p class="text-red-500 font-bold">❌ QR Codes Expired</p>
                            <p class="text-gray-500 text-sm mt-1">Click cancel and try again.</p>
                        </div>
                    </div>
                </div>

                {{-- Countdown --}}
                <div id="countdownArea" class="mb-4">
                    <div class="flex items-center justify-between text-sm text-gray-500 mb-1">
                        <span>QR expires in</span>
                        <span id="countdownTimer" class="font-mono font-bold text-orange-500">5:00</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div id="countdownBar" class="h-2 rounded-full transition-all duration-1000"
                            style="width:100%; background:#22c55e;"></div>
                    </div>
                </div>

                {{-- Exchange rate note --}}
                <p id="exchangeNote" class="text-center text-xs text-gray-400 mb-4"></p>

                {{-- Cancel --}}
                <button id="cancelQrBtn" onclick="closeQrPopup()"
                    class="w-full bg-red-500 hover:bg-red-600 text-white py-2 rounded-full font-medium transition">
                    Cancel Transaction
                </button>
            </div>
        </div>
    </div>

    {{-- ============================================================ --}}
    {{-- SCRIPTS                                                        --}}
    {{-- ============================================================ --}}
    <script>
        const POS_STATE = {
            paymentInterval: null,
            countdownInterval: null,
            totalAmount: 0,
        };

        document.addEventListener("DOMContentLoaded", function() {
            const paymentMethod = document.getElementById('paymentMethod');
            const cashSection = document.getElementById('cashSection');
            const changeSection = document.getElementById('changeSection');
            const cashInput = document.getElementById('cashInput');
            const searchInput = document.getElementById('search');
            const productGrid = document.getElementById('product-grid');

            paymentMethod.addEventListener('change', function() {
                if (this.value === 'khqr') {
                    cashSection.style.display = 'none';
                    changeSection.style.display = 'none';
                } else {
                    cashSection.style.display = 'block';
                    changeSection.style.display = 'flex';
                }
            });

            let timeout = null;
            searchInput.addEventListener('keyup', function() {
                clearTimeout(timeout);
                timeout = setTimeout(() => {
                    fetch(`{{ route('admin.pos.search') }}?search=${this.value}`)
                        .then(r => r.json())
                        .then(products => {
                            productGrid.innerHTML = '';
                            if (!products.length) {
                                productGrid.innerHTML =
                                    "<p class='col-span-4 text-center py-10 text-gray-400'>No products found.</p>";
                                return;
                            }
                            products.forEach(p => {
                                let img = p.image ? `/storage/${p.image}` :
                                    '/images/no-image.png';
                                productGrid.innerHTML += `
                                    <div class="border rounded-lg p-3 hover:shadow transition">
                                        <img src="${img}" class="h-40 w-full object-cover rounded mb-2">
                                        <h3 class="font-semibold text-sm truncate">${p.name}</h3>
                                        <p class="text-green-600 font-bold">$${parseFloat(p.sell_price).toFixed(2)}</p>
                                        <p class="text-xs text-gray-500 mb-2">Stock: ${p.stock}</p>
                                        <button onclick="addToCart(${p.id})" class="w-full bg-blue-600 text-white py-1 rounded text-sm">Add</button>
                                    </div>`;
                            });
                        });
                }, 300);
            });

            cashInput.addEventListener('keyup', calculateChange);
            loadCart();
        });

        // =====================
        // CART
        // =====================
        function addToCart(productId) {
            fetch("{{ route('admin.pos.add') }}", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": "{{ csrf_token() }}"
                    },
                    body: JSON.stringify({
                        product_id: productId
                    })
                })
                .then(r => r.json())
                .then(data => {
                    if (data.error) {
                        alert(data.error);
                        return;
                    }
                    loadCart();
                    document.getElementById('search').value = '';
                    document.getElementById('search').focus();
                });
        }

        function loadCart() {
            fetch("{{ url('/admin/pos-cart-data') }}")
                .then(r => r.json())
                .then(data => {
                    const cartDiv = document.getElementById('cart-items');
                    const totalAmountEl = document.getElementById('totalAmount');
                    const checkoutBtn = document.getElementById('checkoutBtn');
                    const cashInput = document.getElementById('cashInput');

                    cartDiv.innerHTML = '';
                    let subtotal = 0;

                    if (!Object.keys(data).length) {
                        cartDiv.innerHTML =
                            '<p class="text-gray-500 text-sm italic text-center py-10">Cart is empty.</p>';
                    }

                    for (const id in data) {
                        const item = data[id];
                        const itemTotal = item.price * item.quantity;
                        subtotal += itemTotal;
                        cartDiv.innerHTML += `
                            <div class="mb-3 border-b pb-2">
                                <div class="flex justify-between">
                                    <strong class="text-sm">${item.name}</strong>
                                    <button onclick="removeItem(${id})" class="text-red-500 font-bold hover:text-red-700">×</button>
                                </div>
                                <div class="flex justify-between items-center mt-1">
                                    <div class="flex items-center border rounded">
                                        <button onclick="updateQty(${id}, ${item.quantity - 1})" class="px-2 bg-gray-100 hover:bg-gray-200">-</button>
                                        <span class="px-3 text-sm">${item.quantity}</span>
                                        <button onclick="updateQty(${id}, ${item.quantity + 1})" class="px-2 bg-gray-100 hover:bg-gray-200">+</button>
                                    </div>
                                    <span class="font-semibold">$${itemTotal.toFixed(2)}</span>
                                </div>
                            </div>`;
                    }

                    POS_STATE.totalAmount = subtotal;
                    totalAmountEl.innerText = '$' + subtotal.toFixed(2);
                    calculateChange();

                    const isEmpty = !Object.keys(data).length;
                    checkoutBtn.disabled = isEmpty;
                    cashInput.disabled = isEmpty;
                    checkoutBtn.classList.toggle('opacity-50', isEmpty);
                    checkoutBtn.classList.toggle('cursor-not-allowed', isEmpty);
                    cashInput.classList.toggle('opacity-50', isEmpty);
                });
        }

        function updateQty(productId, qty) {
            if (qty <= 0) return removeItem(productId);
            fetch("{{ route('admin.pos.update') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                },
                body: JSON.stringify({
                    product_id: productId,
                    quantity: qty
                })
            }).then(() => loadCart());
        }

        function removeItem(productId) {
            fetch("{{ route('admin.pos.remove') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                },
                body: JSON.stringify({
                    product_id: productId
                })
            }).then(() => loadCart());
        }

        function calculateChange() {
            const cash = parseFloat(document.getElementById('cashInput').value) || 0;
            const change = cash - POS_STATE.totalAmount;
            document.getElementById('changeAmount').innerText = '$' + (change > 0 ? change.toFixed(2) : '0.00');
        }

        // =====================
        // CHECKOUT
        // =====================
        function checkout() {
            const method = document.getElementById('paymentMethod').value;

            if (method === 'cash') {
                const cash = parseFloat(document.getElementById('cashInput').value) || 0;
                if (cash < POS_STATE.totalAmount) {
                    alert("Insufficient cash!");
                    return;
                }
                fetch("{{ route('admin.pos.checkout') }}", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                            "X-CSRF-TOKEN": "{{ csrf_token() }}"
                        },
                        body: JSON.stringify({
                            paid_amount: cash
                        })
                    })
                    .then(r => r.json())
                    .then(data => {
                        if (data.error) {
                            alert(data.error);
                            return;
                        }
                        window.open(`/admin/pos/receipt/${data.sale_id}`, '_blank');
                        location.reload();
                    });
                return;
            }

            if (method === 'khqr') {
                if (POS_STATE.totalAmount <= 0) {
                    alert("Cart is empty!");
                    return;
                }

                const btn = document.getElementById('checkoutBtn');
                btn.disabled = true;
                btn.innerText = 'Generating QR...';

                fetch("{{ route('admin.pos.generateKhqr') }}", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                            "X-CSRF-TOKEN": "{{ csrf_token() }}"
                        },
                        body: JSON.stringify({
                            amount: POS_STATE.totalAmount
                        })
                    })
                    .then(async res => {
                        const data = await res.json();
                        if (!res.ok || data.error) throw new Error(data.error || 'QR generation failed');
                        return data;
                    })
                    .then(data => {
                        showQrPopup(data);
                        startCountdown(data.expires_at);
                        // Poll BOTH QRs simultaneously
                        pollBothPayments(data.usd.md5, data.khr.md5, data.expires_at);
                    })
                    .catch(err => alert('Could not generate QR: ' + err.message))
                    .finally(() => {
                        btn.disabled = false;
                        btn.innerText = 'Checkout';
                    });
            }
        }

        // =====================
        // POLL BOTH QRs
        // =====================
        function pollBothPayments(md5USD, md5KHR, expiresAt) {
            if (POS_STATE.paymentInterval) clearInterval(POS_STATE.paymentInterval);

            // Track if already paid to prevent double processing
            let paid = false;

            POS_STATE.paymentInterval = setInterval(() => {
                if (paid) return;

                if (Math.floor(Date.now() / 1000) >= expiresAt) {
                    clearInterval(POS_STATE.paymentInterval);
                    clearInterval(POS_STATE.countdownInterval);
                    showExpiredState();
                    return;
                }

                // Poll USD
                pollSinglePayment(md5USD, 'usd', () => {
                    paid = true;
                });

                // Poll KHR (500ms offset so requests don't collide)
                setTimeout(() => {
                    if (!paid) pollSinglePayment(md5KHR, 'khr', () => {
                        paid = true;
                    });
                }, 500);

            }, 3000);
        }

        function pollSinglePayment(md5, currency, onSuccess) {
            fetch("{{ route('admin.pos.verifyKhqr') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': "{{ csrf_token() }}"
                    },
                    body: JSON.stringify({
                        md5: md5,
                        currency: currency
                    })
                })
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        onSuccess();
                        clearInterval(POS_STATE.paymentInterval);
                        clearInterval(POS_STATE.countdownInterval);
                        showSuccessState(data.currency);
                        setTimeout(() => {
                            window.location.href = data.receipt_url || `/admin/pos/receipt/${data.sale_id}`;
                        }, 1500);
                    }
                })
                .catch(() => {
                    /* keep polling on network hiccup */ });
        }

        // =====================
        // COUNTDOWN
        // =====================
        function startCountdown(expiresAt) {
            if (POS_STATE.countdownInterval) clearInterval(POS_STATE.countdownInterval);

            const totalSeconds = expiresAt - Math.floor(Date.now() / 1000);
            const bar = document.getElementById('countdownBar');
            const timerEl = document.getElementById('countdownTimer');

            POS_STATE.countdownInterval = setInterval(() => {
                const remaining = expiresAt - Math.floor(Date.now() / 1000);

                if (remaining <= 0) {
                    clearInterval(POS_STATE.countdownInterval);
                    timerEl.innerText = '0:00';
                    bar.style.width = '0%';
                    bar.style.background = '#ef4444';
                    return;
                }

                const mins = Math.floor(remaining / 60);
                const secs = remaining % 60;
                timerEl.innerText = `${mins}:${secs.toString().padStart(2, '0')}`;

                const pct = (remaining / totalSeconds) * 100;
                bar.style.width = pct + '%';

                if (pct > 50) bar.style.background = '#22c55e';
                else if (pct > 25) bar.style.background = '#f97316';
                else bar.style.background = '#ef4444';

                if (remaining <= 30) {
                    timerEl.classList.add('text-red-500', 'animate-pulse');
                    timerEl.classList.remove('text-orange-500');
                }
            }, 1000);
        }

        // =====================
        // POPUP HELPERS
        // =====================
        function showQrPopup(data) {
            // Set QR images and amounts
            document.getElementById('qrImageUSD').src = data.usd.qr_image;
            document.getElementById('qrImageKHR').src = data.khr.qr_image;
            document.getElementById('usdAmount').innerText = data.usd.label;
            document.getElementById('khrAmount').innerText = data.khr.label;
            document.getElementById('exchangeNote').innerText =
                `Exchange rate: $1 = ${(data.khr.amount / data.usd.amount).toLocaleString()} ៛`;

            // Reset states
            document.getElementById('statusWaiting').classList.remove('hidden');
            document.getElementById('statusSuccess').classList.add('hidden');
            document.getElementById('statusExpired').classList.add('hidden');
            document.getElementById('countdownArea').classList.remove('hidden');
            document.getElementById('cancelQrBtn').classList.remove('hidden');
            document.getElementById('countdownTimer').classList.remove('text-red-500', 'animate-pulse');
            document.getElementById('countdownTimer').classList.add('text-orange-500');
            document.getElementById('countdownBar').style.width = '100%';
            document.getElementById('countdownBar').style.background = '#22c55e';

            const popup = document.getElementById('qrPopup');
            popup.classList.remove('hidden');
            popup.classList.add('flex');
        }

        function showSuccessState(currency) {
            document.getElementById('statusWaiting').classList.add('hidden');
            document.getElementById('statusExpired').classList.add('hidden');
            document.getElementById('statusSuccess').classList.remove('hidden');
            document.getElementById('countdownArea').classList.add('hidden');
            document.getElementById('cancelQrBtn').classList.add('hidden');
            document.getElementById('successCurrency').innerText =
                `Paid with ${currency === 'KHR' ? '🇰🇭 Khmer Riel (KHR)' : '🇺🇸 US Dollar (USD)'}`;
        }

        function showExpiredState() {
            document.getElementById('statusWaiting').classList.add('hidden');
            document.getElementById('statusSuccess').classList.add('hidden');
            document.getElementById('statusExpired').classList.remove('hidden');
            document.getElementById('countdownArea').classList.add('hidden');
            document.getElementById('qrImageUSD').src = '';
            document.getElementById('qrImageKHR').src = '';
        }

        function closeQrPopup() {
            if (POS_STATE.paymentInterval) clearInterval(POS_STATE.paymentInterval);
            if (POS_STATE.countdownInterval) clearInterval(POS_STATE.countdownInterval);
            const popup = document.getElementById('qrPopup');
            popup.classList.add('hidden');
            popup.classList.remove('flex');
            document.getElementById('qrImageUSD').src = '';
            document.getElementById('qrImageKHR').src = '';
        }
    </script>
@endsection
