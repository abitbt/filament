<?php

namespace App\Filament\Clusters\Demo\Widgets;

use Filament\Widgets\ChartWidget;

class DemoPieChartWidget extends ChartWidget
{
    protected ?string $heading = 'Traffic Sources';

    protected ?string $description = 'Where visitors come from';

    protected static ?int $sort = 4;

    protected ?string $maxHeight = '300px';

    protected int|string|array $columnSpan = 1;

    protected function getType(): string
    {
        return 'doughnut';
    }

    protected function getData(): array
    {
        return [
            'datasets' => [
                [
                    'data' => [42, 28, 18, 8, 4],
                    'backgroundColor' => [
                        'rgba(99, 102, 241, 0.8)',
                        'rgba(16, 185, 129, 0.8)',
                        'rgba(245, 158, 11, 0.8)',
                        'rgba(244, 63, 94, 0.8)',
                        'rgba(139, 92, 246, 0.8)',
                    ],
                    'borderWidth' => 0,
                ],
            ],
            'labels' => ['Organic Search', 'Direct', 'Social Media', 'Referral', 'Email'],
        ];
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'right',
                ],
            ],
            'cutout' => '60%',
        ];
    }
}
