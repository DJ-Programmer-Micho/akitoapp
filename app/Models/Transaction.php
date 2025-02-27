<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'stripe_session_id',
        'order_id',
        'provider', // Matches migration
        'amount',
        'currency',
        'status',
        'response',
    ];

    protected $casts = [
        'response' => 'array', // Make sure this matches migration
    ];

    public $incrementing = false;
    protected $keyType = 'string';

    // protected static function boot()
    // {
    //     parent::boot();
    //     static::creating(function ($model) {
    //         $model->{$model->getKeyName()} = Str::uuid()->toString();
    //     });
    // }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
