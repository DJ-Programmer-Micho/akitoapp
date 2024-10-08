<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductImage extends Model
{
    use HasFactory;
    protected $table = 'product_images';
    protected $fillable = [
        'variation_id',
        'image_path',
        'is_primary',
        'is_secondary',
        'priority',
    ];

    public function product() { return $this->belongsTo(ProductVariation::class, 'variation_id'); }

}
