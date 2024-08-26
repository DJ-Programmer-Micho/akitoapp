<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VariationCapacity extends Model
{
    use HasFactory;
    protected $table = 'variation_capacities';
    protected $fillable = [
        'created_by_id',
        'updated_by_id',
        'code',
        'priority',
        'status',
    ];

    public function creator() { return $this->belongsTo(User::class, 'created_by_id'); }
    public function updater() { return $this->belongsTo(User::class, 'updated_by_id'); }
    public function variationCapacityTranslation() { return $this->hasone(VariationCapacityTranslation::class,'variation_capacity_id'); }
}
