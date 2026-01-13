<?php

namespace App\Filament\Widgets;

use App\Enums\ActivityEvent;
use App\Filament\Widgets\Concerns\HasDateRangeFilter;
use App\Models\ActivityLog;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;

/**
 * Doughnut chart showing activity distribution by event type.
 *
 * This widget demonstrates:
 * - ChartWidget with getType() returning 'doughnut'
 * - Using enums for chart labels and colors
 * - InteractsWithPageFilters for dashboard filter integration
 * - Custom color palette
 *
 * @see https://filamentphp.com/docs/4.x/widgets/charts
 */
class ActivityBreakdownChart extends ChartWidget
{
    use HasDateRangeFilter;
    use InteractsWithPageFilters;

    protected ?string $heading = 'Activity Breakdown';

    protected ?string $description = 'Distribution of activity events';

    protected static ?int $sort = 3;

    protected ?string $maxHeight = '300px';

    /**
     * Return 'doughnut' for a ring chart, 'pie' for a filled circle.
     */
    protected function getType(): string
    {
        return 'doughnut';
    }

    protected function getData(): array
    {
        $startDate = $this->getFilterStartDateOrDefault(30);
        $endDate = $this->getFilterEndDate();

        // Count activities by event type
        $activities = ActivityLog::query()
            ->whereBetween('created_at', [$startDate->startOfDay(), $endDate->endOfDay()])
            ->selectRaw('event, COUNT(*) as count')
            ->groupBy('event')
            ->pluck('count', 'event')
            ->toArray();

        // Build chart data using enum cases
        $labels = [];
        $data = [];
        $backgroundColors = [];

        foreach (ActivityEvent::cases() as $event) {
            $count = $activities[$event->value] ?? 0;

            if ($count > 0) {
                $labels[] = $event->getLabel();
                $data[] = $count;
                $backgroundColors[] = $this->getEventColor($event);
            }
        }

        // If no data, show placeholder
        if (empty($data)) {
            $labels = ['No Activity'];
            $data = [1];
            $backgroundColors = ['#9CA3AF']; // Gray
        }

        return [
            'datasets' => [
                [
                    'data' => $data,
                    'backgroundColor' => $backgroundColors,
                    'borderWidth' => 0,
                ],
            ],
            'labels' => $labels,
        ];
    }

    /**
     * Map activity events to colors.
     */
    protected function getEventColor(ActivityEvent $event): string
    {
        return match ($event) {
            ActivityEvent::Created => '#10B981', // Emerald (success)
            ActivityEvent::Updated => '#3B82F6', // Blue (info)
            ActivityEvent::Deleted => '#EF4444', // Red (danger)
            ActivityEvent::Login => '#8B5CF6',   // Purple
            ActivityEvent::Logout => '#F59E0B',  // Amber (warning)
        };
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
                    'position' => 'bottom',
                ],
            ],
            'cutout' => '60%', // Size of the hole in the middle
        ];
    }
}
