<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Qris;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'qris_id',
        'transaction_id',
        'amount',
        'fee',
        'status',
        'description',
        'paid_at',
        'callback_url',
        'callback_response'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'fee' => 'decimal:2',
        'paid_at' => 'datetime'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function qris()
    {
        return $this->belongsTo(Qris::class);
    }
}
