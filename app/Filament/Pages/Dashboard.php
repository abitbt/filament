<?php

namespace App\Filament\Pages;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;

/**
 * Custom Dashboard page with filters.
 *
 * This demonstrates how to create a dashboard with global filters
 * that widgets can react to using the InteractsWithPageFilters trait.
 *
 * @see https://filamentphp.com/docs/4.x/widgets/overview#filtering-widget-data
 */
class Dashboard extends BaseDashboard
{
    use BaseDashboard\Concerns\HasFiltersForm;

    /**
     * Define the dashboard filter form.
     *
     * Widgets can access these values via:
     *   - $this->pageFilters['startDate']
     *   - $this->pageFilters['endDate']
     *   - $this->pageFilters['status']
     */
    public function filtersForm(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->schema([
                        DatePicker::make('startDate')
                            ->label('From')
                            ->maxDate(fn (Get $get) => $get('endDate') ?: now())
                            ->placeholder('Start date'),
                        DatePicker::make('endDate')
                            ->label('To')
                            ->minDate(fn (Get $get) => $get('startDate') ?: null)
                            ->maxDate(now())
                            ->placeholder('End date'),
                        Select::make('status')
                            ->label('User Status')
                            ->options([
                                'all' => 'All Users',
                                'active' => 'Active Only',
                                'inactive' => 'Inactive Only',
                            ])
                            ->default('all'),
                    ])
                    ->columns(3)
                    ->columnSpanFull(),
            ]);
    }
}
