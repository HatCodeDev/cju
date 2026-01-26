<?php

declare(strict_types=1);

namespace App\Filament\Resources\Locations\Schemas;

use Filament\Forms;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class LocationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Detalles de la Ubicación')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nombre del Salón/Lugar')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),

                        Forms\Components\TextInput::make('capacity')
                            ->label('Capacidad Máxima')
                            ->numeric()
                            ->minValue(1)
                            ->maxValue(1000)
                            ->nullable(),

                        Forms\Components\Toggle::make('is_active')
                            ->label('Ubicación Activa')
                            ->default(true)
                            ->onColor('success'),
                    ])->columns(2),
            ]);
    }
}
