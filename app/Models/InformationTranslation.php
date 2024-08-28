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
        'locale',
        'description',
        'addition',
        'shipping',
        'question_and_answer',
    ];
    protected $casts = [
        'description'=>'array',
        'addition'=>'array',
        'shipping'=>'array',
        'question_and_answer'=>'array',
    ];

    public function product() { return $this->belongsTo(Information::class,'information_id'); }
}
