<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProductVariation extends Model
{
    use HasFactory;
    protected $table = 'product_variations';
    protected $fillable = [
        'sku',
        'material_id',
        'unit_id',
        'keywords',
        'phenix_system_id',
        'price',
        'discount',
        'stock',
        'order_limit',
        'on_sale',
        'featured',
    ];

    protected $casts = [
        'keywords' => 'array',
        'material_id' => 'integer',
        'phenix_system_id' => 'integer',    
    ];

    public function product() { return $this->hasMany(Product::class, 'variation_id'); }
    public function phenixSystem() { return $this->belongsTo(PhenixSystem::class, 'phenix_system_id');}
    public function images() { return $this->hasMany(ProductImage::class, 'variation_id'); }
    public function colors() { return $this->belongsToMany(VariationColor::class, 'product_variation_color'); }
    public function sizes() { return $this->belongsToMany(VariationSize::class, 'product_variation_size'); }
    public function materials() { return $this->belongsToMany(VariationMaterial::class, 'product_variation_material'); }
    public function capacities() { return $this->belongsToMany(VariationCapacity::class, 'product_variation_capacity'); }
    public function intensity() { return $this->belongsToMany(VariationIntensity::class, 'product_variation_intensity'); }
}
