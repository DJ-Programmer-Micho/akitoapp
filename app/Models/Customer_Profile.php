<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer_Profile extends Model
{
    use HasFactory;

    protected $table = 'customer_profiles';
    protected $fillable = [
        'customer_id',
        'first_name',
        'last_name',
        'country',
        'city',
    ];
}
