<?php

namespace App\Filament\Widgets;

use App\Models\Employee;
use App\Models\Permit;
use App\Models\User;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class DashboardChart extends ChartWidget
{
    protected static ?string $heading = 'Dashboard Statistics';

    protected static ?int $sort = 2;

    protected function getData(): array
    {
        $months = collect(range(0, 11))->map(function ($month) {
            return Carbon::now()->subMonths($month)->format('M');
        })->reverse();

        $employeeData = $months->map(function ($month) {
            return Employee::whereMonth('created_at', Carbon::parse($month)->month)
                ->whereYear('created_at', Carbon::parse($month)->year)
                ->count();
        });

        $permitData = $months->map(function ($month) {
            return Permit::whereMonth('created_at', Carbon::parse($month)->month)
                ->whereYear('created_at', Carbon::parse($month)->year)
                ->count();
        });

        return [
            'datasets' => [
                [
                    'label' => 'Employees',
                    'data' => $employeeData->toArray(),
                    'borderColor' => '#f59e0b',
                    'backgroundColor' => 'rgba(245, 158, 11, 0.1)',
                    'tension' => 0.4,
                ],
                [
                    'label' => 'Permits',
                    'data' => $permitData->toArray(),
                    'borderColor' => '#10b981',
                    'backgroundColor' => 'rgba(16, 185, 129, 0.1)',
                    'tension' => 0.4,
                ],
            ],
            'labels' => $months->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
