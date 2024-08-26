<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VariationMaterial extends Model
{
    use HasFactory;
    protected $table = 'variation_materials';
    protected $fillable = [
        'created_by_id',
        'updated_by_id',
        'code',
        'priority',
        'status',
    ];

    public function creator() { return $this->belongsTo(User::class, 'created_by_id'); }
    public function updater() { return $this->belongsTo(User::class, 'updated_by_id'); }
    public function variationMaterialeTranslation() { return $this->hasone(VariationMaterialTranslation::class,'variation_material_id'); }
}
