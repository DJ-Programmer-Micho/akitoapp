<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;
    protected $fillable = [
        'customer_id', 'first_name', 'last_name', 'email',
        'country','city','address','zip_code',
        'latitude','longitude',
        'driver', 'phone_number',
        'shipping_amount','total_amount_usd','total_amount_iqd','exchange_rate',
        'total_minor','paid_minor','refunded_minor','currency',
        'payment_status','payment_method','tracking_number','discount','status',
    ];

    public function orderItems() { return $this->hasMany(OrderItem::class); }
    public function customer() { return $this->belongsTo(Customer::class); }
    public function driverUser() { return $this->belongsTo(User::class, 'driver'); }
    public function payments() { return $this->hasMany(Payment::class); }
}
