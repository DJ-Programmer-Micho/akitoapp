<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubCategoryTranslation extends Model
{
    use HasFactory;
    protected $table = 'sub_category_translations';
    protected $fillable = [
        'sub_category_id',
        'locale',
        'name',
        'slug',
    ];
    
    public function subCategory() { return $this->belongsTo(SubCategory::class,'sub_category_id'); }
}
