<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductVariation extends Model
{
    use HasFactory;
    protected $table = 'products_variations';
    protected $fillable = [
        'product_id',
        'color_id',
        'size_id',
        'material_id',
        'capacity_id',
        'sku',
        'price',
        'discount_price',
        'on_stock',
        'on_sale',
        'featured',
        'status',
        'priority',
    ];

    public function product() { return $this->hasOne(Product::class, 'variation_id'); }
    public function image() { return $this->hasOne(ProductImage::class, 'id'); }

}
