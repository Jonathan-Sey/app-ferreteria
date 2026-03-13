<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum FacturasStatus: string implements HasColor, HasIcon, HasLabel
{
    case Pendiente = "1";

    case Pagada = "2";

    case Cancelada = "3";

    case Devuelta = "4";

    // case Cancelled = 'cancelled';

    public function getLabel(): string
    {
        return match ($this) {
            self::Pendiente => 'Pendiente',
            self::Pagada => 'Pagada',
            self::Cancelada => 'Cancelada',
            self::Devuelta => 'Devuelta',
            // self::Cancelled => 'Cancelled',
        };
    }

    public function getColor(): string | array | null
    {
        return match ($this) {
            self::Pendiente => 'info',
            self::Pagada => 'success',
            self::Cancelada=> 'danger',
            self::Devuelta => 'warning',
        };
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::Pendiente => 'heroicon-m-arrow-path',
            self::Pagada => 'heroicon-m-check-badge',
            self::Cancelada => 'heroicon-m-x-circle',
            self::Devuelta => 'heroicon-m-sparkles',
        };
    }
}
