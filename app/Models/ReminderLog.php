<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReminderLog extends Model
{
    protected $guarded = ['id'];
    
    protected $casts = [
        'target_date' => 'date',
        'rule_days' => 'integer'
    ];
}
