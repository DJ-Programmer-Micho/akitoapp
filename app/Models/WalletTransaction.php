<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WalletTransaction extends Model
{
    use HasFactory;
    protected $fillable = [
        'wallet_id', 'direction', 'amount_minor', 'currency',
        'source_type', 'source_id', 'reason', 'meta',
        'balance_after_minor', 'idempotency_key'
    ];

    protected $casts = [
        'meta' => 'array',
    ];

    public function wallet()
    {
        return $this->belongsTo(CustomerWallet::class, 'wallet_id');
    }

    public function source()
    {
        return $this->morphTo();
    }
}
