<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerProfile extends Model
{
    use HasFactory;

    protected $table = 'customer_profiles';
    protected $fillable = [
        'customer_id',
        'first_name',
        'last_name',
        'business_module', 
        'brand_name',  
        'country',
        'city',
        'address',
        'zip_code',
        'phone_number',
        'avatar',
    ];
    
    public function customer() { return $this->belongsTo(Customer::class); }
}
