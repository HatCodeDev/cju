<?php

declare(strict_types=1);

namespace App\Filament\Resources\Workshops\Schemas;

use App\Enums\DayOfWeek;
use App\Models\WorkshopSchedule;
use Filament\Forms;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;
use Closure;

class WorkshopForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Información General')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nombre del Taller')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\Select::make('teacher_id')
                            ->label('Profesor Asignado')
                            ->relationship('teacher', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),

                        Forms\Components\Textarea::make('description')
                            ->label('Descripción')
                            ->rows(3)
                            ->columnSpanFull(),

                        Forms\Components\Toggle::make('is_active')
                            ->label('Taller Activo')
                            ->default(true),
                    ])->columns(2),

                Section::make('Programación de Horarios')
                    ->description('Define los días y lugares donde se imparte el taller.')
                    ->schema([
                        Forms\Components\Repeater::make('schedules')
                            ->label('Horarios')
                            ->relationship()
                            ->schema([
                                Grid::make(2)
                                    ->schema([
                                        Forms\Components\Select::make('day_of_week')
                                            ->label('Día')
                                            ->options(DayOfWeek::class)
                                            ->required()
                                            ->native(false),

                                        Forms\Components\TimePicker::make('start_time')
                                            ->label('Inicio')
                                            ->seconds(false)
                                            ->required()
                                            ->live(),

                                        Forms\Components\TimePicker::make('end_time')
                                            ->label('Fin')
                                            ->seconds(false)
                                            ->required()
                                            ->after('start_time')
                                            ->live(),
                                        Forms\Components\Select::make('location_id')
                                            ->label('Ubicación')
                                            ->relationship(
                                                name: 'location',
                                                titleAttribute: 'name',
                                                modifyQueryUsing: fn ($query) => $query->where('is_active', true) // Solo mostrar ubicaciones activas
                                            )

                                            ->searchable()
                                            ->preload()
                                            ->required()
                                            ->live()
                                            ->loadingMessage('Cargando salones...')
                                            ->noOptionsMessage('No hay salones disponibles')
                                            ->searchPrompt('Busca por nombre del salón')
                                            ->rules([
                                                fn (Get $get): Closure => function (string $attribute, mixed $value, Closure $fail) use ($get) {
                                                    $day = $get('day_of_week');
                                                    $startTime = $get('start_time');
                                                    $endTime = $get('end_time');
                                                    $currentScheduleId = $get('id');

                                                    if (! $day || ! $startTime || ! $endTime || ! $value) {
                                                        return;
                                                    }

                                                    $conflict = WorkshopSchedule::query()
                                                        ->overlapping($day, $startTime, $endTime, (int) $value)
                                                        ->when($currentScheduleId, fn ($q) => $q->where('id', '!=', $currentScheduleId))
                                                        ->with(['workshop'])
                                                        ->first();

                                                    if ($conflict) {
                                                        $fail("Conflicto: El salón está ocupado por '{$conflict->workshop->name}' ({$conflict->start_time} - {$conflict->end_time}).");
                                                    }
                                                },
                                            ]),

                                        Forms\Components\Hidden::make('id'),
                                    ]),
                            ])
                            ->defaultItems(0)
                            ->addActionLabel('Agregar Horario')
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
