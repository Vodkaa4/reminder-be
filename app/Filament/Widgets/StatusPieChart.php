<?php

namespace App\Filament\Widgets;

use App\Models\Permit;
use Filament\Widgets\ChartWidget;

class StatusPieChart extends ChartWidget
{
    protected static ?string $heading = 'Permit Status Distribution';

    protected static ?int $sort = 3;

    protected function getData(): array
    {
        $activeCount = Permit::where('status', 'active')->count();
        $renewalCount = Permit::where('status', 'renewal')->count();
        $expiredCount = Permit::where('status', 'expired')->count();

        return [
            'datasets' => [
                [
                    'data' => [$activeCount, $renewalCount, $expiredCount],
                    'backgroundColor' => [
                        '#10b981', // Green for active
                        '#f59e0b', // Amber for renewal
                        '#ef4444', // Red for expired
                    ],
                    'borderColor' => '#ffffff',
                    'borderWidth' => 2,
                ],
            ],
            'labels' => ['Active', 'Renewal', 'Expired'],
        ];
    }

    protected function getType(): string
    {
        return 'pie';
    }
}
