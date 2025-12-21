<?php

namespace App\Filament\Clusters\Demo\Widgets;

use Filament\Widgets\ChartWidget;

class DemoBarChartWidget extends ChartWidget
{
    protected ?string $heading = 'Sales by Category';

    protected ?string $description = 'Product sales breakdown';

    protected static ?int $sort = 3;

    protected ?string $maxHeight = '300px';

    protected int|string|array $columnSpan = 1;

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getData(): array
    {
        return [
            'datasets' => [
                [
                    'label' => 'Sales',
                    'data' => [42, 35, 28, 22, 18, 15],
                    'backgroundColor' => [
                        'rgba(99, 102, 241, 0.8)',
                        'rgba(16, 185, 129, 0.8)',
                        'rgba(245, 158, 11, 0.8)',
                        'rgba(244, 63, 94, 0.8)',
                        'rgba(139, 92, 246, 0.8)',
                        'rgba(6, 182, 212, 0.8)',
                    ],
                    'borderRadius' => 6,
                ],
            ],
            'labels' => ['Electronics', 'Clothing', 'Home & Garden', 'Sports', 'Books', 'Toys'],
        ];
    }

    protected function getOptions(): array
    {
        return [
            'indexAxis' => 'y',
            'plugins' => [
                'legend' => [
                    'display' => false,
                ],
            ],
            'scales' => [
                'x' => [
                    'beginAtZero' => true,
                ],
            ],
        ];
    }
}
