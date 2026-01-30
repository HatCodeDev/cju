<?php

declare(strict_types=1);

namespace App\Filament\Resources\Retirees\Tables;

use App\Enums\RetireeType;
use App\Models\Retiree;
use Barryvdh\DomPDF\Facade\Pdf;
// --- ACCIONES UNIFICADAS (FILAMENT V4) ---
use Filament\Actions\Action;
use Filament\Actions\ActionGroup; // Importante para agrupar
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction; // Nueva acción de detalle
// --- COLUMNAS Y TABLA ---
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
                    ->circular()
                    ->defaultImageUrl(url('/images/placeholder.png')),

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
                    ->fontFamily('mono')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('retiree_type')
                    ->label('Tipo')
                    ->badge()
                    ->sortable(),

                TextColumn::make('age')
                    ->label('Edad')
                    ->state(fn (Retiree $record) => $record->age ? $record->age . ' años' : '-')
                    ->sortable(query: function (Builder $query, string $direction): Builder {
                        return $query->orderBy('birth_date', $direction);
                    }),

                IconColumn::make('is_present')
                    ->label('Asistencia')
                    ->boolean()
                    ->sortable(),

                TextColumn::make('emergencyContact.phone')
                    ->label('Emergencia')
                    ->icon('heroicon-m-phone')
                    ->copyable()
                    ->placeholder('-')
                    ->formatStateUsing(fn (?string $state): string => $state ? preg_replace("/^(\d{3})(\d{3})(\d{4})$/", "($1) $2-$3", $state) : ''),
            ])
            ->filters([
                TrashedFilter::make(),
                SelectFilter::make('retiree_type')
                    ->label('Tipo de Jubilado')
                    ->options(RetireeType::class),
            ])
            ->actions([
                // GRUPO DE ACCIONES: Mejora visual para no saturar la tabla
                ActionGroup::make([

                    // 2. Editar
                    EditAction::make()
                        ->color('primary'),

                    // 3. Imprimir Formato de Registro (NUEVA)
                    Action::make('print_registration')
                        ->label('Hoja de Registro')
                        ->icon('heroicon-o-document-text')
                        ->color('success')
                        ->action(function (Retiree $record) {
                            // Cargar relaciones necesarias para el PDF
                            $record->load(['emergencyContact', 'medicalServices', 'workshops']);

                            $pdf = Pdf::loadView('pdf.retiree-registration-form', ['retiree' => $record]);
                            $pdf->setPaper('letter', 'portrait');

                            return response()->streamDownload(function () use ($pdf) {
                                echo $pdf->output();
                            }, "Registro-{$record->curp}.pdf");
                        }),

                    // 4. Imprimir QR (Renombrada)
                    Action::make('print_qr')
                        ->label('Imprimir QR')
                        ->icon('heroicon-o-qr-code')
                        ->color('warning')
                        ->action(function (Retiree $record) {
                            $pdf = Pdf::loadView('pdf.retiree-card', ['retirees' => [$record]]);
                            return response()->streamDownload(function () use ($pdf) {
                                echo $pdf->output();
                            }, "QR-{$record->full_name}.pdf");
                        }),
                    // 1. Ver Detalle (Modal)
                    ViewAction::make()
                        ->label('Ver Detalles')
                        ->color('info'),

                    // 5. Borrar
                    DeleteAction::make(),
                ])
                    ->icon('heroicon-m-ellipsis-vertical')
                    ->tooltip('Opciones'),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    BulkAction::make('delete')
                        ->label('Eliminar seleccionados')
                        ->icon('heroicon-o-trash')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->action(fn (Collection $records) => $records->each->delete()),

                    BulkAction::make('print_selected_qr')
                        ->label('Imprimir QRs Masivos')
                        ->icon('heroicon-o-printer')
                        ->color('warning')
                        ->action(function (Collection $records) {
                            $records->load(['emergencyContact', 'medicalServices']);
                            $pdf = Pdf::loadView('pdf.retiree-card', ['retirees' => $records]);
                            $pdf->setPaper('letter', 'portrait');

                            return response()->streamDownload(
                                fn () => print($pdf->output()),
                                'QRs-Masivos.pdf'
                            );
                        })
                        ->deselectRecordsAfterCompletion(),
                ]),
            ]);
    }
}
