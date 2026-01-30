<?php

declare(strict_types=1);

namespace App\Filament\Resources\Workshops\Tables;

use App\Models\Workshop;
use App\Enums\DayOfWeek;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Table;
use Illuminate\Support\Collection;

class WorkshopsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Taller')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('teacher.name')
                    ->label('Profesor')
                    ->searchable()
                    ->sortable(),

                // Columna Personalizada: Resumen de Horarios
                TextColumn::make('schedules_summary')
                    ->label('Horarios')
                    ->state(function (Workshop $record): string {
                        if (!$record->relationLoaded('schedules')) {
                            $record->load('schedules');
                        }
                        if ($record->schedules->isEmpty()) {
                            return 'Sin horarios';
                        }

                        // Formatear: "Lun 10:00-11:00, Mar..."
                        return $record->schedules
                            ->map(function ($schedule) {
                                // Manejo robusto del Enum o entero
                                $dayVal = $schedule->day_of_week;
                                // Si es un objeto Enum, obtener su valor, si no, usarlo directo
                                $val = $dayVal instanceof \UnitEnum ? $dayVal->value : $dayVal;

                                $dayName = match($val) {
                                    1, '1' => 'Lun',
                                    2, '2' => 'Mar',
                                    3, '3' => 'Mié',
                                    4, '4' => 'Jue',
                                    5, '5' => 'Vie',
                                    6, '6' => 'Sáb',
                                    7, '7' => 'Dom',
                                    default => '?',
                                };
                                // Formateo simple de horas
                                $start = \Carbon\Carbon::parse($schedule->start_time)->format('H:i');
                                $end = \Carbon\Carbon::parse($schedule->end_time)->format('H:i');

                                return "{$dayName} {$start}-{$end}";
                            })
                            ->join(', ');
                    })
                    ->size('sm')
                    ->wrap(), // Permitir que salte de línea si es largo

                IconColumn::make('is_active')
                    ->label('Estado')
                    ->boolean(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                //
            ]);
    }
}
