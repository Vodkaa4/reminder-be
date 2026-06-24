<?php

namespace App\Filament\Widgets;

use App\Models\Employee;
use App\Models\Permit;
use App\Models\User;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class DashboardChart extends ChartWidget
{
    protected static ?string $heading = 'Statistik Sistem';

    protected static ?int $sort = 2;

    protected function getData(): array
    {
        $dates = collect(range(0, 11))->map(function ($month) {
            return Carbon::now()->subMonths($month);
        })->reverse()->values();

        $employeeData = $dates->map(function ($date) {
            return Employee::whereMonth('created_at', $date->month)
                ->whereYear('created_at', $date->year)
                ->count();
        });

        $permitData = $dates->map(function ($date) {
            return Permit::whereMonth('created_at', $date->month)
                ->whereYear('created_at', $date->year)
                ->count();
        });

        return [
            'datasets' => [
                [
                    'label' => 'Karyawan Baru',
                    'data' => $employeeData->toArray(),
                    'borderColor' => '#f59e0b',
                    'backgroundColor' => 'rgba(245, 158, 11, 0.1)',
                    'tension' => 0.4,
                ],
                [
                    'label' => 'Perizinan Baru',
                    'data' => $permitData->toArray(),
                    'borderColor' => '#10b981',
                    'backgroundColor' => 'rgba(16, 185, 129, 0.1)',
                    'tension' => 0.4,
                ],
            ],
            'labels' => $dates->map(fn($d) => $d->translatedFormat('M'))->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
