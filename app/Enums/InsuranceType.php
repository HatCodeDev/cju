<?php

declare(strict_types=1);

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum InsuranceType: string implements HasLabel
{
    case HUP = 'hup';
    case IMSS = 'imss';
    case ISSSTE = 'issste';
    case ISSSTEP = 'issstep';
    case Other = 'other';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::HUP => 'HUP',
            self::IMSS => 'IMSS',
            self::ISSSTE => 'ISSSTE',
            self::ISSSTEP => 'ISSSTEP',
            self::Other => 'Otro (Especificar)',
        };
    }
}
