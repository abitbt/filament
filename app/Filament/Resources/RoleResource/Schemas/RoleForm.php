<?php

namespace App\Filament\Resources\RoleResource\Schemas;

use App\Enums\Permission;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\ToggleButtons;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class RoleForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Role')
                    ->tabs([
                        Tabs\Tab::make('Details')
                            ->icon('heroicon-o-information-circle')
                            ->schema([
                                Section::make()
                                    ->columns(2)
                                    ->schema(static::getDetailsComponents()),
                            ]),

                        Tabs\Tab::make('Permissions')
                            ->icon('heroicon-o-shield-check')
                            ->schema(static::getPermissionComponents()),
                    ])
                    ->columnSpanFull(),
            ]);
    }

    /**
     * @return array<\Filament\Schemas\Components\Component>
     */
    public static function getDetailsComponents(): array
    {
        return [
            TextInput::make('name')
                ->required()
                ->maxLength(255)
                ->live(onBlur: true)
                ->afterStateUpdated(fn (Set $set, ?string $state) => $set('slug', Str::slug($state ?? ''))),

            TextInput::make('slug')
                ->required()
                ->unique(ignoreRecord: true)
                ->maxLength(255)
                ->helperText('Used to identify the role in code'),

            Textarea::make('description')
                ->columnSpanFull()
                ->rows(3),

            Toggle::make('is_default')
                ->label('Default role for new users')
                ->helperText('Only one role can be the default'),
        ];
    }

    /**
     * @return array<\Filament\Schemas\Components\Component>
     */
    public static function getPermissionComponents(): array
    {
        $components = [];

        foreach (Permission::groups() as $group) {
            $fieldName = 'access_level_'.Str::snake(Str::lower($group));

            $components[] = ToggleButtons::make($fieldName)
                ->label($group)
                ->inlineLabel()
                ->options([
                    '0' => 'None',
                    '1' => 'View',
                    '2' => 'View & Edit',
                    '3' => 'View, Edit & Delete',
                ])
                ->icons([
                    '0' => 'heroicon-o-x-circle',
                    '1' => 'heroicon-o-eye',
                    '2' => 'heroicon-o-pencil-square',
                    '3' => 'heroicon-o-trash',
                ])
                ->colors([
                    '0' => 'gray',
                    '1' => 'info',
                    '2' => 'warning',
                    '3' => 'danger',
                ])
                ->default('0')
                ->inline()
                ->grouped();
        }

        return $components;
    }

    /**
     * Get the access level field name for a permission group.
     */
    public static function getAccessLevelFieldName(string $group): string
    {
        return 'access_level_'.Str::snake(Str::lower($group));
    }

    /**
     * Convert permissions to access levels for form hydration.
     *
     * @param  array<int>  $permissionIds
     * @return array<string, string>
     */
    public static function permissionsToAccessLevels(array $permissionIds): array
    {
        $permissions = \App\Models\Permission::whereIn('id', $permissionIds)->get();
        $accessLevels = [];

        foreach (Permission::groups() as $group) {
            $fieldName = static::getAccessLevelFieldName($group);
            $maxLevel = 0;

            foreach ($permissions as $permission) {
                $enum = Permission::tryFrom($permission->name);
                if ($enum && $enum->getGroup() === $group) {
                    $maxLevel = max($maxLevel, $enum->getAccessLevel());
                }
            }

            $accessLevels[$fieldName] = (string) $maxLevel;
        }

        return $accessLevels;
    }

    /**
     * Convert access levels to permission IDs for saving.
     *
     * @param  array<string, mixed>  $data
     * @return array<int>
     */
    public static function accessLevelsToPermissionIds(array $data): array
    {
        $permissionNames = [];

        foreach (Permission::groups() as $group) {
            $fieldName = static::getAccessLevelFieldName($group);
            $level = (int) ($data[$fieldName] ?? 0);

            $permissions = Permission::forResourceAtLevel($group, $level);
            foreach ($permissions as $permission) {
                $permissionNames[] = $permission->value;
            }
        }

        return \App\Models\Permission::whereIn('name', $permissionNames)
            ->pluck('id')
            ->all();
    }
}
