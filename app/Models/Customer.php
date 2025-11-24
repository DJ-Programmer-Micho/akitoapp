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
    // public function wallet() {return $this->hasOne(CustomerWallet::class); }
    public function walletTransactions() { return $this->hasManyThrough( WalletTransaction::class, CustomerWallet::class,
            'customer_id', // FK on CustomerWallet
            'wallet_id',   // FK on WalletTransaction
            'id',          // PK on Customer
            'id'           // PK on CustomerWallet
        );
    }
    public function wallet()
    {
        // withDefault prevents nulls if a wallet doesn't exist yet
        return $this->hasOne(CustomerWallet::class)->withDefault([
            'balance_minor' => 0,
            'currency' => 'IQD',
        ]);
    }
    public function getWalletBalanceMinorAttribute(): int { return (int) ($this->wallet->balance_minor ?? 0); }
    public function getWalletCurrencyAttribute(): string { return $this->wallet->currency ?? 'IQD'; }
    public function getWalletBalanceFormattedAttribute(): string { return number_format($this->wallet_balance_minor) . ' ' . $this->wallet_currency; }
}
