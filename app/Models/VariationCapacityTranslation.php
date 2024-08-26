<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VariationCapacityTranslation extends Model
{
    use HasFactory;
    use HasFactory;
    protected $table = 'variation_capacity_translations';
    protected $fillable = [
        'variation_capacity_id',
        'name',
        'locale',
    ];

    public function variationMaterial() { return $this->belongsTo(VariationCapacity::class, 'variation_capacity_id'); }
}
