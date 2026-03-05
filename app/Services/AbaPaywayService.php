<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class AbaPaywayService
{
    protected string $merchantId;
    protected string $apiKey;
    protected string $apiUrl;
    protected string $statusUrl;

    public function __construct()
    {
        $this->merchantId = config('services.aba_payway.merchant_id');
        $this->apiKey     = config('services.aba_payway.api_key');
        $this->apiUrl     = config('services.aba_payway.api_url');
        $this->statusUrl  = config('services.aba_payway.status_url');
    }

    // ── Generate QR payment ───────────────────────────────
    public function createPayment(float $amount, string $invoiceNumber): array
    {
        $tranId          = $invoiceNumber;
        $reqTime         = Carbon::now('UTC')->format('YmdHis');
        $currency        = 'USD';
        $amountStr       = number_format($amount, 2, '.', '');
        $paymentOption   = 'abapay_khqr';
        $lifetime        = 30;
        $qrImageTemplate = 'template1';

        // Hash field order per ABA docs:
        // req_time, merchant_id, tran_id, amount, items, first_name, last_name,
        // email, phone, purchase_type, payment_option, callback_url, return_deeplink,
        // currency, custom_fields, return_params, payout, lifetime, qr_image_template
        $hashStr = $reqTime
                 . $this->merchantId
                 . $tranId
                 . $amountStr
                 . ''   // items
                 . ''   // first_name
                 . ''   // last_name
                 . ''   // email
                 . ''   // phone
                 . ''   // purchase_type
                 . $paymentOption
                 . ''   // callback_url
                 . ''   // return_deeplink
                 . $currency
                 . ''   // custom_fields
                 . ''   // return_params
                 . ''   // payout
                 . $lifetime
                 . $qrImageTemplate;

        $hash = $this->hmac($hashStr);

        $payload = [
            'req_time'          => $reqTime,
            'merchant_id'       => $this->merchantId,
            'tran_id'           => $tranId,
            'amount'            => $amountStr,
            'currency'          => $currency,
            'payment_option'    => $paymentOption,
            'lifetime'          => $lifetime,
            'qr_image_template' => $qrImageTemplate,
            'hash'              => $hash,
        ];

        $response = $this->postJson($this->apiUrl, $payload);

        return [
            'status'    => $response['status']['code'] ?? 'error',
            'tran_id'   => $tranId,
            'qr_string' => $response['qrString'] ?? null,
            'qr_image'  => $response['qrImage']  ?? null, // base64 PNG from ABA
            'deeplink'  => $response['abapay_deeplink'] ?? null,
        ];
    }

    // ── Check transaction status ──────────────────────────
    public function checkTransaction(string $tranId): array
    {
        $reqTime = Carbon::now('UTC')->format('YmdHis');

        // Status check hash: req_time + merchant_id + tran_id
        $hash = $this->hmac($reqTime . $this->merchantId . $tranId);

        $payload = [
            'req_time'    => $reqTime,
            'merchant_id' => $this->merchantId,
            'tran_id'     => $tranId,
            'hash'        => $hash,
        ];

        return $this->postJson($this->statusUrl, $payload);
    }

    // ── HMAC-SHA512 with API key ──────────────────────────
    protected function hmac(string $data): string
    {
        return base64_encode(hash_hmac('sha512', $data, $this->apiKey, true));
    }

    // ── JSON POST helper ──────────────────────────────────
    protected function postJson(string $url, array $data): array
    {
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => json_encode($data),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_TIMEOUT        => 30,
            CURLOPT_HTTPHEADER     => [
                'Content-Type: application/json',
                'Accept: application/json',
            ],
        ]);

        $result    = curl_exec($ch);
        $curlError = curl_error($ch);
        curl_close($ch);

        if ($curlError) {
            Log::error('ABA PayWay cURL error', ['url' => $url, 'error' => $curlError]);
        }

        return json_decode($result, true) ?? [];
    }
}