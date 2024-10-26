<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ticker extends Model
{
    use HasFactory;
    protected $table = 'tickers';
    protected $fillable = [
        'created_by_id',
        'updated_by_id',
        'url',
        'priority',
        'status',
    ];

    public function creator() { return $this->belongsTo(User::class, 'created_by_id'); }
    public function updater() { return $this->belongsTo(User::class, 'updated_by_id'); }
    public function tickerTranslation() { return $this->hasOne(TickerTranslation::class, 'ticker_id'); }
}
