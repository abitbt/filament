<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum ActivityEvent: string implements HasColor, HasIcon, HasLabel
{
    case Created = 'created';
    case Updated = 'updated';
    case Deleted = 'deleted';
    case Login = 'login';
    case Logout = 'logout';

    public function getLabel(): string
    {
        return str($this->value)->headline()->toString();
    }

    public function getColor(): string
    {
        return match ($this) {
            self::Created => 'success',
            self::Updated => 'info',
            self::Deleted => 'danger',
            self::Login => 'primary',
            self::Logout => 'gray',
        };
    }

    public function getIcon(): string
    {
        return match ($this) {
            self::Created => 'heroicon-o-plus-circle',
            self::Updated => 'heroicon-o-pencil-square',
            self::Deleted => 'heroicon-o-trash',
            self::Login => 'heroicon-o-arrow-right-on-rectangle',
            self::Logout => 'heroicon-o-arrow-left-on-rectangle',
        };
    }
}
