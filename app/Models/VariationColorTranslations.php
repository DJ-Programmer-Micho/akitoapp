<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VariationColorTranslations extends Model
{
    use HasFactory;
    protected $table = 'variation_color_translations';
    protected $fillable = [
        'variation_color_id',
        'name',
        'locale',
    ];

    public function variationColor() { return $this->belongsTo(VariationColor::class, 'variation_color_id'); }
}
