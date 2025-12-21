<?php

namespace App\Filament\Resources\UserResource\Tables;

use App\Enums\UserStatus;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns(static::getColumns())
            ->filters(static::getFilters())
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
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
            ImageColumn::make('avatar')
                ->circular()
                ->defaultImageUrl(fn ($record): string => 'https://ui-avatars.com/api/?name='.urlencode($record->name).'&color=7F9CF5&background=EBF4FF'),

            TextColumn::make('name')
                ->searchable()
                ->sortable(),

            TextColumn::make('email')
                ->searchable()
                ->sortable(),

            TextColumn::make('role.name')
                ->badge()
                ->sortable(),

            TextColumn::make('status')
                ->badge(),

            TextColumn::make('created_at')
                ->dateTime()
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: true),

            TextColumn::make('updated_at')
                ->dateTime()
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: true),
        ];
    }

    /**
     * @return array<\Filament\Tables\Filters\BaseFilter>
     */
    public static function getFilters(): array
    {
        return [
            SelectFilter::make('role')
                ->relationship('role', 'name'),

            SelectFilter::make('status')
                ->options(UserStatus::class),
        ];
    }
}
