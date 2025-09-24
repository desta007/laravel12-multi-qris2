<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Transaction;

class Qris extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'bank_id',
        'qris_code',
        'qris_image',
        'type',
        'is_active',
        'fee_percentage',
        'bca_merchant_id',
        'bca_terminal_id',
        'bca_additional_info'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'fee_percentage' => 'decimal:2'
    ];

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function bank()
    {
        return $this->belongsTo(Bank::class);
    }

    /**
     * Generate QR code image from the QRIS code
     */
    public function generateQrCodeImage()
    {
        if (empty($this->qris_code)) {
            return null;
        }

        try {
            // Hasil generate sudah berupa string SVG
            $qrCode = \SimpleSoftwareIO\QrCode\Facades\QrCode::format('svg')
                ->size(200)
                ->margin(2)
                ->generate($this->qris_code);

            // langsung jadikan data URI
            return 'data:image/svg+xml;base64,' . base64_encode($qrCode);
        } catch (\Exception $e) {
            \Log::error('QR Code generation failed: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Accessor for QR code image data URL
     */
    public function getQrCodeImageDataUrlAttribute()
    {
        return $this->generateQrCodeImage();
    }
}
