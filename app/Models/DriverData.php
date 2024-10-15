<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DriverData extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'driver_lic_no',
        'plate_number',
        'vin_number',
        'vehicle_lic_no',
        'vehicle_model',
    ];

    public function userDriverData() { return $this->belongsTo(User::class); }
}
