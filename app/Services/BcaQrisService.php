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
    protected $origin;

    public function __construct()
    {
        $this->baseUrl = config('bca.base_url');
        $this->clientId = config('bca.client_id');
        $this->clientSecret = config('bca.client_secret');
        $this->apiKey = config('bca.api_key');
        $this->apiSecret = config('bca.api_secret');
        $this->origin = config('bca.origin');
    }

    /**
     * Mendapatkan Access Token dari BCA.
     * Token akan di-cache untuk efisiensi.
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

            $response->throw(); // Lemparkan exception jika request gagal

            return $response->json('access_token');
        });
    }

    /**
     * Membuat signature sesuai dengan ketentuan BCA.
     */
    private function generateSignature(string $httpMethod, string $relativeUrl, string $accessToken, string $requestBody, string $timestamp): string
    {
        $hashedBody = strtolower(hash('sha256', $requestBody));
        $stringToSign = "{$httpMethod}:{$relativeUrl}:{$accessToken}:{$hashedBody}:{$timestamp}";

        return hash_hmac('sha512', $stringToSign, $this->apiSecret);
    }

    /**
     * Generate QRIS MPM (Merchant Presented Mode).
     *
     * @param float $amount
     * @param string $transactionId
     * @return array
     */
    public function generateQris(float $amount, string $transactionId): array
    {
        try {
            $accessToken = $this->getAccessToken();
            // $timestamp = Carbon::now('UTC')->toIso8601String() . 'Z';
            $timestamp = now('UTC')->format('Y-m-d\TH:i:s.v\Z');
            $relativeUrl = '/openapi/v1.0/qr/qr-mpm-generate';
            $url = $this->baseUrl . $relativeUrl;

            // $payload = [
            //     'merchantId' => '0000000000001', // Ganti dengan Merchant ID Anda
            //     'terminalId' => '00000011',     // Ganti dengan Terminal ID Anda
            //     'merchantName' => 'NAMA TOKO ANDA',
            //     'amount' => number_format($amount, 2, '.', ''),
            //     'qrType' => 'MPM',
            //     'transactionId' => $transactionId,
            //     'currency' => 'IDR',
            //     'expiredDate' => '1D', // Berlaku 1 hari
            // ];

            $payload = [
                'qrType' => 'DYNAMIC',
                'amount' => number_format($amount, 2, '.', ''),
                'merchantId' => config('bca.merchant_id'),
                'partnerReferenceNo' => $transactionId,
            ];

            $requestBody = json_encode($payload);

            $hashedBody = strtolower(hash('sha256', $requestBody));
            $stringToSign = "POST:{$relativeUrl}:{$accessToken}:{$hashedBody}:{$timestamp}:{$this->apiKey}";

            // $signature = $this->generateSignature('POST', $relativeUrl, $accessToken, $requestBody, $timestamp);
            $signature = hash_hmac('sha512', $stringToSign, $this->apiSecret);

            $headers = [
                'Authorization' => 'Bearer ' . $accessToken,
                'Content-Type' => 'application/json',
                // 'Origin' => $this->origin,
                'X-BCA-Key' => $this->apiKey,
                'X-BCA-Timestamp' => $timestamp,
                'X-BCA-Signature' => $signature,
            ];

            $response = Http::withHeaders($headers)->withBody($requestBody, 'application/json')->post($url);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'data' => $response->json(),
                ];
            }

            // Gagal, kembalikan response error dari BCA
            return [
                'success' => false,
                'error' => $response->json() ?? ['message' => $response->body()],
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => ['message' => $e->getMessage()],
            ];
        }
    }
}
