<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PermitHistory extends Model
{
    protected $fillable = ['permit_id', 'issued_at', 'expires_at', 'document_path', 'notes', 'updater_name', 'old_number'];

    protected $casts = [
        'issued_at' => 'date',
        'expires_at' => 'date',
    ];

    public function permit()
    {
        return $this->belongsTo(Permit::class);
    }
}
