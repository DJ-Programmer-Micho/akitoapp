<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Tymon\JWTAuth\Contracts\JWTSubject;
use App\Models\CustomerProfile;

class Customer extends Authenticatable implements JWTSubject
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'username',
        'email',
        'password',
        'status',
        'email_verify',
        'phone_verify',
        'company_verify',
        'email_otp_number',
        'phone_otp_number',
        'uid',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'phone_verified_at' => 'datetime',
    ];

    // JWT methods
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

    public function customer_profile() { return $this->hasOne(CustomerProfile::class, 'customer_id'); }
    public function customer_addresses() { return $this->hasMany(CustomerAddress::class, 'customer_id'); }
    public function discountRules() { return $this->hasMany(DiscountRule::class); }
    public function orders() { return $this->hasMany(Order::class, 'customer_id'); }
}
