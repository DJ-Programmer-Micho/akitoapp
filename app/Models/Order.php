<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;
    protected $fillable = [
        'customer_id',
        'first_name',
        'last_name',
        'email',
        'country',
        'city',
        'address',
        'zip_code',
        'phone_number',
        'total_amount',
        'payment_status',
        'payment_method',
        'tracking_number',
        'status',
    ];

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }
}
