<?php

declare(strict_types=1);

namespace App\Filament\Resources\Retirees\Schemas;

use App\Enums\Gender;
use App\Enums\PatientType;
use Filament\Forms;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class RetireeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Información Personal')
                    ->schema([
                        Forms\Components\FileUpload::make('photo_path')
                            ->label('Fotografía')
                            ->avatar()
                            ->imageEditor()
                            ->directory('retirees-photos')
                            ->columnSpanFull(),

                        Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('full_name')
                                    ->label('Nombre Completo')
                                    ->required()
                                    ->maxLength(255)
                                    ->dehydrateStateUsing(fn (?string $state): string => Str::title($state ?? '')),

                                Forms\Components\TextInput::make('curp')
                                    ->label('CURP')
                                    ->required()
                                    ->minLength(18)
                                    ->maxLength(18)
                                    ->unique(ignoreRecord: true)
                                    ->extraInputAttributes(['style' => 'text-transform: uppercase'])
                                    ->dehydrateStateUsing(fn (string $state): string => strtoupper($state))
                                    ->placeholder('CLAVE ÚNICA DE REGISTRO DE POBLACIÓN'),

                                Forms\Components\Select::make('patient_type')
                                    ->label('Tipo de Paciente')
                                    ->options(PatientType::class)
                                    ->native(false)
                                    ->required(),
                            ]),

                        Grid::make(2)
                            ->schema([
                                Forms\Components\Select::make('gender')
                                    ->label('Sexo')
                                    ->options(Gender::class)
                                    ->native(false),

                                Forms\Components\DatePicker::make('birth_date')
                                    ->label('Fecha de Nacimiento')
                                    ->maxDate(now())
                                    ->required(),
                            ]),

                        Forms\Components\Toggle::make('is_present')
                            ->label('¿Está presente hoy?')
                            ->onColor('success')
                            ->offColor('danger')
                            ->default(false),
                    ]),

                Section::make('Datos de Emergencia y Médicos')
                    ->schema([
                        Forms\Components\TextInput::make('emergency_contact1')
                            ->label('Contacto de Emergencia 1')
                            ->tel()
                            ->numeric()
                            ->minLength(10)
                            ->maxLength(10)
                            ->required()
                            ->placeholder('10 dígitos sin espacios'),

                        Forms\Components\TextInput::make('emergency_contact2')
                            ->label('Contacto de Emergencia 2')
                            ->tel()
                            ->numeric()
                            ->minLength(10)
                            ->maxLength(10)
                            ->placeholder('Opcional'),

                        Forms\Components\Textarea::make('medical_notes')
                            ->label('Notas Médicas')
                            ->rows(3)
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
