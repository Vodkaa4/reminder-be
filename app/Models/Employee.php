<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    protected $fillable = [
        'nip', 'name', 'email', 'supervisor', 'is_permanent',
        'contract_start', 'contract_end', 'resign_date',
        'dept', 'sect', 'position', 'location'
    ];

    protected $casts = [
        'is_permanent' => 'boolean',
        'contract_start' => 'date',
        'contract_end' => 'date',
        'resign_date' => 'date',
    ];
}
