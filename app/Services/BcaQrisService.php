<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Carbon\Carbon;

class BcaQrisService
{
    protected $baseUrl;
    protected $clientId;
    protected $clientSecret;
    protected $apiKey;
    protected $apiSecret;
    protected $partnerId;
    protected $channelId;

    public function __construct()
    {
        $this->baseUrl     = config('bca.base_url');        // https://sandbox.bca.co.id
        $this->clientId    = config('bca.client_id');
        $this->clientSecret = config('bca.client_secret');
        $this->apiKey      = config('bca.api_key');
        $this->apiSecret   = config('bca.api_secret');
        // $this->partnerId   = config('bca.partner_id');      // X-PARTNER-ID
        // $this->channelId   = config('bca.channel_id');      // CHANNEL-ID
    }

    /**
     * Mendapatkan Access Token
     */
    private function getAccessToken(): string
    {
        return Cache::remember('bca_access_token', 3500, function () {
            $url = $this->baseUrl . '/api/oauth/token';

            $response = Http::asForm()->withHeaders([
                'Authorization' => 'Basic ' . base64_encode($this->clientId . ':' . $this->clientSecret),
            ])->post($url, [
                'grant_type' => 'client_credentials',
            ]);

            $response->throw();

            return $response->json('access_token');
        });
    }

    /**
     * Membuat Signature sesuai ketentuan BCA QRIS
     */
    private function generateSignature(string $accessToken, string $timestamp, string $externalId, string $body): string
    {
        // BCA format: <HTTP_METHOD>:<ENDPOINT_PATH>:<ACCESS_TOKEN>:<BODY_HASH>:<TIMESTAMP>:<EXTERNAL_ID>
        $relativeUrl = '/openapi/v1.0/qr/qr-mpm-generate';
        $hashedBody  = hash('sha256', $body);

        $stringToSign = "POST:$relativeUrl:$accessToken:$hashedBody:$timestamp:$externalId";

        return base64_encode(
            hash_hmac('sha512', $stringToSign, $this->apiSecret, true)
        );
    }

    /**
     * Generate QRIS
     */
    public function generateQris(float $amount, string $transactionId): array
    {
        try {
            $accessToken = $this->getAccessToken();

            // timestamp harus format "2025-09-16T14:05:08+07:00"
            $timestamp   = Carbon::now('Asia/Jakarta')->format('Y-m-d\TH:i:sP');

            $externalId  = (string) Str::uuid();

            $payload = [
                "partnerReferenceNo" => $transactionId,
                "amount" => [
                    "value"    => number_format($amount, 2, '.', ''),
                    "currency" => "IDR"
                ],
                "merchantId"     => '123456789', //config('bca.merchant_id'),
                "subMerchantId"  => "",
                "terminalId"     => 'A1234567', //config('bca.terminal_id'),
                "validityPeriod" => Carbon::now('Asia/Jakarta')->addMinutes(5)->format('Y-m-d\TH:i:sP'),
                "additionalInfo" => [
                    "convenienceFee"      => "0.00",
                    "partnerMerchantType" => "",
                    "terminalLocationName" => "",
                    "qrOption"            => "C"
                ]
            ];

            $body = json_encode($payload, JSON_UNESCAPED_SLASHES);

            $signature = $this->generateSignature($accessToken, $timestamp, $externalId, $body);

            $headers = [
                'Authorization' => 'Bearer ' . $accessToken,
                'Content-Type'  => 'application/json',
                'CHANNEL-ID'    => '95251', //$this->channelId,
                'X-PARTNER-ID'  => '123456789',
                'X-EXTERNAL-ID' => '41807553358950093184162180797837',
                'X-TIMESTAMP'   => $timestamp,
                'X-SIGNATURE'   => $signature,
            ];

            $url = $this->baseUrl . '/openapi/v1.0/qr/qr-mpm-generate';

            $response = Http::withHeaders($headers)->post($url, $payload);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'data'    => $response->json(),
                ];
            }

            return [
                'success' => false,
                'error'   => $response->json() ?? ['message' => $response->body()],
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error'   => ['message' => $e->getMessage()],
            ];
        }
    }
}
