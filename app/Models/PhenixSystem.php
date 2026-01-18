<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PhenixSystem extends Model
{
    protected $fillable = [
        'name',
        'code',
        'base_url',
        'username',
        'password',
        'is_active',
    ];
}
