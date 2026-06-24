<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReminderLog extends Model
{
    protected $guarded = [];
    
    protected $casts = [
        'target_date' => 'date',
        'rule_days' => 'int',
        'meta' => 'array',
    ];
}
