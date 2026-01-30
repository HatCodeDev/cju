<?php

declare(strict_types=1);

namespace App\Enums;

use Filament\Support\Contracts\HasColor; // <--- Importar esto
use Filament\Support\Contracts\HasLabel;

enum RetireeType: string implements HasLabel, HasColor // <--- Agregar la interfaz
{
    case Internal = 'internal';
    case External = 'external';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Internal => 'Jubilado BUAP',
            self::External => 'Externo',
        };
    }

    // Nuevo método para definir los colores
    public function getColor(): string | array | null
    {
        return match ($this) {
            self::Internal => 'info', // Verde
            self::External => 'warning', // Naranja/Amarillo (o usa 'info' para azul, 'gray' para gris)
        };
    }
}
