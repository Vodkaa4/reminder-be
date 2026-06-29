<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReminderRule extends Model
{
    protected $fillable = [
        'entity',
        'days_before',
        'channel',
        'active',
        'is_recurring',
        'recurring_interval_days',
    ];
    
    protected $casts = [
        'entity' => 'string',
        'active' => 'boolean',
        'is_recurring' => 'boolean',
        'recurring_interval_days' => 'integer',
        'days_before' => 'integer',
    ];
    
    // Konstanta untuk nilai entity
    const ENTITY_CONTRACT = 'contract';
    const ENTITY_PERMIT = 'permit';
    
    // Getter untuk mendapatkan pilihan entity
    public static function getEntityOptions()
    {
        return [
            self::ENTITY_CONTRACT => 'Contract',
            self::ENTITY_PERMIT => 'Permit'
        ];
    }
}
