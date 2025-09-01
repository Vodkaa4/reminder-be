<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Permit extends Model
{
    protected $fillable = [
        'type','number','holder','asset_location',
        'issued_at','expires_at','pic','status',
        'notes','attachment_path',
    ];

    protected $casts = [
        'issued_at'  => 'date',
        'expires_at' => 'date',
    ];
}
