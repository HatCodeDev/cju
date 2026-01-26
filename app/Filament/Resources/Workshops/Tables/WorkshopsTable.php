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
                        if ($record->schedules->isEmpty()) {
                            return 'Sin asignar';
                        }

                        // Formatear: "Lun 10:00-11:00, Mar..."
                        return $record->schedules
                            ->map(function ($schedule) {
                                // Convertimos el enum o entero a nombre corto (ej: 'Lun')
                                $dayName = match($schedule->day_of_week) {
                                    DayOfWeek::Lunes, 1 => 'Lun',
                                    DayOfWeek::Martes, 2 => 'Mar',
                                    DayOfWeek::Miercoles, 3 => 'Mié',
                                    DayOfWeek::Jueves, 4 => 'Jue',
                                    DayOfWeek::Viernes, 5 => 'Vie',
                                    DayOfWeek::Sabado, 6 => 'Sáb',
                                    DayOfWeek::Domingo, 7 => 'Dom',
                                    default => '?',
                                };

                                // Formateo simple de horas
                                $start = \Carbon\Carbon::parse($schedule->start_time)->format('H:i');
                                $end = \Carbon\Carbon::parse($schedule->end_time)->format('H:i');

                                return "{$dayName} {$start}-{$end}";
                            })
                            ->join(', ');
                    })
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
