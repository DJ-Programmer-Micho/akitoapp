<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VariationSizeTranslation extends Model
{
    use HasFactory;
    protected $table = 'variation_size_translations';
    protected $fillable = [
        'variation_size_id',
        'name',
        'locale',
    ];

    public function variationSize() { return $this->belongsTo(VariationSize::class, 'variation_size_id'); }
}
