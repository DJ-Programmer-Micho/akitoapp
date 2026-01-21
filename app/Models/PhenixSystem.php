<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PhenixSystem extends Model
{
    protected $fillable = [
        'name','code','base_url','username','password','token',
        'is_active','timeout_seconds','retry_times',
    ];

    protected $casts = [
        'is_active' => 'boolean',

        'username' => 'encrypted',
        'password' => 'encrypted',
        'token'    => 'encrypted',
    ];
}
