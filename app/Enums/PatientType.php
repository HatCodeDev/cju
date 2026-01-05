<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;
use Filament\Support\Contracts\HasColor;

enum PatientType: string implements HasLabel, HasColor
{
    case JUBILADO_BUAP = 'jubilado_buap';
    case EXTERNO = 'externo';
    case ESTUDIANTE = 'estudiante';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::JUBILADO_BUAP => 'Jubilado BUAP',
            self::EXTERNO => 'Externo',
            self::ESTUDIANTE => 'Estudiante',
        };
    }

    public function getColor(): string | array | null
    {
        return match ($this) {
            self::JUBILADO_BUAP => 'info',
            self::EXTERNO => 'warning',
            self::ESTUDIANTE => 'success',
        };
    }
}
