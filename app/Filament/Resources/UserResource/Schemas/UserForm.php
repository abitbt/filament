<?php

namespace App\Filament\Resources\UserResource\Schemas;

use App\Enums\UserStatus;
use App\Models\User;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(3)
                    ->columnSpanFull()
                    ->schema([
                        Section::make('User Information')
                            ->columns(2)
                            ->schema(static::getFormComponents())
                            ->columnSpan(2),

                        Section::make('Avatar')
                            ->schema([
                                FileUpload::make('avatar')
                                    ->image()
                                    ->directory('avatars')
                                    ->imageEditor()
                                    ->circleCropper()
                                    ->hiddenLabel(),
                            ])
                            ->columnSpan(1),
                    ]),
            ]);
    }

    /**
     * @return array<Component>
     */
    public static function getFormComponents(): array
    {
        return [
            TextInput::make('name')
                ->required()
                ->maxLength(255),

            TextInput::make('email')
                ->email()
                ->required()
                ->unique(ignoreRecord: true)
                ->maxLength(255),

            TextInput::make('password')
                ->password()
                ->dehydrateStateUsing(fn (string $state): string => Hash::make($state))
                ->dehydrated(fn (?string $state): bool => filled($state))
                ->required(fn (string $operation): bool => $operation === 'create')
                ->confirmed()
                ->revealable(),

            TextInput::make('password_confirmation')
                ->password()
                ->requiredWith('password')
                ->dehydrated(false)
                ->revealable(),

            self::guardedSelfEdit(
                Select::make('role_id')
                    ->label('Role')
                    ->relationship('role', 'name')
                    ->searchable()
                    ->preload()
                    ->required()
                    ->native(false),
                'role',
            ),

            self::guardedSelfEdit(
                Select::make('status')
                    ->options(UserStatus::class)
                    ->default(UserStatus::Active)
                    ->required()
                    ->native(false),
                'status',
            ),
        ];
    }

    /**
     * Make a Select read-only (and excluded from save) when the form's record
     * is the currently authenticated user — prevents self-escalation via
     * direct form submission.
     */
    private static function guardedSelfEdit(Select $select, string $noun): Select
    {
        $isSelf = fn (?User $record): bool => $record?->id === Auth::id();

        return $select
            ->disabled($isSelf)
            ->dehydrated(fn (?User $record): bool => ! $isSelf($record))
            ->helperText(fn (?User $record): ?string => $isSelf($record)
                ? "You cannot change your own {$noun}."
                : null);
    }
}
