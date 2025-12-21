<?php

namespace App\Filament\Clusters\Demo\Pages;

use App\Filament\Clusters\Demo\DemoCluster;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Page;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Flex;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Wizard;
use Filament\Schemas\Schema;

class FormLayoutsDemo extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-squares-2x2';

    protected static ?string $navigationLabel = 'Form Layouts';

    protected static ?int $navigationSort = 2;

    protected static ?string $cluster = DemoCluster::class;

    protected string $view = 'filament.clusters.demo.pages.form-layouts-demo';

    /**
     * @var array<string, mixed>
     */
    public array $data = [];

    public function mount(): void
    {
        $this->data = [
            'section_name' => 'John Doe',
            'section_email' => 'john@example.com',
            'tab_personal_name' => 'Jane Smith',
            'tab_personal_email' => 'jane@example.com',
            'tab_work_company' => 'Acme Inc',
            'tab_work_position' => 'Developer',
            'tab_settings_notifications' => true,
            'tab_settings_newsletter' => false,
            'wizard_account_username' => 'johndoe',
            'wizard_account_email' => 'john@example.com',
            'wizard_profile_name' => 'John Doe',
            'wizard_profile_bio' => 'Software developer',
            'wizard_preferences_theme' => 'dark',
            'grid_field_1' => 'Field 1',
            'grid_field_2' => 'Field 2',
            'grid_field_3' => 'Field 3',
            'grid_field_4' => 'Field 4',
            'fieldset_address' => '123 Main St',
            'fieldset_city' => 'New York',
            'fieldset_state' => 'NY',
            'fieldset_zip' => '10001',
            'split_main' => 'Main content area',
            'split_aside' => 'Sidebar content',
            'repeater_items' => [
                ['name' => 'Item 1', 'quantity' => 5, 'price' => 10.00],
                ['name' => 'Item 2', 'quantity' => 3, 'price' => 25.50],
            ],
        ];
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->statePath('data')
            ->components([
                Section::make('Section Component')
                    ->description('Sections group related fields together with optional headers')
                    ->icon('heroicon-o-rectangle-group')
                    ->collapsible()
                    ->columns(2)
                    ->schema([
                        TextInput::make('section_name')
                            ->label('Name'),
                        TextInput::make('section_email')
                            ->label('Email')
                            ->email(),
                    ]),

                Section::make('Tabs Component')
                    ->description('Tabs organize content into switchable panels')
                    ->schema([
                        Tabs::make('demo_tabs')
                            ->tabs([
                                Tabs\Tab::make('Personal')
                                    ->icon('heroicon-o-user')
                                    ->schema([
                                        TextInput::make('tab_personal_name')
                                            ->label('Full Name'),
                                        TextInput::make('tab_personal_email')
                                            ->label('Email')
                                            ->email(),
                                    ])
                                    ->columns(2),

                                Tabs\Tab::make('Work')
                                    ->icon('heroicon-o-briefcase')
                                    ->badge('2')
                                    ->schema([
                                        TextInput::make('tab_work_company')
                                            ->label('Company'),
                                        TextInput::make('tab_work_position')
                                            ->label('Position'),
                                    ])
                                    ->columns(2),

                                Tabs\Tab::make('Settings')
                                    ->icon('heroicon-o-cog-6-tooth')
                                    ->schema([
                                        Toggle::make('tab_settings_notifications')
                                            ->label('Enable Notifications'),
                                        Toggle::make('tab_settings_newsletter')
                                            ->label('Subscribe to Newsletter'),
                                    ]),
                            ]),
                    ]),

                Section::make('Wizard Component')
                    ->description('Multi-step forms with validation between steps')
                    ->schema([
                        Wizard::make([
                            Wizard\Step::make('Account')
                                ->icon('heroicon-o-user-circle')
                                ->description('Set up your account')
                                ->schema([
                                    TextInput::make('wizard_account_username')
                                        ->label('Username')
                                        ->required(),
                                    TextInput::make('wizard_account_email')
                                        ->label('Email')
                                        ->email()
                                        ->required(),
                                ])
                                ->columns(2),

                            Wizard\Step::make('Profile')
                                ->icon('heroicon-o-identification')
                                ->description('Complete your profile')
                                ->schema([
                                    TextInput::make('wizard_profile_name')
                                        ->label('Display Name'),
                                    TextInput::make('wizard_profile_bio')
                                        ->label('Bio'),
                                ])
                                ->columns(2),

                            Wizard\Step::make('Preferences')
                                ->icon('heroicon-o-cog')
                                ->description('Set your preferences')
                                ->schema([
                                    Select::make('wizard_preferences_theme')
                                        ->label('Theme')
                                        ->options([
                                            'light' => 'Light',
                                            'dark' => 'Dark',
                                            'system' => 'System',
                                        ])
                                        ->native(false),
                                ]),
                        ]),
                    ]),

                Section::make('Grid Component')
                    ->description('Responsive grid layouts with configurable columns')
                    ->schema([
                        Grid::make([
                            'default' => 1,
                            'sm' => 2,
                            'md' => 3,
                            'lg' => 4,
                        ])
                            ->schema([
                                TextInput::make('grid_field_1')
                                    ->label('Field 1'),
                                TextInput::make('grid_field_2')
                                    ->label('Field 2'),
                                TextInput::make('grid_field_3')
                                    ->label('Field 3'),
                                TextInput::make('grid_field_4')
                                    ->label('Field 4'),
                            ]),
                    ]),

                Section::make('Fieldset Component')
                    ->description('Fieldsets group related fields with a border')
                    ->schema([
                        Fieldset::make('Address')
                            ->schema([
                                TextInput::make('fieldset_address')
                                    ->label('Street Address')
                                    ->columnSpanFull(),
                                TextInput::make('fieldset_city')
                                    ->label('City'),
                                TextInput::make('fieldset_state')
                                    ->label('State'),
                                TextInput::make('fieldset_zip')
                                    ->label('ZIP Code'),
                            ])
                            ->columns(3),
                    ]),

                Section::make('Flex Component')
                    ->description('Flexible side-by-side layout for main content and sidebar')
                    ->schema([
                        Flex::make([
                            Section::make('Main Content')
                                ->schema([
                                    TextInput::make('split_main')
                                        ->label('Main Field')
                                        ->helperText('This is the main content area'),
                                ])
                                ->grow(),
                            Section::make('Sidebar')
                                ->schema([
                                    TextInput::make('split_aside')
                                        ->label('Sidebar Field'),
                                ])
                                ->grow(false),
                        ]),
                    ]),

                Section::make('Group Component')
                    ->description('Groups multiple components without visual wrapper')
                    ->schema([
                        Group::make()
                            ->schema([
                                TextInput::make('group_first')
                                    ->label('First Field'),
                                TextInput::make('group_second')
                                    ->label('Second Field'),
                            ])
                            ->columns(2),
                    ]),

                Section::make('Repeater Component')
                    ->description('Dynamic lists of repeatable field groups')
                    ->schema([
                        Repeater::make('repeater_items')
                            ->label('Order Items')
                            ->schema([
                                TextInput::make('name')
                                    ->label('Item Name')
                                    ->required(),
                                TextInput::make('quantity')
                                    ->label('Qty')
                                    ->numeric()
                                    ->default(1)
                                    ->minValue(1),
                                TextInput::make('price')
                                    ->label('Unit Price')
                                    ->numeric()
                                    ->prefix('$'),
                            ])
                            ->columns(3)
                            ->defaultItems(1)
                            ->reorderable()
                            ->collapsible()
                            ->itemLabel(fn (array $state): string => $state['name'] ?? 'New Item'),
                    ]),
            ]);
    }
}
