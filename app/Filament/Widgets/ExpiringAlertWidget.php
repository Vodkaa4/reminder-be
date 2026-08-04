<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use App\Models\Permit;
use App\Models\Employee;

class ExpiringAlertWidget extends Widget
{
    protected static string $view = 'filament.widgets.expiring-alert-widget';

    protected static ?int $sort = -1; // Top of the dashboard

    public int $expiringPermitsCount = 0;
    public int $expiringContractsCount = 0;

    public function mount()
    {
        $this->expiringPermitsCount = Permit::where('status', 'renewal')
            ->orWhere(function ($query) {
                $query->where('expires_at', '<=', today()->addDays(60))
                      ->where('expires_at', '>=', today());
            })->count();

        $this->expiringContractsCount = Employee::where('is_permanent', false)
            ->where('contract_end', '<=', today()->addDays(30))
            ->where('contract_end', '>=', today())
            ->count();
    }
}
