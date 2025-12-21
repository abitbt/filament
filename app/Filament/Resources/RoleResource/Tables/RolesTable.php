<?php

namespace App\Filament\Resources\RoleResource\Tables;

use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class RolesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns(static::getColumns())
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->groupedBulkActions([
                DeleteBulkAction::make(),
            ])
            ->defaultSort('name');
    }

    /**
     * @return array<\Filament\Tables\Columns\Column>
     */
    public static function getColumns(): array
    {
        return [
            TextColumn::make('name')
                ->searchable()
                ->sortable(),

            TextColumn::make('slug')
                ->searchable()
                ->color('gray'),

            TextColumn::make('permissions_count')
                ->counts('permissions')
                ->badge()
                ->label('Permissions'),

            TextColumn::make('users_count')
                ->counts('users')
                ->badge()
                ->color('success')
                ->label('Users'),

            IconColumn::make('is_default')
                ->boolean()
                ->label('Default'),

            TextColumn::make('created_at')
                ->dateTime()
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: true),
        ];
    }
}
