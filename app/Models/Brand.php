<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
    use HasFactory;
    protected $table = 'brands';
    protected $fillable = [
        'created_by_id',
        'updated_by_id',
        'priority',
        'status',
        'image',
    ];

    public function creator() { return $this->belongsTo(User::class, 'created_by_id'); }
    public function updater() { return $this->belongsTo(User::class, 'updated_by_id'); }
    public function brandtranslation() { return $this->hasOne(BrandTranslation::class, 'brand_id'); }
    public function product() { return $this->hasMany(Product::class, 'brand_id'); }
}
