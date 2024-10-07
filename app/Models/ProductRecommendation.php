<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductRecommendation extends Model
{
    use HasFactory;

    protected $fillable = [
        'created_by_id',
        'updated_by_id',
        'product_id',
        'recommended_product_id'
    ];

    public function creator() { return $this->belongsTo(User::class, 'created_by_id'); }
    public function updater() { return $this->belongsTo(User::class, 'updated_by_id'); }
    public function product() { return $this->belongsTo(Product::class); }
    public function recommendedProduct() { return $this->belongsTo(Product::class, 'recommended_product_id'); }
}
