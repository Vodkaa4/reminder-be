<?php

namespace App\Filament\Widgets;

use App\Models\Employee;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Carbon\Carbon;

class EmployeeStatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $totalEmployees = Employee::count();
        $permanentEmployees = Employee::where('is_permanent', true)->count();
        $contractEmployees = Employee::where('is_permanent', false)->count();
        
        // Count contracts expiring in the next 30 days
        $expiringSoon = Employee::where('is_permanent', false)
            ->where('contract_end', '>=', now())
            ->where('contract_end', '<=', now()->addDays(30))
            ->count();

        // Count expired contracts
        $expiredContracts = Employee::where('is_permanent', false)
            ->where('contract_end', '<', now())
            ->count();

        return [
            Stat::make('Total Users', User::count())
                ->description('All registered users')
                ->descriptionIcon('heroicon-m-users')
                ->color('primary')
                ->chart([7, 2, 10, 3, 15, 4, 17]),

            Stat::make('Verified Users', User::whereNotNull('email_verified_at')->count())
                ->description('Users with verified email')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success')
                ->chart([17, 16, 14, 15, 14, 13, 12]),

            Stat::make('Total Employees', $totalEmployees)
                ->description('All registered employees')
                ->descriptionIcon('heroicon-m-users')
                ->color('info'),

            Stat::make('Permanent Employees', $permanentEmployees)
                ->description('Full-time permanent staff')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),

            Stat::make('Contract Employees', $contractEmployees)
                ->description('Temporary contract staff')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),

            Stat::make('Expiring Soon (≤30 days)', $expiringSoon)
                ->description('Contracts ending soon')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color($expiringSoon > 0 ? 'danger' : 'success'),

            Stat::make('Expired Contracts', $expiredContracts)
                ->description('Overdue contracts')
                ->descriptionIcon('heroicon-m-x-circle')
                ->color($expiredContracts > 0 ? 'danger' : 'success'),
        ];
    }
}
