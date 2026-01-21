<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PhenixSyncLog extends Model
{
    protected $fillable = [
        'phenix_system_id','system_code','matched','updated','changes',
        'xlsx_path','meta','synced_at'
    ];

    protected $casts = [
        'meta' => 'array',
        'synced_at' => 'datetime',
    ];

    public function system()
    {
        return $this->belongsTo(PhenixSystem::class, 'phenix_system_id');
    }
}
