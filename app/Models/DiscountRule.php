<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DiscountRule extends Model
{
    use HasFactory;
    protected $fillable = ['customer_id', 'type', 'brand_id', 'category_id', 'sub_category_id', 'product_id', 'discount_percentage'];
    public function customer() { return $this->belongsTo(Customer::class); }
    public function brand() { return $this->belongsTo(Brand::class); }
    public function category() { return $this->belongsTo(Category::class); }
    public function subCategory() { return $this->belongsTo(SubCategory::class); }
    public function product() { return $this->belongsTo(Product::class); }
}
