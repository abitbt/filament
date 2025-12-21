<?php

namespace App\Filament\Clusters\Demo\Pages;

use App\Filament\Clusters\Demo\DemoCluster;
use App\Filament\Clusters\Demo\Widgets\DemoBarChartWidget;
use App\Filament\Clusters\Demo\Widgets\DemoLineChartWidget;
use App\Filament\Clusters\Demo\Widgets\DemoPieChartWidget;
use App\Filament\Clusters\Demo\Widgets\DemoStatsWidget;
use Filament\Pages\Page;

class WidgetsDemo extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-chart-bar';

    protected static ?string $navigationLabel = 'Widgets';

    protected static ?int $navigationSort = 5;

    protected static ?string $cluster = DemoCluster::class;

    protected string $view = 'filament.clusters.demo.pages.widgets-demo';

    /**
     * @return array<class-string>
     */
    protected function getHeaderWidgets(): array
    {
        return [
            DemoStatsWidget::class,
        ];
    }

    /**
     * @return array<class-string>
     */
    protected function getFooterWidgets(): array
    {
        return [
            DemoLineChartWidget::class,
            DemoBarChartWidget::class,
            DemoPieChartWidget::class,
        ];
    }

    public function getFooterWidgetsColumns(): int|array
    {
        return [
            'default' => 1,
            'md' => 2,
            'xl' => 3,
        ];
    }
}
