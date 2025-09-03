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
        'fee_percentage'
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
}
