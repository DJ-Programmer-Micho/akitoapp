<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Slug extends Model
{
    use HasFactory;
    protected $table = 'slugs';
    protected $fillable = [
        'created_by_id',
        'updated_by_id',
    ];

    public function creator() { return $this->belongsTo(User::class, 'created_by_id'); }
    public function updater() { return $this->belongsTo(User::class, 'updated_by_id'); }
    public function product() { return $this->hasone(Product::class,'slug_id'); }
    public function slugTranslation() { return $this->hasone(SlugTranslation::class,'slug_id'); }
}
