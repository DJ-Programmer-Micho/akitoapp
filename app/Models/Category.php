<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;
    protected $table = 'categories';
    protected $fillable = [
        'created_by_id',
        'updated_by_id',
        'priority',
        'status',
        'image',
    ];

    public function creator() { return $this->belongsTo(User::class, 'created_by_id'); }
    public function updater() { return $this->belongsTo(User::class, 'updated_by_id'); }
    public function categoryTranslation(){ return $this->hasone(CategoryTranslation::class,'category_id'); }
    public function subCategory() { return $this->hasMany(subcategory::class,'category_id'); }
    public function product() { return $this->belongsToMany(Product::class, 'product_category'); }
}
