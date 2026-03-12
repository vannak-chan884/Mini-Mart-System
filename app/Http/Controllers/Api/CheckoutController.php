<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use KHQR\BakongKHQR;
use KHQR\Helpers\KHQRData;
use KHQR\Models\IndividualInfo;

class CheckoutController extends ApiController
{
    // POST /api/checkout/khqr — generate QR for amount
    public function generateKhqr(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.01',
        ]);

        try {
            $amountUSD    = round((float) $request->amount, 2);
            $exchangeRate = (int) env('KHR_EXCHANGE_RATE', 4100);
            $amountKHR    = (int) round($amountUSD * $exchangeRate);
            $expiresInMs  = strval((int) floor(microtime(true) * 1000) + (5 * 60 * 1000));

            // ── USD QR ───────────────────────────────────────────────
            $infoUSD = new IndividualInfo(
                bakongAccountID:     env('BAKONG_ACCOUNT_ID'),
                merchantName:        env('BAKONG_MERCHANT_NAME', 'Mini Mart'),
                merchantCity:        env('BAKONG_MERCHANT_CITY', 'Phnom Penh'),
                currency:            KHQRData::CURRENCY_USD,
                amount:              $amountUSD,
                expirationTimestamp: $expiresInMs
            );
            $resultUSD = BakongKHQR::generateIndividual($infoUSD);

            // ── KHR QR ───────────────────────────────────────────────
            $infoKHR = new IndividualInfo(
                bakongAccountID:     env('BAKONG_ACCOUNT_ID'),
                merchantName:        env('BAKONG_MERCHANT_NAME', 'Mini Mart'),
                merchantCity:        env('BAKONG_MERCHANT_CITY', 'Phnom Penh'),
                currency:            KHQRData::CURRENCY_KHR,
                amount:              $amountKHR,
                expirationTimestamp: $expiresInMs
            );
            $resultKHR = BakongKHQR::generateIndividual($infoKHR);

            $qrUSD = $resultUSD->data['qr'];
            $md5USD = $resultUSD->data['md5'];
            $qrKHR = $resultKHR->data['qr'];
            $md5KHR = $resultKHR->data['md5'];

            return $this->success([
                'usd' => [
                    'qr_string'  => $qrUSD,
                    'qr_image'   => 'https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=' . urlencode($qrUSD),
                    'md5'        => $md5USD,
                    'amount'     => $amountUSD,
                    'label'      => '$' . number_format($amountUSD, 2),
                ],
                'khr' => [
                    'qr_string'  => $qrKHR,
                    'qr_image'   => 'https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=' . urlencode($qrKHR),
                    'md5'        => $md5KHR,
                    'amount'     => $amountKHR,
                    'label'      => '៛' . number_format($amountKHR),
                ],
                'merchant_name' => env('BAKONG_MERCHANT_NAME', 'Mini Mart'),
                'expires_at'    => time() + 300,
                'exchange_rate' => $exchangeRate,
            ]);

        } catch (\Exception $e) {
            Log::error('CheckoutController@generateKhqr', ['msg' => $e->getMessage()]);
            return $this->error('Failed to generate KHQR: ' . $e->getMessage());
        }
    }

    // POST /api/checkout/khqr/verify
    public function verifyKhqr(Request $request)
    {
        $request->validate([
            'md5'      => 'required|string',
            'currency' => 'required|in:usd,khr',
        ]);

        try {
            $response = Http::timeout(10)
                ->withoutVerifying()
                ->withHeaders(['Authorization' => 'Bearer ' . env('BAKONG_TOKEN')])
                ->post('https://api-bakong.nbc.gov.kh/v1/check_transaction_by_md5', [
                    'md5' => $request->md5,
                ]);

            if ($response->status() === 401) {
                return $this->error('Bakong token expired.', 401);
            }

            $data = $response->json();

            if (isset($data['responseCode']) && $data['responseCode'] === 0) {
                return $this->success(['paid' => true, 'txn' => $data['data'] ?? []]);
            }

            return $this->success(['paid' => false]);

        } catch (\Exception $e) {
            Log::error('CheckoutController@verifyKhqr', ['msg' => $e->getMessage()]);
            return $this->success(['paid' => false]);
        }
    }
}