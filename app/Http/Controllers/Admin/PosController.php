<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\StockHistory;
use App\Models\Sale;
use App\Models\SaleItem;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use KHQR\BakongKHQR;
use KHQR\Helpers\KHQRData;
use KHQR\Models\IndividualInfo;

class PosController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->search;
        $products = Product::query()
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('barcode', 'like', "%{$search}%");
                });
            })
            ->where('stock', '>', 0)
            ->latest()
            ->get();

        return view('admin.pos.index', compact('products'));
    }

    public function search(Request $request)
    {
        $search = $request->search;
        $products = Product::query()
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('barcode', 'like', "%{$search}%");
                });
            })
            ->where('stock', '>', 0)
            ->latest()
            ->get();

        return response()->json($products);
    }

    public function addToCart(Request $request)
    {
        $product = Product::findOrFail($request->product_id);

        if ($product->stock <= 0) {
            return response()->json(['error' => 'Out of stock']);
        }

        $cart = session()->get('cart', []);

        if (isset($cart[$product->id])) {
            if ($cart[$product->id]['quantity'] >= $product->stock) {
                return response()->json(['error' => 'Stock limit reached']);
            }
            $cart[$product->id]['quantity']++;
        } else {
            $cart[$product->id] = [
                "name"     => $product->name,
                "price"    => $product->sell_price,
                "quantity" => 1,
            ];
        }

        session()->put('cart', $cart);
        return response()->json(['success' => true]);
    }

    public function updateCart(Request $request)
    {
        $cart = session()->get('cart', []);
        if (isset($cart[$request->product_id])) {
            $cart[$request->product_id]['quantity'] = $request->quantity;
            session()->put('cart', $cart);
        }
        return response()->json(['success' => true]);
    }

    public function removeFromCart(Request $request)
    {
        $cart = session()->get('cart', []);
        unset($cart[$request->product_id]);
        session()->put('cart', $cart);
        return response()->json(['success' => true]);
    }

    public function checkout(Request $request)
    {
        $request->validate(['paid_amount' => 'required|numeric|min:0']);

        $cart = session()->get('cart', []);
        if (empty($cart)) {
            return response()->json(['error' => 'Cart is empty']);
        }

        $totalAmount = 0;
        foreach ($cart as $id => $item) {
            $product = Product::find($id);
            if (!$product || $product->stock < $item['quantity']) {
                return response()->json(['error' => "Stock limit reached for {$item['name']}"]);
            }
            $totalAmount += $item['price'] * $item['quantity'];
        }

        $paidAmount = (float) $request->paid_amount;
        if ($paidAmount < $totalAmount) {
            return response()->json(['error' => 'Insufficient cash payment']);
        }

        DB::beginTransaction();
        try {
            $lastSale  = Sale::latest()->first();
            $nextId    = $lastSale ? $lastSale->id + 1 : 1;
            $invoiceNo = 'INV-' . date('Ymd') . '-' . str_pad($nextId, 4, '0', STR_PAD_LEFT);

            $sale = Sale::create([
                'invoice_no'     => $invoiceNo,
                'total_amount'   => $totalAmount,
                'paid_amount'    => $paidAmount,
                'change_amount'  => $paidAmount - $totalAmount,
                'payment_method' => 'cash',
                'user_id'        => auth()->id(),
            ]);

            foreach ($cart as $id => $item) {
                SaleItem::create([
                    'sale_id'    => $sale->id,
                    'product_id' => $id,
                    'quantity'   => $item['quantity'],
                    'price'      => $item['price'],
                    'total'      => $item['price'] * $item['quantity'],
                ]);
                Product::find($id)->decrement('stock', $item['quantity']);
                StockHistory::create([
                    'product_id' => $id,
                    'quantity'   => $item['quantity'],
                    'type'       => 'out',
                    'note'       => 'Sold via POS (Cash)'
                ]);
            }

            DB::commit();
            session()->forget('cart');
            $this->sendTelegramNotification($sale, 'cash');

            return response()->json(['success' => true, 'sale_id' => $sale->id]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Database error: ' . $e->getMessage()]);
        }
    }

    public function receipt(Sale $sale)
    {
        $sale->load('items.product');
        return view('admin.pos.receipt', compact('sale'));
    }

    // =========================================================
    // GENERATE BOTH QR CODES (USD + KHR) at once
    // =========================================================
    public function generateKhqr(Request $request)
    {
        try {
            $cart = session()->get('cart', []);
            if (empty($cart)) {
                return response()->json(['error' => 'Cart is empty'], 422);
            }

            $totalAmount = 0;
            foreach ($cart as $item) {
                $totalAmount += $item['price'] * $item['quantity'];
            }
            $amountUSD = round($totalAmount, 2);

            $exchangeRate = (int) env('KHR_EXCHANGE_RATE', 4100);
            $amountKHR    = (int) round($amountUSD * $exchangeRate);

            $expiresInMs = strval((int) floor(microtime(true) * 1000) + (5 * 60 * 1000));

            // USD QR
            $infoUSD = new IndividualInfo(
                bakongAccountID:     env('BAKONG_ACCOUNT_ID'),
                merchantName:        env('BAKONG_MERCHANT_NAME', 'My Store'),
                merchantCity:        env('BAKONG_MERCHANT_CITY', 'Phnom Penh'),
                currency:            KHQRData::CURRENCY_USD,
                amount:              $amountUSD,
                expirationTimestamp: $expiresInMs
            );
            $resultUSD = BakongKHQR::generateIndividual($infoUSD);
            $qrUSD     = $resultUSD->data['qr'];
            $md5USD    = $resultUSD->data['md5'];

            // KHR QR
            $infoKHR = new IndividualInfo(
                bakongAccountID:     env('BAKONG_ACCOUNT_ID'),
                merchantName:        env('BAKONG_MERCHANT_NAME', 'My Store'),
                merchantCity:        env('BAKONG_MERCHANT_CITY', 'Phnom Penh'),
                currency:            KHQRData::CURRENCY_KHR,
                amount:              $amountKHR,
                expirationTimestamp: $expiresInMs
            );
            $resultKHR = BakongKHQR::generateIndividual($infoKHR);
            $qrKHR     = $resultKHR->data['qr'];
            $md5KHR    = $resultKHR->data['md5'];

            $imgUSD = $this->qrToImage($qrUSD);
            $imgKHR = $this->qrToImage($qrKHR);

            session([
                'khqr_md5_usd'  => $md5USD,
                'khqr_md5_khr'  => $md5KHR,
                'khqr_amount'   => $amountUSD,
                'khqr_paid'     => false,   // ← guard flag
                'khqr_sale_id'  => null,
            ]);

            return response()->json([
                'usd' => [
                    'qr_image' => $imgUSD,
                    'md5'      => $md5USD,
                    'amount'   => $amountUSD,
                    'label'    => '$' . number_format($amountUSD, 2),
                ],
                'khr' => [
                    'qr_image' => $imgKHR,
                    'md5'      => $md5KHR,
                    'amount'   => $amountKHR,
                    'label'    => '៛' . number_format($amountKHR),
                ],
                'expires_at' => time() + 300,
            ]);

        } catch (\Exception $e) {
            Log::error('generateKhqr error', ['msg' => $e->getMessage()]);
            return response()->json(['error' => 'QR generation failed: ' . $e->getMessage()], 500);
        }
    }

    // =========================================================
    // VERIFY — with double-processing guard
    // =========================================================
    public function verifyKhqr(Request $request)
    {
        try {
            $md5      = $request->input('md5');
            $currency = $request->input('currency', 'usd');

            if (!$md5) {
                return response()->json(['error' => 'Missing md5'], 422);
            }

            // ✅ GUARD: If the other QR already completed this sale, return
            // the same success response so the second poller stops cleanly
            if (session('khqr_paid')) {
                $saleId = session('khqr_sale_id');
                return response()->json([
                    'success'     => true,
                    'sale_id'     => $saleId,
                    'currency'    => strtoupper($currency),
                    'receipt_url' => "/admin/pos/receipt/{$saleId}",
                    'already_paid'=> true,
                ]);
            }

            $token = env('BAKONG_TOKEN');
            if (!$token) {
                return response()->json(['error' => 'BAKONG_TOKEN not set'], 500);
            }

            $response = Http::timeout(10)
                ->withoutVerifying()
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $token,
                    'Content-Type'  => 'application/json',
                ])
                ->post('https://api-bakong.nbc.gov.kh/v1/check_transaction_by_md5', [
                    'md5' => $md5
                ]);

            Log::info('Bakong verify', [
                'currency'    => $currency,
                'http_status' => $response->status(),
                'body'        => $response->body(),
            ]);

            if ($response->status() === 401) {
                return response()->json(['error' => 'Bakong token expired'], 401);
            }

            if ($response->failed()) {
                return response()->json(['status' => 'PENDING']);
            }

            $data = $response->json();

            if (isset($data['responseCode']) && $data['responseCode'] === 0) {

                // ✅ GUARD: Double-check session hasn't been taken by a
                // concurrent request that arrived at the same moment
                if (session('khqr_paid')) {
                    $saleId = session('khqr_sale_id');
                    return response()->json([
                        'success'      => true,
                        'sale_id'      => $saleId,
                        'currency'     => strtoupper($currency),
                        'receipt_url'  => "/admin/pos/receipt/{$saleId}",
                        'already_paid' => true,
                    ]);
                }

                $txn = $data['data'] ?? [];

                $paidAmount = $txn['amount'] ?? session('khqr_amount');
                if ($currency === 'khr') {
                    $exchangeRate = (int) env('KHR_EXCHANGE_RATE', 4100);
                    $paidAmount   = round($paidAmount / $exchangeRate, 2);
                }

                $paymentMethod = 'khqr_' . $currency;

                $sale = $this->completeSale(
                    $paymentMethod,
                    $paidAmount,
                    $txn['hash'] ?? null
                );

                // ✅ Mark as paid in session so the other poller returns
                // gracefully instead of crashing on empty cart
                session([
                    'khqr_paid'    => true,
                    'khqr_sale_id' => $sale->id,
                ]);
                session()->forget(['khqr_md5_usd', 'khqr_md5_khr', 'khqr_amount']);

                $this->sendTelegramNotification($sale, $paymentMethod, $txn);

                return response()->json([
                    'success'     => true,
                    'sale_id'     => $sale->id,
                    'currency'    => strtoupper($currency),
                    'receipt_url' => "/admin/pos/receipt/{$sale->id}",
                ]);
            }

            return response()->json(['status' => 'PENDING']);

        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            Log::error('Bakong connection failed', ['msg' => $e->getMessage()]);
            return response()->json(['status' => 'PENDING']);
        } catch (\Exception $e) {
            Log::error('verifyKhqr exception', ['msg' => $e->getMessage()]);
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    // =========================================================
    // HELPERS
    // =========================================================
    private function qrToImage(string $qrString): string
    {
        $url      = 'https://api.qrserver.com/v1/create-qr-code/?size=250x250&data=' . urlencode($qrString);
        $contents = @file_get_contents($url);
        return $contents ? 'data:image/png;base64,' . base64_encode($contents) : $url;
    }

    private function completeSale($method, $paidAmount, $hash = null)
    {
        $cart = session()->get('cart', []);
        if (empty($cart)) throw new \Exception("Cart is empty");

        $total = 0;
        foreach ($cart as $productId => $item) {
            $product = Product::find($productId);
            if (!$product || $product->stock < $item['quantity']) {
                throw new \Exception("Stock problem for: {$item['name']}");
            }
            $total += $item['price'] * $item['quantity'];
        }

        DB::beginTransaction();
        try {
            $lastSale  = Sale::latest()->first();
            $nextId    = $lastSale ? $lastSale->id + 1 : 1;
            $invoiceNo = 'INV-' . date('Ymd') . '-' . str_pad($nextId, 4, '0', STR_PAD_LEFT);

            $sale = Sale::create([
                'user_id'        => auth()->id(),
                'invoice_no'     => $invoiceNo,
                'total_amount'   => $total,
                'paid_amount'    => $paidAmount,
                'change_amount'  => 0,
                'payment_method' => $method,
                'bakong_hash'  => $hash,
            ]);

            foreach ($cart as $productId => $item) {
                SaleItem::create([
                    'sale_id'    => $sale->id,
                    'product_id' => $productId,
                    'quantity'   => $item['quantity'],
                    'price'      => $item['price'],
                    'total'      => $item['price'] * $item['quantity']
                ]);
                Product::where('id', $productId)->decrement('stock', $item['quantity']);
                StockHistory::create([
                    'product_id' => $productId,
                    'quantity'   => $item['quantity'],
                    'type'       => 'out',
                    'note'       => 'Sold via POS (' . strtoupper($method) . ')'
                ]);
            }

            DB::commit();
            session()->forget('cart');
            return $sale;

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    // private function sendTelegramNotification($sale, $method = null)
    // {
    //     try {
    //         $m = strtolower($method ?? $sale->payment_method);
    //         $paymentLabel = match(true) {
    //             $m === 'cash'            => 'Cash 💵',
    //             $m === 'khqr_usd'        => 'KHQR — USD 🇺🇸✅',
    //             $m === 'khqr_khr'        => 'KHQR — KHR 🇰🇭✅',
    //             str_contains($m, 'khqr') => 'KHQR Bakong ✅',
    //             default                  => strtoupper($m),
    //         };

    //         $message  = "🧾 *New Sale!*\n";
    //         $message .= "Invoice: `{$sale->invoice_no}`\n";
    //         $message .= "Total: $" . number_format($sale->total_amount, 2) . "\n";
    //         $message .= "Payment: {$paymentLabel}\n";
    //         $message .= "Time: " . now()->setTimezone('Asia/Phnom_Penh')->format('d M Y, h:i A');

    //         Http::withoutVerifying()
    //             ->post("https://api.telegram.org/bot" . env('TELEGRAM_BOT_TOKEN') . "/sendMessage", [
    //                 'chat_id'    => env('TELEGRAM_CHAT_ID'),
    //                 'text'       => $message,
    //                 'parse_mode' => 'Markdown'
    //             ]);
    //     } catch (\Exception $e) {
    //         Log::warning('Telegram notification failed', ['msg' => $e->getMessage()]);
    //     }
    // }

    private function sendTelegramNotification($sale, $method = null, $txn = [])
    {
        try {
            $m = strtolower($method ?? $sale->payment_method);
            $paymentLabel = match(true) {
                $m === 'cash'            => 'Cash 💵',
                $m === 'khqr_usd'        => 'KHQR — USD 🇺🇸✅',
                $m === 'khqr_khr'        => 'KHQR — KHR 🇰🇭✅',
                str_contains($m, 'khqr') => 'KHQR Bakong ✅',
                default                  => strtoupper($m),
            };

            $message = "🧾 *New Sale!*\n";
            $message .= "Invoice: `{$sale->invoice_no}`\n";
            $message .= "Total: $" . number_format($sale->total_amount, 2) . "\n";

            // ── KHQR transaction details from Bakong ──────────────────
            if (!empty($txn)) {
                // fromAccountId format: "aclbkhppxxx@aclb" or "012345678@wing"
                $fromAccount = $txn['fromAccountId'] ?? null;

                if ($fromAccount) {
                    // Extract phone/account number and bank tag
                    // Format is either "phonenumber@banktag" or "name@banktag"
                    [$accountPart, $bankTag] = explode('@', $fromAccount) + [null, null];

                    // Map bank tag → full bank name
                    $bankNames = [
                        'aclb'  => 'ACLEDA Bank',
                        'abaa'  => 'ABA Bank',
                        'bkrt'  => 'Bakong',
                        'wing'  => 'Wing Bank',
                        'aba'   => 'ABA Bank',
                        'ppb'   => 'Phnom Penh Bank',
                        'cbd'   => 'Cambodian Bank',
                        'campu' => 'Campu Bank',
                        'adb'   => 'Advanced Bank',
                        'mab'   => 'Maybank',
                        'scb'   => 'Sathapana Bank',
                    ];
                    $bankName = $bankNames[strtolower($bankTag ?? '')] ?? strtoupper($bankTag ?? 'Unknown');

                    $message .= "From: `{$accountPart}`\n";
                    $message .= "Bank: {$bankName}\n";
                }

                // Short hash from description e.g. "60637615632 | KHQR"
                if (!empty($txn['description'])) {
                    $shortRef = explode(' | ', $txn['description'])[0] ?? null;
                    if ($shortRef) {
                        $message .= "Ref: `{$shortRef}`\n";
                    }
                }

                // Transaction hash (short version — first 12 chars)
                if (!empty($txn['hash'])) {
                    $shortHash = substr($txn['hash'], 0, 12) . '...';
                    $message .= "Hash: `{$shortHash}`\n";
                }
            }

            $message .= "Payment: {$paymentLabel}\n";
            $message .= "Time: " . now()->setTimezone('Asia/Phnom_Penh')->format('d M Y, h:i A');

            Http::withoutVerifying()
                ->post("https://api.telegram.org/bot" . env('TELEGRAM_BOT_TOKEN') . "/sendMessage", [
                    'chat_id'    => env('TELEGRAM_CHAT_ID'),
                    'text'       => $message,
                    'parse_mode' => 'Markdown'
                ]);
        } catch (\Exception $e) {
            Log::warning('Telegram notification failed', ['msg' => $e->getMessage()]);
        }
    }
}