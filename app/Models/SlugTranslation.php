<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SlugTranslation extends Model
{
    use HasFactory;
    protected $table = 'slug_translations';
    protected $fillable = [
        'slug_id',
        'url',
        'locale',
    ];
    
    public function slug() { return $this->belongsTo(Slug::class,'slug_id'); }
}
