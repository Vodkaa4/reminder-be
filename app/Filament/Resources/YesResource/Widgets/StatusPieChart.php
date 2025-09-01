<?php

namespace App\Filament\Resources\YesResource\Widgets;

use Filament\Widgets\ChartWidget;

class StatusPieChart extends ChartWidget
{
    protected static ?string $heading = 'Chart';

    protected function getData(): array
    {
        return [
            //
        ];
    }

    protected function getType(): string
    {
        return 'pie';
    }
}
