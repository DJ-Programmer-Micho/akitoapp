<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    protected $table = 'products';
    protected $fillable = [
        'created_by_id',
        'updated_by_id',
        'brand_id',
        'variation_id',
        'information_id',
        'is_spare_part',
        'priority',
        'status',
    ];

    public function creator() { return $this->belongsTo(User::class, 'created_by_id'); }
    public function updater() { return $this->belongsTo(User::class, 'updated_by_id'); }
    public function productTranslation() { return $this->hasMany(ProductTranslation::class,'product_id'); }
    public function information() { return $this->belongsTo(Information::class,'information_id'); }
    public function variation() { return $this->belongsTo(ProductVariation::class,'variation_id'); }
    public function brand() { return $this->belongsTo(Brand::class, 'brand_id'); }
    public function categories() { return $this->belongsToMany(Category::class, 'product_category'); }
    public function subCategories() { return $this->belongsToMany(SubCategory::class, 'product_sub_category'); }
    public function tags() { return $this->belongsToMany(Tag::class,'product_tag'); }
    // public function slug() { return $this->hasone(Slug::class,'slug_id'); }
}
