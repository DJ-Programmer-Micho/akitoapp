<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubCategory extends Model
{
    use HasFactory;
    protected $table = 'sub_categories';
    protected $fillable = [
        'created_by_id',
        'updated_by_id',
        'category_id',
        'priority',
        'status',
        'image',
    ];

    public function creator() { return $this->belongsTo(User::class, 'created_by_id'); }
    public function updater() { return $this->belongsTo(User::class, 'updated_by_id'); }
    public function subCategoryTranslation() { return $this->hasone(SubCategoryTranslation::class,'sub_category_id');  }
    public function category() { return $this->belongsTo(Category::class, 'category_id'); }
    public function product() { return $this->belongsToMany(Product::class,'product_sub_category'); }
}
