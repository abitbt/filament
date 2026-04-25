<?php

namespace App\Filament\Resources\UserResource\RelationManagers;

use App\Enums\ActivityEvent;
use BackedEnum;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class ActivityLogsRelationManager extends RelationManager
{
    protected static string $relationship = 'activityLogs';

    protected static ?string $title = 'Activity Logs';

    protected static string|BackedEnum|null $icon = 'heroicon-o-clipboard-document-list';

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('description')
            ->columns([
                TextColumn::make('event')
                    ->badge(),

                TextColumn::make('description')
                    ->limit(60)
                    ->searchable(),

                TextColumn::make('ip_address')
                    ->label('IP')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('created_at')
                    ->since()
                    ->label('When')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('event')
                    ->options(ActivityEvent::class),
            ])
            ->headerActions([])
            ->recordActions([])
            ->groupedBulkActions([])
            ->defaultSort('created_at', 'desc');
    }

    public static function canViewForRecord(Model $ownerRecord, string $pageClass): bool
    {
        return auth()->user()?->can('activity_logs.read') ?? false;
    }
}
