<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum AttendanceType: string implements HasLabel
{
    case CHECK_IN = 'check_in';
    case CHECK_OUT = 'check_out';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::CHECK_IN => 'Entrada',
            self::CHECK_OUT => 'Salida',
        };
    }

    public function getColor(): string | array | null
    {
        return match ($this) {
            self::CHECK_IN => 'success',
            self::CHECK_OUT => 'warning',
        };
    }
}
