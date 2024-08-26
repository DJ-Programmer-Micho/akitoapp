<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InformationTranslation extends Model
{
    use HasFactory;
    protected $table = 'information_translations';
    protected $fillable = [
        'information_id',
        'description',
        'addition',
        'question_and_answer',
        'locale',
    ];
    protected $casts = [
        // 'options'=>'array',
        'question_and_answer'=>'array',
    ];

    public function product() { return $this->belongsTo(Information::class,'information_id'); }
}
