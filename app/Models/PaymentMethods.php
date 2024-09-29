<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentMethods extends Model
{
    use HasFactory;
    protected $fillable = [
        "name",
        "active",
        "addon_identifier",
        "transaction_fee",
        "online",
        "currency",
    ];
}
