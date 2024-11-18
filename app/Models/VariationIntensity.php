<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VariationIntensity extends Model
{
    use HasFactory;
    protected $table = 'variation_intensities';
    protected $fillable = [
        'created_by_id',
        'updated_by_id',
        'min',
        'max',
        'priority',
        'status',
    ];

    public function creator() { return $this->belongsTo(User::class, 'created_by_id'); }
    public function updater() { return $this->belongsTo(User::class, 'updated_by_id'); }
    public function productVariations() { return $this->belongsToMany(ProductVariation::class, 'product_variation_size'); }
}
