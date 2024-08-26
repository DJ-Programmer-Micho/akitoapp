<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    use HasFactory;
    protected $table = 'tags';
    protected $fillable = [
        'created_by_id',
        'updated_by_id',
        'icon',
        'priority',
        'status',
    ];

    public function creator() { return $this->belongsTo(User::class, 'created_by_id'); }
    public function updater() { return $this->belongsTo(User::class, 'updated_by_id'); }
    public function product() { return $this->hasone(Product::class,'tag_id'); }
    public function tagTranslation() { return $this->hasone(TagTranslation::class,'tag_id'); }

}
