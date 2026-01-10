<?php

declare(strict_types=1);

namespace App\Filament\Resources\Users\Tables;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('avatar_url')
                    ->label('Avatar')
                    ->disk('public')
                    ->circular()
                    ->defaultImageUrl(url('/images/placeholder.png')), // Opcional
                TextColumn::make('name')
                    ->label('Nombre')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('email')
                    ->label('Correo Electrónico')
                    ->searchable()
                    ->icon('heroicon-m-envelope')
                    ->copyable()
                    ->copyMessage('Email copiado')
                    ->sortable(),

                // Columna para ver los Roles (Shield)
                TextColumn::make('roles.name')
                    ->label('Roles')
                    ->badge()
                    ->color('primary')
                    ->separator(','),

                TextColumn::make('created_at')
                    ->label('Registrado')
                    ->dateTime('d/m/Y H:i') // Formato legible
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->label('Última Actualización')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                // Futuro: Filtro por Roles (SelectFilter)
            ])
            ->recordActions([
                Action::make('print_credential')
                    ->label('Gafete')
                    ->icon('heroicon-o-identification')
                    ->color('warning')
                    // Agregamos la URL explícita pasando el ID del registro
                    ->url(fn (User $record) => route('print.credentials', ['ids' => $record->id]))
                    ->openUrlInNewTab(), // Ahora sí funcionará porque tiene una URL que abrir

                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    // Acción Masiva: Imprimir Selección
                    BulkAction::make('print_selected_credentials')
                        ->label('Imprimir Gafetes Seleccionados')
                        ->icon('heroicon-o-printer')
                        ->color('warning')
                        ->action(function (Collection $records) {
                            // Redirigimos al usuario a la vista de impresión con los IDs
                            // Nota: Las acciones masivas NO pueden abrir nuevas pestañas automáticamente por seguridad del navegador (pop-up blockers)
                            // Por lo tanto, esto redireccionará la pestaña actual.
                            return redirect()->route('print.credentials', [
                                'ids' => implode(',', $records->pluck('id')->toArray())
                            ]);
                        })
                        ->deselectRecordsAfterCompletion(),

                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
