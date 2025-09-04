<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReminderRule extends Model
{
    protected $guarded = ['id'];
    
    protected $casts = [
        'active' => 'boolean',
        'days_before' => 'integer'
    ];
    
    public $timestamps = true;
}
