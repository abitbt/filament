<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ActivityLogResource\Pages;
use App\Filament\Resources\ActivityLogResource\Tables\ActivityLogsTable;
use App\Models\ActivityLog;
use BackedEnum;
use Filament\Infolists\Components\KeyValueEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use UnitEnum;

class ActivityLogResource extends Resource
{
    protected static ?string $model = ActivityLog::class;

    protected static string|BackedEnum|null $navigationIcon = null;

    protected static string|UnitEnum|null $navigationGroup = 'System';

    protected static ?int $navigationSort = 10;

    protected static ?string $modelLabel = 'Activity Log';

    protected static ?string $pluralModelLabel = 'Activity Logs';

    public static function canCreate(): bool
    {
        return false;
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Activity Details')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('user.name')
                            ->label('User')
                            ->placeholder('System'),

                        TextEntry::make('event')
                            ->badge(),

                        TextEntry::make('description'),

                        TextEntry::make('created_at')
                            ->dateTime()
                            ->label('Date'),

                        TextEntry::make('ip_address'),

                        TextEntry::make('user_agent')
                            ->columnSpanFull()
                            ->limit(100),
                    ]),

                Section::make('Subject')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('subject_type')
                            ->formatStateUsing(fn (?string $state): string => $state ? class_basename($state) : '-')
                            ->label('Model'),

                        TextEntry::make('subject_id')
                            ->label('ID'),
                    ]),

                Section::make('Changes')
                    ->visible(fn (ActivityLog $record): bool => ! empty($record->properties))
                    ->schema([
                        KeyValueEntry::make('properties.old')
                            ->label('Old Values'),

                        KeyValueEntry::make('properties.new')
                            ->label('New Values'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return ActivityLogsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListActivityLogs::route('/'),
            'view' => Pages\ViewActivityLog::route('/{record}'),
        ];
    }
}
