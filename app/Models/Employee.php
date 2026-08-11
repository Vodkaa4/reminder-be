<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    protected $fillable = [
        'nip', 'name', 'email', 'supervisor', 'is_permanent',
        'contract_start', 'contract_end', 'resign_date',
        'dept', 'sect', 'position', 'location', 'reminders_muted'
    ];

    protected $casts = [
        'is_permanent' => 'boolean',
        'contract_start' => 'date',
        'contract_end' => 'date',
        'resign_date' => 'date',
    ];

    public function contractHistories()
    {
        return $this->hasMany(ContractHistory::class);
    }

    protected static function boot()
    {
        parent::boot();

        static::updating(function ($employee) {
            // Automatically log history if contract_end changes
            if ($employee->isDirty('contract_end') && $employee->getOriginal('contract_end')) {
                $employee->contractHistories()->create([
                    'start_date' => $employee->getOriginal('contract_start'),
                    'end_date' => $employee->getOriginal('contract_end'),
                    'notes' => 'Tersimpan otomatis saat update masa berlaku.',
                    'updater_name' => auth()->user()?->name ?? 'System',
                ]);
            }
        });
    }
}
