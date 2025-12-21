<?php

namespace App\Filament\Resources\ActivityLogResource\Tables;

use App\Enums\ActivityEvent;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ActivityLogsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns(static::getColumns())
            ->filters(static::getFilters())
            ->recordActions([
                ViewAction::make(),
                DeleteAction::make(),
            ])
            ->groupedBulkActions([
                DeleteBulkAction::make(),
            ])
            ->defaultSort('created_at', 'desc');
    }

    /**
     * @return array<\Filament\Tables\Columns\Column>
     */
    public static function getColumns(): array
    {
        return [
            TextColumn::make('user.name')
                ->label('User')
                ->searchable()
                ->sortable()
                ->placeholder('System'),

            TextColumn::make('event')
                ->badge(),

            TextColumn::make('description')
                ->limit(50)
                ->searchable(),

            TextColumn::make('subject_type')
                ->formatStateUsing(fn (?string $state): string => $state ? class_basename($state) : '-')
                ->label('Model'),

            TextColumn::make('ip_address')
                ->toggleable(isToggledHiddenByDefault: true),

            TextColumn::make('created_at')
                ->dateTime()
                ->sortable()
                ->label('Date'),
        ];
    }

    /**
     * @return array<\Filament\Tables\Filters\BaseFilter>
     */
    public static function getFilters(): array
    {
        return [
            SelectFilter::make('event')
                ->options(ActivityEvent::class),

            SelectFilter::make('user')
                ->relationship('user', 'name')
                ->searchable()
                ->preload(),
        ];
    }
}
