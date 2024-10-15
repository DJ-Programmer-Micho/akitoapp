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
        'latitude',
        'longitude',
        'driver',
        'phone_number',
        'shipping_amount',
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
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
    public function driverUser() // Renamed for clarity
    {
        return $this->belongsTo(User::class, 'driver'); // Specify 'driver' as the foreign key
    }
    // public function calculateTotalAmount()
    // {
    //     return $this->orderItems->sum('total');
    // }
}
