<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReminderLog extends Model
{
    protected $fillable = ['entity','entity_id','target_date','rule_days','recipient','channel','status','meta'];
    protected $casts = ['target_date'=>'date','rule_days'=>'int'];
}
