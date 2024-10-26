<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TickerTranslation extends Model
{
    use HasFactory;
    protected $table = 'ticker_translations';
    protected $fillable = [
        'ticker_id',
        'locale',
        'name',
        'slug',
    ];

    public function brand() { return $this->belongsTo(Brand::class, 'ticker_id'); }
}
