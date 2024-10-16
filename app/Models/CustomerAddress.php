<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerAddress extends Model
{
    use HasFactory;
    protected $fillable = [
        'customer_id',
        'type',
        'building_name',
        'apt_or_company',
        'address_name',
        'floor',
        'country',
        'city',
        'address',
        'zip_code',
        'phone_number',
        'additional_directions',
        'address_label',
        'latitude',
        'longitude',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
