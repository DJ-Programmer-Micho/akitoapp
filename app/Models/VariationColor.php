<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VariationColor extends Model
{
    use HasFactory;
    protected $table = 'variation_colors';
    protected $fillable = [
        'created_by_id',
        'updated_by_id',
        'code',
        'priority',
        'status',
    ];

    public function creator() { return $this->belongsTo(User::class, 'created_by_id'); }
    public function updater() { return $this->belongsTo(User::class, 'updated_by_id'); }
    public function productVariations() { return $this->belongsToMany(ProductVariation::class, 'product_variation_color'); }
    public function variationColorTranslation() { return $this->hasone(VariationColorTranslations::class,'variation_color_id'); }
}
