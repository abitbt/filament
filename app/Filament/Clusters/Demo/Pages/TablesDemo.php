<?php

namespace App\Filament\Clusters\Demo\Pages;

use App\Enums\UserStatus;
use App\Filament\Clusters\Demo\DemoCluster;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Repeater\TableColumn;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Alignment;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\TextInputColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class TablesDemo extends Page implements HasForms, HasTable
{
    use InteractsWithForms;
    use InteractsWithTable;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-table-cells';

    protected static ?string $navigationLabel = 'Tables';

    protected static ?int $navigationSort = 3;

    protected static ?string $cluster = DemoCluster::class;

    protected string $view = 'filament.clusters.demo.pages.tables-demo';

    /**
     * @var array<string, mixed>
     */
    public array $invoiceData = [];

    public function mount(): void
    {
        $this->invoiceData = [
            'invoice_items' => [
                ['description' => 'Web Development Services', 'quantity' => 40, 'unit_price' => 150.00],
                ['description' => 'UI/UX Design', 'quantity' => 20, 'unit_price' => 125.00],
                ['description' => 'Project Management', 'quantity' => 10, 'unit_price' => 100.00],
            ],
        ];
    }

    public function invoiceForm(Schema $schema): Schema
    {
        return $schema
            ->statePath('invoiceData')
            ->components([
                Section::make('Invoice Line Items')
                    ->description('Add, edit, and remove line items. Totals calculate automatically.')
                    ->icon('heroicon-o-document-text')
                    ->schema([
                        Repeater::make('invoice_items')
                            ->hiddenLabel()
                            ->table([
                                TableColumn::make('Item Description')
                                    ->width('45%')
                                    ->markAsRequired(),
                                TableColumn::make('Qty')
                                    ->width('15%')
                                    ->alignment(Alignment::Center)
                                    ->markAsRequired(),
                                TableColumn::make('Unit Price')
                                    ->width('20%')
                                    ->alignment(Alignment::End)
                                    ->markAsRequired(),
                                TableColumn::make('Line Total')
                                    ->width('20%')
                                    ->alignment(Alignment::End),
                            ])
                            ->schema([
                                TextInput::make('description')
                                    ->placeholder('Enter item description...')
                                    ->required()
                                    ->live(onBlur: true),
                                TextInput::make('quantity')
                                    ->numeric()
                                    ->default(1)
                                    ->minValue(1)
                                    ->required()
                                    ->live(onBlur: true),
                                TextInput::make('unit_price')
                                    ->numeric()
                                    ->prefix('$')
                                    ->default(0)
                                    ->minValue(0)
                                    ->required()
                                    ->live(onBlur: true),
                                TextEntry::make('line_total')
                                    ->state(fn (Get $get): string => '$'.number_format(
                                        ((float) ($get('quantity') ?? 0)) * ((float) ($get('unit_price') ?? 0)),
                                        2
                                    )),
                            ])
                            ->defaultItems(1)
                            ->reorderable()
                            ->cloneable()
                            ->itemLabel(fn (array $state): string => $state['description'] ?? 'New Item'),
                    ]),
            ]);
    }

    public function getSubtotal(): float
    {
        /** @var array<int, array{description?: string, quantity?: int|float|string, unit_price?: int|float|string}> $items */
        $items = $this->invoiceData['invoice_items'] ?? [];

        return collect($items)->sum(function (array $item): float {
            return ((float) ($item['quantity'] ?? 0)) * ((float) ($item['unit_price'] ?? 0));
        });
    }

    public function getTax(): float
    {
        return $this->getSubtotal() * 0.10;
    }

    public function getGrandTotal(): float
    {
        return $this->getSubtotal() + $this->getTax();
    }

    /**
     * @return array<string, Schema>
     */
    protected function getForms(): array
    {
        return [
            'invoiceForm' => $this->invoiceForm(
                $this->makeSchema()->statePath('invoiceData'),
            ),
        ];
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(User::query())
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                ImageColumn::make('avatar')
                    ->label('Avatar')
                    ->circular()
                    ->defaultImageUrl(fn (User $record): string => 'https://ui-avatars.com/api/?name='.urlencode($record->name).'&color=7F9CF5&background=EBF4FF'),

                TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->copyable()
                    ->copyMessage('Email copied!')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('role.name')
                    ->label('Role')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Super Admin' => 'danger',
                        'Admin' => 'warning',
                        default => 'primary',
                    })
                    ->sortable(),

                // Editable Columns Demo - These columns allow inline editing
                TextInputColumn::make('name')
                    ->label('Name')
                    ->searchable()
                    ->sortable()
                    ->rules(['required', 'max:255'])
                    ->afterStateUpdated(fn (User $record, ?string $state) => Notification::make()
                        ->title('Name updated')
                        ->body("Changed to: {$state}")
                        ->success()
                        ->send()),

                SelectColumn::make('status')
                    ->label('Status')
                    ->options(UserStatus::class)
                    ->afterStateUpdated(fn (User $record, ?string $state) => Notification::make()
                        ->title('Status updated')
                        ->body("Changed to: {$state}")
                        ->success()
                        ->send()),

                ToggleColumn::make('is_verified')
                    ->label('Verified')
                    ->onColor('success')
                    ->offColor('danger')
                    ->onIcon('heroicon-o-check')
                    ->offIcon('heroicon-o-x-mark')
                    ->getStateUsing(fn (User $record): bool => $record->email_verified_at !== null)
                    ->afterStateUpdated(function (User $record, bool $state): void {
                        $record->email_verified_at = $state ? now() : null;
                        $record->save();

                        Notification::make()
                            ->title($state ? 'User verified' : 'User unverified')
                            ->success()
                            ->send();
                    }),

                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime('M j, Y')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('updated_at')
                    ->label('Updated')
                    ->since()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('role')
                    ->relationship('role', 'name')
                    ->preload()
                    ->label('Role'),

                SelectFilter::make('status')
                    ->options(UserStatus::class),

                TernaryFilter::make('email_verified_at')
                    ->label('Email Verified')
                    ->nullable()
                    ->placeholder('All Users')
                    ->trueLabel('Verified Only')
                    ->falseLabel('Unverified Only'),

                Filter::make('created_at')
                    ->schema([
                        DatePicker::make('created_from')
                            ->label('Created From')
                            ->native(false),
                        DatePicker::make('created_until')
                            ->label('Created Until')
                            ->native(false),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];

                        if ($data['created_from'] ?? null) {
                            $indicators[] = 'Created from '.$data['created_from'];
                        }

                        if ($data['created_until'] ?? null) {
                            $indicators[] = 'Created until '.$data['created_until'];
                        }

                        return $indicators;
                    }),
            ], layout: FiltersLayout::AboveContentCollapsible)
            ->recordActions([
                Action::make('view')
                    ->icon('heroicon-o-eye')
                    ->color('gray')
                    ->action(fn (User $record) => Notification::make()
                        ->title('Viewing: '.$record->name)
                        ->info()
                        ->send()),

                Action::make('edit')
                    ->icon('heroicon-o-pencil')
                    ->color('primary')
                    ->schema([
                        TextInput::make('name')
                            ->label('Name')
                            ->required()
                            ->default(fn (User $record): string => $record->name),
                        TextInput::make('email')
                            ->label('Email')
                            ->email()
                            ->required()
                            ->default(fn (User $record): string => $record->email),
                    ])
                    ->action(fn (User $record, array $data) => Notification::make()
                        ->title('Edit action triggered')
                        ->body('Name: '.$data['name'].', Email: '.$data['email'])
                        ->success()
                        ->send()),

                Action::make('delete')
                    ->icon('heroicon-o-trash')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Delete User')
                    ->modalDescription('Are you sure you want to delete this user? This action cannot be undone.')
                    ->action(fn (User $record) => Notification::make()
                        ->title('Delete action triggered')
                        ->body('Would delete: '.$record->name)
                        ->warning()
                        ->send()),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkAction::make('export')
                        ->label('Export Selected')
                        ->icon('heroicon-o-arrow-down-tray')
                        ->action(fn (Collection $records) => Notification::make()
                            ->title('Export triggered')
                            ->body('Would export '.$records->count().' records')
                            ->success()
                            ->send())
                        ->deselectRecordsAfterCompletion(),

                    BulkAction::make('activate')
                        ->label('Activate')
                        ->icon('heroicon-o-check')
                        ->color('success')
                        ->action(fn (Collection $records) => Notification::make()
                            ->title('Activate triggered')
                            ->body('Would activate '.$records->count().' users')
                            ->success()
                            ->send())
                        ->deselectRecordsAfterCompletion(),

                    BulkAction::make('delete')
                        ->label('Delete')
                        ->icon('heroicon-o-trash')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->action(fn (Collection $records) => Notification::make()
                            ->title('Bulk delete triggered')
                            ->body('Would delete '.$records->count().' users')
                            ->danger()
                            ->send())
                        ->deselectRecordsAfterCompletion(),
                ]),
            ])
            ->groups([
                Group::make('role.name')
                    ->label('Role')
                    ->collapsible(),
                Group::make('status')
                    ->label('Status')
                    ->collapsible(),
            ])
            ->defaultSort('created_at', 'desc')
            ->striped()
            ->paginated([10, 25, 50, 100])
            ->poll('60s');
    }
}
