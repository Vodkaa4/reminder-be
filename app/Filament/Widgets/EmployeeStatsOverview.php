<?php

namespace App\Filament\Widgets;

use App\Models\Employee;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Carbon\Carbon;

class EmployeeStatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;

    protected int | string | array $columnSpan = 'full';

    protected function getColumns(): int
    {
        return 4;
    }

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
            Stat::make('Total Pengguna', User::count())
                ->description('Semua pengguna terdaftar')
                ->descriptionIcon('heroicon-m-users')
                ->color('primary')
                ->chart([7, 2, 10, 3, 15, 4, 17]),

            Stat::make('Pengguna Terverifikasi', User::whereNotNull('email_verified_at')->count())
                ->description('Pengguna dengan email terverifikasi')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success')
                ->chart([17, 16, 14, 15, 14, 13, 12]),

            Stat::make('Total Karyawan', $totalEmployees)
                ->description('Semua karyawan terdaftar')
                ->descriptionIcon('heroicon-m-users')
                ->color('info'),

            Stat::make('Karyawan Tetap', $permanentEmployees)
                ->description('Staf tetap penuh waktu')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),

            Stat::make('Karyawan Kontrak', $contractEmployees)
                ->description('Staf kontrak sementara')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),

            Stat::make('Segera Berakhir (≤30 hari)', $expiringSoon)
                ->description('Kontrak segera berakhir')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color($expiringSoon > 0 ? 'danger' : 'success'),

            Stat::make('Kontrak Berakhir', $expiredContracts)
                ->description('Kontrak yang telah habis')
                ->descriptionIcon('heroicon-m-x-circle')
                ->color($expiredContracts > 0 ? 'danger' : 'success'),
        ];
    }
}
