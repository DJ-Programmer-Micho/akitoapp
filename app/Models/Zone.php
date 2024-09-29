<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Zone extends Model
{
    use HasFactory;
    protected $fillable = [
        'name', 
        'delivery_man', 
        'digit_payment', 
        'cod_payment', 
        'status',
        'coordinates', 
    ];

    protected $casts = [
        'coordinates' => 'array', // Automatically cast to array
    ];
}
