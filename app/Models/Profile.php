<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    use HasFactory;

    protected $table = 'profiles';
    protected $fillable = [
        'user_id',
        'position',
        'first_name',
        'last_name',
        'address',
        'phone_number',
        'avatar',
    ];
    
    public function user() { return $this->belongsTo(User::class); }
}
