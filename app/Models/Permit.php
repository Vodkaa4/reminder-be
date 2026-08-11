<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Permit extends Model
{
    protected $fillable = [
        'type',
        'number',
        'holder',
        'asset_location',
        'issued_at',
        'expires_at',
        'pic',
        'status',
        'notes',
        'attachment_path',
        'reminders_muted',
        'progress_status',
    ];

    protected $casts = [
        'issued_at'  => 'date',
        'expires_at' => 'date',
    ];

    public function permitHistories()
    {
        return $this->hasMany(PermitHistory::class);
    }

    /**
     * Get the calculated status based on expiry date
     */
    public function getCalculatedStatusAttribute(): string
    {
        if (!$this->expires_at) {
            return 'active';
        }

        $today = Carbon::today();
        $expiryDate = Carbon::parse($this->expires_at);
        $daysUntilExpiry = $today->diffInDays($expiryDate, false);

        if ($daysUntilExpiry < 0) {
            return 'expired';
        } elseif ($daysUntilExpiry <= 60) {
            return 'renewal';
        } else {
            return 'active';
        }
    }

    /**
     * Update the status based on expiry date
     */
    public function updateStatus(): void
    {
        $this->status = $this->calculated_status;
        $this->save();
    }

    /**
     * Override save method to automatically update status
     */
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($permit) {
            if ($permit->expires_at) {
                $today = Carbon::today();
                $expiryDate = Carbon::parse($permit->expires_at);
                $daysUntilExpiry = $today->diffInDays($expiryDate, false);

                if ($daysUntilExpiry < 0) {
                    $permit->status = 'expired';
                } elseif ($daysUntilExpiry <= 60) {
                    $permit->status = 'renewal';
                } else {
                    $permit->status = 'active';
                }
            } else {
                $permit->status = 'active';
            }

            // Automate reminders_muted based on progress_status
            if ($permit->progress_status && $permit->progress_status !== 'Selesai') {
                $permit->reminders_muted = true;
            } else {
                $permit->reminders_muted = false;
            }
        });

        static::updating(function ($permit) {
            // Automatically log history if expires_at changes
            if ($permit->isDirty('expires_at') && $permit->getOriginal('expires_at')) {
                $permit->permitHistories()->create([
                    'issued_at' => $permit->getOriginal('issued_at'),
                    'expires_at' => $permit->getOriginal('expires_at'),
                    'notes' => 'Tersimpan otomatis saat update masa berlaku.',
                    'updater_name' => auth()->user()?->name ?? 'System',
                    'old_number' => $permit->getOriginal('number'),
                ]);
            }
        });
    }
}
