<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Zone extends Model
{
    use HasFactory;
    protected $fillable = [
        'name', 
        'delivery_team', 
        'digit_payment', 
        'cod_payment', 
        'status',
        'coordinates', 
    ];

    protected $casts = [
        'coordinates' => 'array', // Automatically cast to array
    ];

    public function driverTeam() { return $this->belongsTo(DriverTeam::class, 'delivery_team'); }
}
