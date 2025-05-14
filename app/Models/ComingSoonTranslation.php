<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ComingSoonTranslation extends Model
{
    use HasFactory;
    protected $table = 'coming_soon_translations';
    protected $fillable = [
        'coming_soon_id',
        'locale',
        'name',
        'slug',
    ];

    public function coming_soon() { return $this->belongsTo(ComingSoon::class, 'coming_soon_id'); }
}
