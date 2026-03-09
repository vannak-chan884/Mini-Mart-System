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
        $amountStr       = number_format($amount, 2, '.', ''); // e.g. "1.00"
        $paymentOption   = 'abapay_khqr';
        $lifetime        = 30;
        $qrImageTemplate = 'template1_color';

        // ── Hash field order per ABA docs ─────────────────────────────────
        // IMPORTANT: every field must be a string, empty fields = empty string
        // Fields: req_time · merchant_id · tran_id · amount · items ·
        //         first_name · last_name · email · phone · purchase_type ·
        //         payment_option · callback_url · return_deeplink · currency ·
        //         custom_fields · return_params · payout · lifetime · qr_image_template
        $hashStr = $reqTime               // YmdHis string
                 . $this->merchantId      // merchant id string
                 . $tranId                // tran_id string
                 . $amountStr             // "1.00" string — must match payload amount exactly
                 . ''                     // items
                 . ''                     // first_name
                 . ''                     // last_name
                 . ''                     // email
                 . ''                     // phone
                 . ''                     // purchase_type
                 . $paymentOption         // "abapay_khqr"
                 . ''                     // callback_url
                 . ''                     // return_deeplink
                 . $currency              // "USD"
                 . ''                     // custom_fields
                 . ''                     // return_params
                 . ''                     // payout
                 . (string) $lifetime     // "30" — must be string in hash
                 . $qrImageTemplate;      // "template1"

        $hash = $this->hmac($hashStr);

        // ── Payload — amount sent as STRING to match hash exactly ─────────
        // ABA docs show amount as number in JSON but hash uses string "1.00"
        // Sending as string is safer and matches hash computation
        $payload = [
            'req_time'          => $reqTime,
            'merchant_id'       => $this->merchantId,
            'tran_id'           => $tranId,
            'amount'            => $amountStr,   // ✅ string — matches hash exactly
            'currency'          => $currency,
            'payment_option'    => $paymentOption,
            'lifetime'          => $lifetime,    // int in payload is fine
            'qr_image_template' => $qrImageTemplate,
            'hash'              => $hash,
        ];

        $response = $this->postJson($this->apiUrl, $payload);

        // Log response for debugging — remove after confirmed working
        Log::info('ABA PayWay generate response', [
            'status'    => $response['status'] ?? null,
            'tran_id'   => $tranId,
            'merchant'  => $this->merchantId,
            'amount'    => $amountStr,
            'hash_input'=> substr($hashStr, 0, 60) . '...', // partial — don't log full key
        ]);

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