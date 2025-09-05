# Panduan Transisi dari QRIS Dummy ke QRIS Asli

## Overview
Dokumen ini menjelaskan langkah-langkah yang diperlukan untuk beralih dari penggunaan QRIS dummy/sandbox ke QRIS asli dalam sistem QRIS Payment Gateway.

## Komponen yang Menggunakan QRIS Dummy

### 1. Database Seeder
- **File**: `database/seeders/QrisSeeder.php`
- **Isi**: Data QRIS dummy dengan `qris_code` seperti 'DUMMY_QRIS_CODE_BCA_STATIC_001'

### 2. Model QRIS
- **File**: `app/Models/Qris.php`
- **Field yang relevan**:
  - `qris_code`: Menyimpan kode QRIS (saat ini berisi kode dummy)
  - `qris_image`: Menyimpan URL gambar QRIS (saat ini null untuk dummy)
  - `type`: Tipe QRIS (static/dynamic)

### 3. API Controller
- **File**: `app/Http/Controllers/Api/PaymentController.php`
- **Fungsi yang menggunakan QRIS**:
  - `generateQris()`: Mengambil QRIS berdasarkan strategi distribusi
  - Mengembalikan `qris_code` dan `qris_image` dalam response

### 4. Layanan Distribusi QRIS
- **File**: `app/Services/QrisDistributionService.php`
- **Fungsi**: Memilih QRIS berdasarkan strategi (random, round-robin, specific)

### 5. Simulasi Pembayaran
- **File**: `app/Console/Commands/SimulatePayment.php`
- **Fungsi**: Menggunakan QRIS dummy untuk simulasi

## Bagian yang Perlu Disesuaikan untuk QRIS Asli

### 1. Data QRIS dalam Database
**Perubahan yang diperlukan**:
- Mengganti `qris_code` dengan kode QRIS asli dari bank/provider
- Menambahkan `qris_image` dengan URL gambar QRIS asli
- Memastikan `bank_id` sesuai dengan bank yang memberikan QRIS

**Cara implementasi**:
```sql
-- Contoh update untuk QRIS asli
UPDATE qris 
SET 
  qris_code = '0002010102112230303IDR0408123456785204533353033605405100005802ID5915NAMA MERCHANT6015KOTA MERCHANT610512345624201031230215MERCHANT_ID6304ABCD',
  qris_image = 'https://your-storage-url/qris-images/bca-merchant.png',
  name = 'BCA Merchant QRIS'
WHERE id = 1;
```

### 2. Integrasi dengan Provider QRIS
**Perubahan yang diperlukan**:
- Menambahkan API client untuk berkomunikasi dengan provider QRIS
- Mengimplementasikan pembuatan QRIS dinamis jika diperlukan
- Menangani callback dari provider QRIS

**Lokasi implementasi**:
- Membuat service baru: `app/Services/QrisProviderService.php`
- Memperbarui `PaymentController::generateQris()` untuk menggunakan service provider

### 3. Verifikasi Callback
**Perubahan yang diperlukan**:
- Menambahkan verifikasi signature/callback dari provider QRIS
- Memastikan keamanan dengan memvalidasi callback source

**Lokasi implementasi**:
- Memperbarui `PaymentController::handleCallback()`

### 4. Konfigurasi Environment
**Perubahan yang diperlukan**:
- Menambahkan environment variables untuk API key provider QRIS
- Menambahkan URL callback yang sesuai

**Variabel yang perlu ditambahkan di `.env`**:
```env
# QRIS Provider Configuration
QRIS_PROVIDER_API_KEY=your_provider_api_key
QRIS_PROVIDER_BASE_URL=https://api.qrisprovider.com
QRIS_CALLBACK_URL=https://yourdomain.com/api/payment/callback
```

### 5. Penanganan Gambar QRIS
**Perubahan yang diperlukan**:
- Menyimpan gambar QRIS asli di storage yang sesuai
- Memastikan URL gambar dapat diakses publik

**Implementasi**:
- Menambahkan filesystem configuration untuk storage QRIS
- Memperbarui `qris_image` dengan URL publik gambar

## Langkah-langkah Implementasi

### Tahap 1: Persiapan
1. Dapatkan QRIS code dan gambar dari provider/bank
2. Siapkan storage untuk gambar QRIS
3. Dapatkan API key dan dokumentasi dari provider QRIS

### Tahap 2: Konfigurasi Awal
1. Tambahkan environment variables di `.env`
2. Update konfigurasi jika diperlukan
3. Jalankan migration jika ada penambahan field

### Tahap 3: Implementasi Service Provider
1. Buat `app/Services/QrisProviderService.php`
2. Implementasikan method untuk:
   - Generate QRIS dinamis (jika diperlukan)
   - Verifikasi callback
   - Handle error

### Tahap 4: Update Controller
1. Modifikasi `PaymentController::generateQris()` untuk menggunakan provider service
2. Update `PaymentController::handleCallback()` dengan verifikasi signature

### Tahap 5: Update Data Database
1. Hapus atau nonaktifkan QRIS dummy
2. Tambahkan QRIS asli dengan data yang benar
3. Pastikan relasi dengan bank tetap terjaga

### Tahap 6: Testing
1. Test generate QRIS dengan QRIS asli
2. Test callback handling
3. Test semua strategi distribusi
4. Test error handling

## Contoh Implementasi Service Provider

### Membuat Service Provider
```php
<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class QrisProviderService
{
    protected $apiKey;
    protected $baseUrl;
    
    public function __construct()
    {
        $this->apiKey = config('qris.provider_api_key');
        $this->baseUrl = config('qris.provider_base_url');
    }
    
    /**
     * Generate dynamic QRIS
     */
    public function generateQris($amount, $description = null)
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->apiKey,
            'Content-Type' => 'application/json',
        ])->post($this->baseUrl . '/qris/generate', [
            'amount' => $amount,
            'description' => $description,
            'callback_url' => config('qris.callback_url'),
        ]);
        
        if ($response->successful()) {
            return $response->json();
        }
        
        Log::error('QRIS Generation Failed', [
            'status' => $response->status(),
            'body' => $response->body(),
        ]);
        
        return null;
    }
    
    /**
     * Verify callback signature
     */
    public function verifyCallback($payload, $signature)
    {
        // Implement signature verification based on provider documentation
        // This is just an example
        $expectedSignature = hash_hmac('sha256', json_encode($payload), $this->apiKey);
        return hash_equals($expectedSignature, $signature);
    }
}
```

### Update Controller untuk Menggunakan Service Provider
```php
// Di PaymentController::generateQris()
// Ganti bagian pemilihan QRIS dengan:
$qrisProviderService = new QrisProviderService();

if ($selectedQris->type === 'dynamic') {
    // Generate dynamic QRIS via provider
    $providerResponse = $qrisProviderService->generateQris(
        $request->amount, 
        $request->description
    );
    
    if ($providerResponse) {
        $qrisCode = $providerResponse['qris_code'];
        $qrisImage = $providerResponse['qris_image'];
    } else {
        return response()->json([
            'success' => false,
            'message' => 'Failed to generate dynamic QRIS'
        ], 500);
    }
} else {
    // Use static QRIS
    $qrisCode = $selectedQris->qris_code;
    $qrisImage = $selectedQris->qris_image;
}
```

## Pertimbangan Keamanan

1. **Verifikasi Callback**: Selalu verifikasi signature/callback dari provider
2. **HTTPS**: Gunakan HTTPS untuk semua komunikasi
3. **Logging**: Log semua transaksi dan callback untuk audit
4. **Rate Limiting**: Implementasikan rate limiting untuk mencegah abuse
5. **Data Sensitif**: Jangan log API key atau data sensitif lainnya

## Testing dan Verifikasi

1. **Unit Testing**: Buat test untuk service provider
2. **Integration Testing**: Test end-to-end flow dengan QRIS asli
3. **Callback Testing**: Test berbagai skenario callback (success, failed, timeout)
4. **Security Testing**: Verifikasi bahwa signature verification bekerja dengan benar

## Rollback Plan

1. Simpan backup data QRIS dummy
2. Simpan konfigurasi awal
3. Siapkan script untuk rollback ke QRIS dummy jika diperlukan
4. Dokumentasikan prosedur rollback dengan jelas

## Maintenance

1. Monitor transaksi secara berkala
2. Update API key secara berkala sesuai kebijakan provider
3. Backup data QRIS secara teratur
4. Update dokumentasi sesuai perubahan yang terjadi