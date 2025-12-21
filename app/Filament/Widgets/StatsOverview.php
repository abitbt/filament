<?php

namespace App\Filament\Widgets;

use App\Enums\UserStatus;
use App\Models\ActivityLog;
use App\Models\Role;
use App\Models\User;
use Carbon\Carbon;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Number;

/**
 * Stats overview widget showing key metrics.
 *
 * This widget demonstrates:
 * - StatsOverviewWidget with multiple Stat cards
 * - InteractsWithPageFilters for dashboard filter integration
 * - Mini charts (sparklines) on stats
 * - Dynamic descriptions with trends
 *
 * @see https://filamentphp.com/docs/4.x/widgets/stats-overview
 */
class StatsOverview extends StatsOverviewWidget
{
    use InteractsWithPageFilters;

    protected ?string $pollingInterval = '30s';

    protected static ?int $sort = 0;

    protected function getStats(): array
    {
        // Get date range from dashboard filters
        $startDate = ! is_null($this->pageFilters['startDate'] ?? null)
            ? Carbon::parse($this->pageFilters['startDate'])
            : null;

        $endDate = ! is_null($this->pageFilters['endDate'] ?? null)
            ? Carbon::parse($this->pageFilters['endDate'])
            : now();

        $statusFilter = $this->pageFilters['status'] ?? 'all';

        // Build user query with filters
        $userQuery = User::query();

        if ($startDate) {
            $userQuery->whereBetween('created_at', [$startDate->startOfDay(), $endDate->endOfDay()]);
        }

        if ($statusFilter === 'active') {
            $userQuery->where('status', UserStatus::Active);
        } elseif ($statusFilter === 'inactive') {
            $userQuery->where('status', UserStatus::Inactive);
        }

        $totalUsers = $userQuery->count();
        $activeUsers = User::where('status', UserStatus::Active)->count();

        // Activity count for the date range
        $activityQuery = ActivityLog::query();
        if ($startDate) {
            $activityQuery->whereBetween('created_at', [$startDate->startOfDay(), $endDate->endOfDay()]);
        } else {
            $activityQuery->whereDate('created_at', today());
        }
        $activityCount = $activityQuery->count();

        // Calculate period description
        $periodDesc = $startDate
            ? $startDate->format('M j').' - '.$endDate->format('M j')
            : 'Today';

        return [
            Stat::make('Total Users', $this->formatNumber($totalUsers))
                ->description($this->formatNumber($activeUsers).' active')
                ->descriptionIcon('heroicon-m-user-group')
                ->color('primary')
                ->chart($this->getUserTrend()),

            Stat::make('New Users', $this->formatNumber(
                $startDate
                    ? $totalUsers
                    : User::where('created_at', '>=', now()->subWeek())->count()
            ))
                ->description($startDate ? $periodDesc : 'Last 7 days')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success'),

            Stat::make('Roles', $this->formatNumber(Role::count()))
                ->description('Defined roles')
                ->descriptionIcon('heroicon-m-shield-check')
                ->color('warning'),

            Stat::make('Activity', $this->formatNumber($activityCount))
                ->description($periodDesc)
                ->descriptionIcon('heroicon-m-clipboard-document-list')
                ->color('info'),
        ];
    }

    /**
     * Format large numbers with suffixes (1.2k, 3.4m).
     */
    protected function formatNumber(int $number): string
    {
        if ($number < 1000) {
            return (string) Number::format($number, 0);
        }

        if ($number < 1000000) {
            return Number::format($number / 1000, 1).'k';
        }

        return Number::format($number / 1000000, 1).'m';
    }

    /**
     * Get user registration trend for sparkline chart.
     *
     * @return array<int>
     */
    protected function getUserTrend(): array
    {
        return User::query()
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->where('created_at', '>=', now()->subDays(7))
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('count')
            ->toArray();
    }
}
