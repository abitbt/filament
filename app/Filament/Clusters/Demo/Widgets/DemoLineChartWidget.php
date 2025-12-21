<?php

namespace App\Filament\Clusters\Demo\Widgets;

use Filament\Widgets\ChartWidget;

class DemoLineChartWidget extends ChartWidget
{
    protected ?string $heading = 'Revenue Over Time';

    protected ?string $description = 'Monthly revenue for the current year';

    protected static ?int $sort = 2;

    protected ?string $maxHeight = '300px';

    protected function getType(): string
    {
        return 'line';
    }

    protected function getData(): array
    {
        return [
            'datasets' => [
                [
                    'label' => 'Revenue',
                    'data' => [4500, 5200, 4800, 6100, 5900, 7200, 6800, 8100, 7500, 8900, 9200, 10500],
                    'borderColor' => '#6366f1',
                    'backgroundColor' => 'rgba(99, 102, 241, 0.1)',
                    'fill' => true,
                    'tension' => 0.4,
                ],
                [
                    'label' => 'Expenses',
                    'data' => [3200, 3400, 3100, 3800, 3600, 4200, 4000, 4500, 4200, 4800, 5100, 5400],
                    'borderColor' => '#f43f5e',
                    'backgroundColor' => 'rgba(244, 63, 94, 0.1)',
                    'fill' => true,
                    'tension' => 0.4,
                ],
            ],
            'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
        ];
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'top',
                ],
            ],
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                ],
            ],
        ];
    }
}
