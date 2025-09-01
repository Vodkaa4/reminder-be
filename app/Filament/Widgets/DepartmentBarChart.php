<?php

namespace App\Filament\Widgets;

use App\Models\Employee;
use Filament\Widgets\ChartWidget;

class DepartmentBarChart extends ChartWidget
{
    protected static ?string $heading = 'Employee Distribution by Department';

    protected static ?int $sort = 4;

    protected function getData(): array
    {
        $departments = Employee::select('dept')
            ->selectRaw('COUNT(*) as count')
            ->groupBy('dept')
            ->pluck('count', 'dept')
            ->toArray();

        return [
            'datasets' => [
                [
                    'label' => 'Employees',
                    'data' => array_values($departments),
                    'backgroundColor' => [
                        '#3b82f6', // Blue
                        '#10b981', // Green
                        '#f59e0b', // Amber
                        '#ef4444', // Red
                        '#8b5cf6', // Purple
                        '#06b6d4', // Cyan
                    ],
                    'borderColor' => '#ffffff',
                    'borderWidth' => 1,
                ],
            ],
            'labels' => array_keys($departments),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
