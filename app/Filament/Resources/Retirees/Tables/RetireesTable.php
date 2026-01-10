<?php

declare(strict_types=1);

namespace App\Filament\Resources\Retirees\Tables;

use App\Enums\PatientType;
use App\Models\Retiree;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Str;

class RetireesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('photo_path')
                    ->label('Foto')
                    ->circular(),

                TextColumn::make('full_name')
                    ->label('Nombre')
                    ->searchable()
                    ->sortable()
                    ->description(fn (Retiree $record) => $record->gender?->getLabel()),

                TextColumn::make('curp')
                    ->label('CURP')
                    ->searchable()
                    ->copyable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('uuid')
                    ->label('UUID')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('patient_type')
                    ->label('Tipo')
                    ->badge()
                    ->sortable(),

                TextColumn::make('age_calc')
                    ->label('Edad')
                    ->state(fn (Retiree $record) => $record->age ? $record->age . ' años' : 'Sin fecha')
                    ->sortable(query: function (Builder $query, string $direction): Builder {
                        return $query->orderBy('birth_date', $direction);
                    }),

                IconColumn::make('is_present')
                    ->label('Asistencia')
                    ->boolean()
                    ->sortable(),

                TextColumn::make('emergency_contact1')
                    ->label('Tel. Emergencia')
                    ->icon('heroicon-m-phone')
                    ->copyable()
                    ->formatStateUsing(fn (string $state): string => preg_replace("/^(\d{3})(\d{3})(\d{4})$/", "($1) $2-$3", $state)),
            ])
            ->filters([
                TrashedFilter::make(),
                SelectFilter::make('patient_type')
                    ->label('Tipo de Paciente')
                    ->options(PatientType::class),
            ])
            ->recordActions([
                // Action genérica importada desde Filament\Actions\Action
                Action::make('print_qr')
                    ->label('PDF')
                    ->icon('heroicon-o-printer')
                    ->color('warning')
                    ->action(function (Retiree $record) {
                        $pdf = Pdf::loadView('pdf.retiree-card', ['retirees' => [$record]]);
                        $filename = Str::slug($record->full_name) . '-QR.pdf';
                        return response()->streamDownload(function () use ($pdf) {
                            echo $pdf->output();
                        }, $filename);
                    }),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    // Implementación manual de DeleteBulkAction
                    BulkAction::make('delete')
                        ->label('Eliminar seleccionados')
                        ->icon('heroicon-o-trash')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->action(fn (Collection $records) => $records->each->delete())
                        ->deselectRecordsAfterCompletion(),

                    // Implementación manual de ForceDeleteBulkAction
                    BulkAction::make('forceDelete')
                        ->label('Forzar eliminación')
                        ->icon('heroicon-o-trash')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->action(fn (Collection $records) => $records->each->forceDelete())
                        ->deselectRecordsAfterCompletion(),

                    // Implementación manual de RestoreBulkAction
                    BulkAction::make('restore')
                        ->label('Restaurar seleccionados')
                        ->icon('heroicon-o-arrow-uturn-left')
                        ->color('success')
                        ->requiresConfirmation()
                        ->action(fn (Collection $records) => $records->each->restore())
                        ->deselectRecordsAfterCompletion(),

                    BulkAction::make('print_selected')
                        ->label('Imprimir Credenciales')
                        ->icon('heroicon-o-printer')
                        ->color('warning')
                        ->action(function (Collection $records) {
                            $pdf = Pdf::loadView('pdf.retiree-card', [
                                'retirees' => $records
                            ]);
                            $pdf->setPaper('letter', 'portrait');

                            $pdf->setOptions([
                                'dpi' => 150,
                                'defaultFont' => 'sans-serif',
                                'isRemoteEnabled' => true
                            ]);
                            $filename = 'Credenciales-Masivas-' . now()->format('Y-m-d-His') . '.pdf';
                            return response()->streamDownload(
                                fn () => print($pdf->output()),
                                $filename
                            );
                        })
                        ->deselectRecordsAfterCompletion(),
                ]),
            ]);
    }
}
