<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ComingSoon extends Model
{
    use HasFactory;
    protected $table = 'coming_soons';
    protected $fillable = [
        'created_by_id',
        'updated_by_id',
        'priority',
        'status',
        'image',
    ];

    public function creator() { return $this->belongsTo(User::class, 'created_by_id'); }
    public function updater() { return $this->belongsTo(User::class, 'updated_by_id'); }
    public function coming_soon_translation() { return $this->hasOne(ComingSoonTranslation::class, 'coming_soon_id'); }
}
