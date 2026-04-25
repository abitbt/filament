<?php

namespace App\Filament\Clusters\Demo\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

/**
 * Synthetic order status used by the Actions demo page to illustrate
 * Filament's stateful workflow-action pattern. Not persisted — lives on
 * a Livewire property for the duration of the page session.
 */
enum OrderStatus: string implements HasColor, HasLabel
{
    case New = 'new';
    case Processing = 'processing';
    case Shipped = 'shipped';
    case Delivered = 'delivered';
    case Cancelled = 'cancelled';

    public function getLabel(): string
    {
        return ucfirst($this->value);
    }

    public function getColor(): string
    {
        return match ($this) {
            self::New => 'gray',
            self::Processing => 'warning',
            self::Shipped => 'info',
            self::Delivered => 'success',
            self::Cancelled => 'danger',
        };
    }
}
