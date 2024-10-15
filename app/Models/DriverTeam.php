<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DriverTeam extends Model
{
    use HasFactory;
    protected $fillable = ['team_name','status'];

    public function userDriverTeam() { return $this->belongsToMany(User::class, 'driver_team_membership', 'team_id', 'user_id'); }
}
