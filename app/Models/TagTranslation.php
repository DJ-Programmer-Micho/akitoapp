<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TagTranslation extends Model
{
    use HasFactory;
    protected $table = 'tag_translations';
    protected $fillable = [
        'tag_id',
        'name',
        'locale',
    ];

    public function tag() { return $this->belongsTo(Tag::class, 'id'); }
}
