<?php

namespace App\Filament\Clusters\Demo\Pages;

use App\Filament\Clusters\Demo\DemoCluster;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\KeyValueEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Concerns\InteractsWithInfolists;
use Filament\Infolists\Contracts\HasInfolists;
use Filament\Pages\Page;
use Filament\Schemas\Components\Flex;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\IconPosition;

class InfolistsDemo extends Page implements HasInfolists
{
    use InteractsWithInfolists;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationLabel = 'Infolists';

    protected static ?int $navigationSort = 6;

    protected static ?string $cluster = DemoCluster::class;

    protected string $view = 'filament.clusters.demo.pages.infolists-demo';

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->state($this->getDemoData())
            ->components([
                Section::make('Text Entries')
                    ->description('Various ways to display text data')
                    ->columns(3)
                    ->schema([
                        TextEntry::make('name')
                            ->label('Name')
                            ->weight(FontWeight::Bold)
                            ->size('lg'),

                        TextEntry::make('email')
                            ->label('Email')
                            ->icon('heroicon-o-envelope')
                            ->iconPosition(IconPosition::Before)
                            ->copyable()
                            ->copyMessage('Email copied!'),

                        TextEntry::make('phone')
                            ->label('Phone')
                            ->icon('heroicon-o-phone'),

                        TextEntry::make('status')
                            ->label('Status')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'active' => 'success',
                                'pending' => 'warning',
                                'inactive' => 'danger',
                                default => 'gray',
                            }),

                        TextEntry::make('role')
                            ->label('Role')
                            ->badge()
                            ->color('primary'),

                        TextEntry::make('website')
                            ->label('Website')
                            ->url(fn (string $state): string => $state)
                            ->openUrlInNewTab(),

                        TextEntry::make('created_at')
                            ->label('Member Since')
                            ->dateTime('F j, Y'),

                        TextEntry::make('last_login')
                            ->label('Last Login')
                            ->since(),

                        TextEntry::make('balance')
                            ->label('Balance')
                            ->money('USD'),
                    ]),

                Section::make('Icon Entries')
                    ->description('Boolean and icon-based displays')
                    ->columns(4)
                    ->schema([
                        IconEntry::make('is_verified')
                            ->label('Verified')
                            ->boolean(),

                        IconEntry::make('has_subscription')
                            ->label('Subscription')
                            ->boolean()
                            ->trueIcon('heroicon-o-check-badge')
                            ->falseIcon('heroicon-o-x-circle')
                            ->trueColor('success')
                            ->falseColor('danger'),

                        IconEntry::make('notifications_enabled')
                            ->label('Notifications')
                            ->icon(fn (bool $state): string => $state
                                ? 'heroicon-o-bell'
                                : 'heroicon-o-bell-slash')
                            ->color(fn (bool $state): string => $state
                                ? 'success'
                                : 'gray'),

                        IconEntry::make('priority')
                            ->label('Priority')
                            ->icon(fn (string $state): string => match ($state) {
                                'high' => 'heroicon-o-arrow-up-circle',
                                'medium' => 'heroicon-o-minus-circle',
                                'low' => 'heroicon-o-arrow-down-circle',
                                default => 'heroicon-o-question-mark-circle',
                            })
                            ->color(fn (string $state): string => match ($state) {
                                'high' => 'danger',
                                'medium' => 'warning',
                                'low' => 'success',
                                default => 'gray',
                            }),
                    ]),

                Section::make('Image Entry')
                    ->description('Display images and avatars')
                    ->schema([
                        Flex::make([
                            ImageEntry::make('avatar')
                                ->label('Profile Photo')
                                ->circular()
                                ->imageSize(100)
                                ->defaultImageUrl('https://ui-avatars.com/api/?name=John+Doe&size=200'),

                            Group::make([
                                TextEntry::make('name')
                                    ->label('Full Name')
                                    ->weight(FontWeight::Bold),
                                TextEntry::make('bio')
                                    ->label('Biography')
                                    ->prose()
                                    ->markdown(),
                            ])->grow(),
                        ]),
                    ]),

                Section::make('Key-Value Entry')
                    ->description('Display structured key-value data')
                    ->schema([
                        KeyValueEntry::make('metadata')
                            ->label('Account Metadata')
                            ->columnSpanFull(),
                    ]),

                Section::make('Repeatable Entry')
                    ->description('Display lists of related data')
                    ->schema([
                        RepeatableEntry::make('orders')
                            ->label('Recent Orders')
                            ->columns(4)
                            ->schema([
                                TextEntry::make('id')
                                    ->label('Order ID')
                                    ->weight(FontWeight::Bold),
                                TextEntry::make('product')
                                    ->label('Product'),
                                TextEntry::make('amount')
                                    ->label('Amount')
                                    ->money('USD'),
                                TextEntry::make('status')
                                    ->label('Status')
                                    ->badge()
                                    ->color(fn (string $state): string => match ($state) {
                                        'completed' => 'success',
                                        'processing' => 'warning',
                                        'cancelled' => 'danger',
                                        default => 'gray',
                                    }),
                            ]),
                    ]),

                Section::make('Layout Components')
                    ->description('Tabs, grids, and flex for organizing content')
                    ->schema([
                        Tabs::make('Details')
                            ->tabs([
                                Tabs\Tab::make('Contact')
                                    ->icon('heroicon-o-user')
                                    ->schema([
                                        Grid::make(2)
                                            ->schema([
                                                TextEntry::make('address')
                                                    ->label('Address'),
                                                TextEntry::make('city')
                                                    ->label('City'),
                                                TextEntry::make('country')
                                                    ->label('Country'),
                                                TextEntry::make('postal_code')
                                                    ->label('Postal Code'),
                                            ]),
                                    ]),

                                Tabs\Tab::make('Preferences')
                                    ->icon('heroicon-o-cog-6-tooth')
                                    ->schema([
                                        Grid::make(3)
                                            ->schema([
                                                TextEntry::make('language')
                                                    ->label('Language'),
                                                TextEntry::make('timezone')
                                                    ->label('Timezone'),
                                                TextEntry::make('currency')
                                                    ->label('Currency'),
                                            ]),
                                    ]),

                                Tabs\Tab::make('Activity')
                                    ->icon('heroicon-o-clock')
                                    ->badge('3')
                                    ->schema([
                                        TextEntry::make('activity_summary')
                                            ->label('Recent Activity')
                                            ->html(),
                                    ]),
                            ]),
                    ]),
            ]);
    }

    /**
     * @return array<string, mixed>
     */
    protected function getDemoData(): array
    {
        return [
            'name' => 'John Doe',
            'email' => 'john.doe@example.com',
            'phone' => '+1 (555) 123-4567',
            'status' => 'active',
            'role' => 'Administrator',
            'website' => 'https://example.com',
            'created_at' => now()->subMonths(6),
            'last_login' => now()->subHours(2),
            'balance' => 15234.50,
            'is_verified' => true,
            'has_subscription' => true,
            'notifications_enabled' => true,
            'priority' => 'high',
            'avatar' => null,
            'bio' => 'Senior software engineer with **10+ years** of experience in web development. Passionate about clean code and user experience.',
            'metadata' => [
                'Account ID' => 'ACC-123456',
                'Plan' => 'Enterprise',
                'API Calls' => '45,230 / 100,000',
                'Storage Used' => '2.4 GB / 10 GB',
                'Last Payment' => 'Dec 1, 2024',
            ],
            'orders' => [
                ['id' => 'ORD-001', 'product' => 'Pro Subscription', 'amount' => 99.00, 'status' => 'completed'],
                ['id' => 'ORD-002', 'product' => 'Add-on Pack', 'amount' => 29.00, 'status' => 'completed'],
                ['id' => 'ORD-003', 'product' => 'Support Hours', 'amount' => 150.00, 'status' => 'processing'],
            ],
            'address' => '123 Main Street, Suite 100',
            'city' => 'San Francisco',
            'country' => 'United States',
            'postal_code' => '94102',
            'language' => 'English (US)',
            'timezone' => 'America/Los_Angeles (PST)',
            'currency' => 'USD ($)',
            'activity_summary' => '<ul><li>Logged in from new device</li><li>Updated profile settings</li><li>Submitted support ticket #4521</li></ul>',
        ];
    }
}
