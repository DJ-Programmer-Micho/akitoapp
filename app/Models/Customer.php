<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;
    protected $fillable = [
        'email',
        'phone',
        'password',
        'status',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

        protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function customer_profile()
    {
        return $this->hasOne(Customer_Profile::class, 'customer_id');
    }
}
