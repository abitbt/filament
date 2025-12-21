<?php

namespace App\Filament\Widgets;

use App\Enums\UserStatus;
use App\Models\User;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;

/**
 * Line chart showing user registrations over time.
 *
 * This widget demonstrates:
 * - ChartWidget with getType() returning 'line'
 * - InteractsWithPageFilters for dashboard filter integration
 * - Dynamic data based on date range
 * - Chart.js options for styling
 *
 * @see https://filamentphp.com/docs/4.x/widgets/charts
 */
class UserGrowthChart extends ChartWidget
{
    use InteractsWithPageFilters;

    protected ?string $heading = 'User Registrations';

    protected ?string $description = 'New user sign-ups over time';

    protected static ?int $sort = 1;

    protected ?string $maxHeight = '300px';

    /**
     * Return 'line', 'bar', 'pie', 'doughnut', 'polarArea', 'radar', 'scatter', or 'bubble'.
     */
    protected function getType(): string
    {
        return 'line';
    }

    protected function getData(): array
    {
        // Get date range from dashboard filters (or default to last 30 days)
        $startDate = ! is_null($this->pageFilters['startDate'] ?? null)
            ? Carbon::parse($this->pageFilters['startDate'])
            : now()->subDays(30);

        $endDate = ! is_null($this->pageFilters['endDate'] ?? null)
            ? Carbon::parse($this->pageFilters['endDate'])
            : now();

        // Get status filter
        $statusFilter = $this->pageFilters['status'] ?? 'all';

        // Build query
        $query = User::query()
            ->whereBetween('created_at', [$startDate->startOfDay(), $endDate->endOfDay()]);

        // Apply status filter
        if ($statusFilter === 'active') {
            $query->where('status', UserStatus::Active);
        } elseif ($statusFilter === 'inactive') {
            $query->where('status', UserStatus::Inactive);
        }

        // Group by date
        $users = $query
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('count', 'date')
            ->toArray();

        // Fill in missing dates with zeros
        $period = CarbonPeriod::create($startDate, $endDate);
        $labels = [];
        $data = [];

        foreach ($period as $date) {
            $dateKey = $date->format('Y-m-d');
            $labels[] = $date->format('M j');
            $data[] = $users[$dateKey] ?? 0;
        }

        return [
            'datasets' => [
                [
                    'label' => 'New Users',
                    'data' => $data,
                    'fill' => 'start', // Creates an area chart effect
                    'tension' => 0.3, // Smooth curves
                ],
            ],
            'labels' => $labels,
        ];
    }

    /**
     * Chart.js options for additional customization.
     *
     * @return array<string, mixed>
     */
    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'display' => false,
                ],
            ],
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'ticks' => [
                        'precision' => 0, // No decimals for user counts
                    ],
                ],
            ],
        ];
    }
}
