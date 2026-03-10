<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\StockHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use KHQR\BakongKHQR;
use KHQR\Helpers\KHQRData;
use KHQR\Models\IndividualInfo;

class PosController extends ApiController
{
    // GET /api/pos/cart
    public function cart(Request $request)
    {
        $cart  = session('cart', []);
        $items = [];
        $total = 0;

        foreach ($cart as $id => $item) {
            $itemTotal = $item['price'] * $item['quantity'];
            $total    += $itemTotal;
            $items[]   = [
                'product_id' => (int) $id,
                'name'       => $item['name'],
                'price'      => (float) $item['price'],
                'quantity'   => $item['quantity'],
                'total'      => $itemTotal,
            ];
        }

        return response()->json(['data' => $items, 'total' => $total]);
    }

    // POST /api/pos/cart/add
    public function addToCart(Request $request)
    {
        $request->validate(['product_id' => 'required|exists:products,id']);

        $product = Product::findOrFail($request->product_id);

        if ($product->stock <= 0) {
            return response()->json(['error' => 'Out of stock'], 422);
        }

        $cart = session('cart', []);

        if (isset($cart[$product->id])) {
            if ($cart[$product->id]['quantity'] >= $product->stock) {
                return response()->json(['error' => 'Stock limit reached'], 422);
            }
            $cart[$product->id]['quantity']++;
        } else {
            $cart[$product->id] = [
                'name'     => $product->name,
                'price'    => $product->sell_price,
                'quantity' => 1,
            ];
        }

        session(['cart' => $cart]);
        return response()->json(['success' => true, 'cart_count' => count($cart)]);
    }

    // POST /api/pos/cart/update
    public function updateCart(Request $request)
    {
        $request->validate([
            'product_id' => 'required',
            'quantity'   => 'required|integer|min:1',
        ]);

        $cart = session('cart', []);
        if (isset($cart[$request->product_id])) {
            $cart[$request->product_id]['quantity'] = $request->quantity;
            session(['cart' => $cart]);
        }

        return response()->json(['success' => true]);
    }

    // POST /api/pos/cart/remove
    public function removeFromCart(Request $request)
    {
        $request->validate(['product_id' => 'required']);
        $cart = session('cart', []);
        unset($cart[$request->product_id]);
        session(['cart' => $cart]);
        return response()->json(['success' => true]);
    }

    // POST /api/pos/cart/clear
    public function clearCart()
    {
        session()->forget('cart');
        return response()->json(['success' => true]);
    }

    // POST /api/pos/checkout/cash
    public function checkoutCash(Request $request)
    {
        $request->validate(['paid_amount' => 'required|numeric|min:0']);

        $cart = session('cart', []);
        if (empty($cart)) {
            return response()->json(['error' => 'Cart is empty'], 422);
        }

        $total = 0;
        foreach ($cart as $id => $item) {
            $product = Product::find($id);
            if (!$product || $product->stock < $item['quantity']) {
                return response()->json(['error' => "Insufficient stock for: {$item['name']}"], 422);
            }
            $total += $item['price'] * $item['quantity'];
        }

        if ((float) $request->paid_amount < $total) {
            return response()->json(['error' => 'Insufficient payment amount'], 422);
        }

        DB::beginTransaction();
        try {
            $sale = $this->createSale('cash', (float) $request->paid_amount, $total, $cart);
            DB::commit();
            session()->forget('cart');
            $this->sendTelegramNotification($sale, 'cash');

            return response()->json([
                'success'    => true,
                'sale_id'    => $sale->id,
                'invoice_no' => $sale->invoice_no,
                'total'      => $total,
                'paid'       => (float) $request->paid_amount,
                'change'     => round((float) $request->paid_amount - $total, 2),
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    // POST /api/pos/checkout/khqr  — generates both USD + KHR QR codes
    public function generateKhqr(Request $request)
    {
        try {
            $cart = session('cart', []);
            if (empty($cart)) {
                return response()->json(['error' => 'Cart is empty'], 422);
            }

            $total        = array_reduce($cart, fn($c, $i) => $c + $i['price'] * $i['quantity'], 0);
            $amountUSD    = round($total, 2);
            $exchangeRate = (int) env('KHR_EXCHANGE_RATE', 4100);
            $amountKHR    = (int) round($amountUSD * $exchangeRate);
            $expiresInMs  = strval((int) floor(microtime(true) * 1000) + (5 * 60 * 1000));

            $infoUSD = new IndividualInfo(
                bakongAccountID:     env('BAKONG_ACCOUNT_ID'),
                merchantName:        env('BAKONG_MERCHANT_NAME', 'My Store'),
                merchantCity:        env('BAKONG_MERCHANT_CITY', 'Phnom Penh'),
                currency:            KHQRData::CURRENCY_USD,
                amount:              $amountUSD,
                expirationTimestamp: $expiresInMs
            );
            $resultUSD = BakongKHQR::generateIndividual($infoUSD);

            $infoKHR = new IndividualInfo(
                bakongAccountID:     env('BAKONG_ACCOUNT_ID'),
                merchantName:        env('BAKONG_MERCHANT_NAME', 'My Store'),
                merchantCity:        env('BAKONG_MERCHANT_CITY', 'Phnom Penh'),
                currency:            KHQRData::CURRENCY_KHR,
                amount:              $amountKHR,
                expirationTimestamp: $expiresInMs
            );
            $resultKHR = BakongKHQR::generateIndividual($infoKHR);

            session([
                'khqr_md5_usd' => $resultUSD->data['md5'],
                'khqr_md5_khr' => $resultKHR->data['md5'],
                'khqr_amount'  => $amountUSD,
                'khqr_paid'    => false,
            ]);

            return response()->json([
                'usd' => [
                    'qr_string' => $resultUSD->data['qr'],
                    'md5'       => $resultUSD->data['md5'],
                    'amount'    => $amountUSD,
                ],
                'khr' => [
                    'qr_string' => $resultKHR->data['qr'],
                    'md5'       => $resultKHR->data['md5'],
                    'amount'    => $amountKHR,
                ],
                'expires_at'   => time() + 300,
                'exchange_rate'=> $exchangeRate,
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'QR generation failed: ' . $e->getMessage()], 500);
        }
    }

    // POST /api/pos/checkout/khqr/verify
    public function verifyKhqr(Request $request)
    {
        $request->validate([
            'md5'      => 'required|string',
            'currency' => 'required|in:usd,khr',
        ]);

        if (session('khqr_paid') === true) {
            return response()->json(['status' => 'ALREADY_PAID']);
        }

        try {
            $response = Http::timeout(10)
                ->withoutVerifying()
                ->withHeaders(['Authorization' => 'Bearer ' . env('BAKONG_TOKEN')])
                ->post('https://api-bakong.nbc.gov.kh/v1/check_transaction_by_md5', [
                    'md5' => $request->md5
                ]);

            if ($response->status() === 401) {
                return response()->json(['error' => 'Bakong token expired'], 401);
            }

            $data = $response->json();

            if (isset($data['responseCode']) && $data['responseCode'] === 0) {
                if (session('khqr_paid') === true) {
                    return response()->json(['status' => 'ALREADY_PAID']);
                }

                session(['khqr_paid' => true]);

                $txn        = $data['data'] ?? [];
                $paidAmount = $txn['amount'] ?? session('khqr_amount');

                if ($request->currency === 'khr') {
                    $paidAmount = round($paidAmount / (int) env('KHR_EXCHANGE_RATE', 4100), 2);
                }

                $cart   = session('cart', []);
                $total  = array_reduce($cart, fn($c, $i) => $c + $i['price'] * $i['quantity'], 0);
                $method = 'khqr_' . $request->currency;

                DB::beginTransaction();
                try {
                    $sale = $this->createSale($method, $paidAmount, $total, $cart, $txn['hash'] ?? null);
                    DB::commit();
                    session()->forget(['cart', 'khqr_md5_usd', 'khqr_md5_khr', 'khqr_amount', 'khqr_paid']);
                    $this->sendTelegramNotification($sale, $method);

                    return response()->json([
                        'success'    => true,
                        'currency'   => strtoupper($request->currency),
                        'sale_id'    => $sale->id,
                        'invoice_no' => $sale->invoice_no,
                        'total'      => $total,
                    ]);
                } catch (\Exception $e) {
                    DB::rollBack();
                    return response()->json(['error' => $e->getMessage()], 500);
                }
            }

            return response()->json(['status' => 'PENDING']);

        } catch (\Exception $e) {
            Log::error('API verifyKhqr error', ['msg' => $e->getMessage()]);
            return response()->json(['status' => 'PENDING']);
        }
    }

    // ── Helpers ───────────────────────────────────────────────────
    private function createSale(string $method, float $paid, float $total, array $cart, ?string $txnId = null): Sale
    {
        $lastSale  = Sale::latest()->first();
        $nextId    = $lastSale ? $lastSale->id + 1 : 1;
        $invoiceNo = 'INV-' . date('Ymd') . '-' . str_pad($nextId, 4, '0', STR_PAD_LEFT);

        $sale = Sale::create([
            'invoice_no'     => $invoiceNo,
            'total_amount'   => $total,
            'paid_amount'    => $paid,
            'change_amount'  => max(0, $paid - $total),
            'payment_method' => $method,
            'bakong_txn_id'  => $txnId,
            'user_id'        => auth('sanctum')->id(),
        ]);

        foreach ($cart as $productId => $item) {
            SaleItem::create([
                'sale_id'    => $sale->id,
                'product_id' => $productId,
                'quantity'   => $item['quantity'],
                'price'      => $item['price'],
                'total'      => $item['price'] * $item['quantity'],
            ]);
            Product::where('id', $productId)->decrement('stock', $item['quantity']);
            StockHistory::create([
                'product_id' => $productId,
                'quantity'   => $item['quantity'],
                'type'       => 'out',
                'note'       => 'Sold via API (' . strtoupper($method) . ')',
            ]);
        }

        return $sale;
    }

    private function sendTelegramNotification(Sale $sale, string $method): void
    {
        try {
            $label = match(true) {
                $method === 'cash'          => 'Cash 💵',
                $method === 'khqr_usd'      => 'KHQR USD 🇺🇸✅',
                $method === 'khqr_khr'      => 'KHQR KHR 🇰🇭✅',
                default                     => strtoupper($method),
            };
            Http::withoutVerifying()->post(
                "https://api.telegram.org/bot" . env('TELEGRAM_BOT_TOKEN') . "/sendMessage",
                [
                    'chat_id'    => env('TELEGRAM_CHAT_ID'),
                    'text'       => "🧾 *New Sale!*\nInvoice: `{$sale->invoice_no}`\nTotal: $" . number_format($sale->total_amount, 2) . "\nPayment: {$label}\nTime: " . now()->setTimezone('Asia/Phnom_Penh')->format('d M Y, h:i A'),
                    'parse_mode' => 'Markdown',
                ]
            );
        } catch (\Exception $e) {
            Log::warning('Telegram failed', ['msg' => $e->getMessage()]);
        }
    }

    /**
     * GET /api/pos/products
     * Lightweight product list optimized for POS terminal
     */
    public function products(Request $request)
    {
        $query = Product::with('category:id,name')
            ->where('stock', '>', 0) // only in-stock products
            ->select('id', 'category_id', 'name', 'barcode', 'sell_price', 'stock', 'image');

        // Search by name or barcode
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('barcode', 'like', "%{$search}%");
            });
        }

        // Filter by category
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        $products = $query->orderBy('name')->get();

        return response()->json([
            'success' => true,
            'message' => 'POS products loaded.',
            'data'    => $products,
            'meta'    => [
                'total' => $products->count(),
            ],
        ]);
    }
}