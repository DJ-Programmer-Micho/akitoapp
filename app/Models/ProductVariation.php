<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductVariation extends Model
{
    use HasFactory;
    protected $table = 'product_variations';
    protected $fillable = [
        'sku',
        'keywords',
        'price',
        'discount',
        'stock',
        'order_limit',
        'on_sale',
        'featured',
    ];

    protected $casts = [
        'keywords' => 'array',
    ];

    public function product() { return $this->hasMany(Product::class, 'variation_id'); }
    public function images() { return $this->hasMany(ProductImage::class, 'variation_id'); }
    public function colors() { return $this->belongsToMany(VariationColor::class, 'product_variation_color'); }
    public function sizes() { return $this->belongsToMany(VariationSize::class, 'product_variation_size'); }
    public function materials() { return $this->belongsToMany(VariationMaterial::class, 'product_variation_material'); }
    public function capacities() { return $this->belongsToMany(VariationCapacity::class, 'product_variation_capacity'); }
}
