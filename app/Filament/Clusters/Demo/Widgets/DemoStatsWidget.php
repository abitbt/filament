<?php

namespace App\Filament\Clusters\Demo\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class DemoStatsWidget extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        return [
            Stat::make('Total Revenue', '$192,340')
                ->description('32% increase')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success')
                ->chart([7, 3, 4, 5, 6, 3, 5, 8, 10, 12, 15, 14]),

            Stat::make('New Customers', '1,245')
                ->description('12% increase')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('primary')
                ->chart([3, 5, 7, 6, 8, 9, 10, 12, 11, 14, 15, 18]),

            Stat::make('Bounce Rate', '21.3%')
                ->description('3% decrease')
                ->descriptionIcon('heroicon-m-arrow-trending-down')
                ->color('danger')
                ->chart([15, 14, 13, 12, 14, 11, 10, 9, 10, 8, 7, 6]),

            Stat::make('Avg. Session', '2m 34s')
                ->description('Stable')
                ->descriptionIcon('heroicon-m-minus')
                ->color('warning'),
        ];
    }
}
