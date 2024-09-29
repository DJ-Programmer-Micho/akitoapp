<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShippingCost extends Model
{
    use HasFactory;
    protected $fillable = [
        'first_km_cost',
        'additional_km_cost',
        'free_delivery_over',
    ];
}
