<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Information extends Model
{
    use HasFactory;
    protected $table = 'categories';
    protected $fillable = [
        'created_by_id',
        'updated_by_id',
    ];
    public function creator() { return $this->belongsTo(User::class, 'created_by_id'); }
    public function updater() { return $this->belongsTo(User::class, 'updated_by_id'); }
    public function informationTranslation(){ return $this->hasone(InformationTranslation::class,'information_id'); }
    public function product() { return $this->hasMany(Product::class, 'information_id'); }

}
