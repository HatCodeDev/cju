<?php

declare(strict_types=1);

namespace App\Filament\Resources\Retirees;

use App\Filament\Resources\Retirees\Pages;
use App\Filament\Resources\Retirees\Schemas\RetireeForm; // Importamos la clase del formulario
use App\Filament\Resources\Retirees\Tables\RetireesTable; // Importamos la clase de la tabla
use App\Models\Retiree;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use BackedEnum; // Necesario para tipado estricto en Filament 4

class RetireeResource extends Resource
{
    protected static ?string $model = Retiree::class;

    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $modelLabel = 'Jubilado';

    protected static ?string $pluralModelLabel = 'Jubilados';

    protected static ?string $recordTitleAttribute = 'full_name';

    public static function form(Schema $schema): Schema
    {
        // Delegamos la configuración a la clase dedicada
        return RetireeForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        // Delegamos la configuración a la clase dedicada
        return RetireesTable::configure($table);
    }

    public static function getRelations(): array
    {
        // Por ahora vacío, ya que manejaremos contactos y servicios dentro del mismo formulario
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRetirees::route('/'),
            'create' => Pages\CreateRetiree::route('/create'),
            'edit' => Pages\EditRetiree::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
