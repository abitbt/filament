<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\ActivityLogResource;
use App\Models\ActivityLog;
use Filament\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

/**
 * Table widget showing latest activity log entries.
 *
 * This widget demonstrates:
 * - TableWidget with a custom query
 * - Record actions linking to resource pages
 * - Header actions for navigation
 * - Relationship eager loading
 *
 * @see https://filamentphp.com/docs/4.x/widgets/tables
 */
class LatestActivityWidget extends BaseWidget
{
    protected int|string|array $columnSpan = 'full';

    protected static ?int $sort = 2;

    protected static ?string $heading = 'Latest Activity';

    /**
     * Header actions appear in the widget header.
     *
     * @return array<Action>
     */
    protected function getHeaderActions(): array
    {
        return [
            Action::make('viewAll')
                ->label('View All')
                ->url(ActivityLogResource::getUrl('index'))
                ->icon('heroicon-o-arrow-right')
                ->color('gray')
                ->size('sm'),
        ];
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                ActivityLog::query()
                    ->with('user')
                    ->latest('created_at')
                    ->limit(10)
            )
            ->columns([
                TextColumn::make('user.name')
                    ->label('User')
                    ->placeholder('System')
                    ->searchable(),

                TextColumn::make('event')
                    ->badge(),

                TextColumn::make('description')
                    ->limit(50)
                    ->tooltip(fn (ActivityLog $record): string => $record->description),

                TextColumn::make('ip_address')
                    ->label('IP')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('created_at')
                    ->since()
                    ->label('When')
                    ->sortable(),
            ])
            ->recordActions([
                Action::make('view')
                    ->url(fn (ActivityLog $record): string => ActivityLogResource::getUrl('view', ['record' => $record]))
                    ->icon('heroicon-o-eye')
                    ->color('gray'),
            ])
            ->paginated(false)
            ->poll('30s'); // Auto-refresh every 30 seconds
    }
}
