<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VariationMaterialTranslation extends Model
{
    use HasFactory;
    protected $table = 'variation_material_translations';
    protected $fillable = [
        'variation_material_id',
        'name',
        'locale',
    ];

    public function variationMaterial() { return $this->belongsTo(VariationMaterial::class, 'variation_material_id'); }
}
