<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;
    protected $fillable = [
        'order_id',
        'customer_id',
        'amount_minor',
        'currency',
        'method',
        'status',
        'provider',
        'provider_payment_id',
        'idempotency_key',
        'type',
        'meta',
    ];
    protected $casts = [
        'meta' => 'array',
    ];
    public function order() { return $this->belongsTo(Order::class); }
    public function customer() { return $this->belongsTo(Customer::class); }
    public function topupCustomer() { return $this->belongsTo(Customer::class, 'customer_id'); }
    public function transactions() { return $this->hasMany(Transaction::class); }
    public function refunds() { return $this->hasMany(Refund::class); }
}
