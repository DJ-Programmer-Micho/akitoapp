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
        'category_id',
        'sub_category_id',
        'slug_id',
        'tag_id',
        'variation_id',
        'information_id',
        'is_spare_part',
        'priority',
        'status',
    ];

    
    protected $casts = [
        'tag_id'=>'array',
    ];

    public function creator() { return $this->belongsTo(User::class, 'created_by_id'); }
    public function updater() { return $this->belongsTo(User::class, 'updated_by_id'); }
    public function productTranslation() { return $this->hasone(ProductTranslation::class,'product_id'); }
    public function information() { return $this->hasone(Information::class,'information_id'); }
    public function variation() { return $this->hasone(ProductVariation::class,'variation_id'); }
    public function brand() { return $this->belongsTo(Category::class, 'brand_id'); }
    public function category() { return $this->belongsTo(Category::class, 'category_id'); }
    public function subCategory() { return $this->belongsTo(SubCategory::class, 'sub_category_id'); }
    public function slug() { return $this->hasone(Slug::class,'slug_id'); }
    public function tag() { return $this->hasMany(Tag::class,'tag_id'); }
}
