<?php

declare(strict_types=1);

namespace App\Filament\Resources\Retirees;

use App\Filament\Resources\Retirees\Pages;
use App\Filament\Resources\Retirees\Schemas\RetireeForm;
use App\Filament\Resources\Retirees\Tables\RetireesTable;
use App\Models\Retiree;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use BackedEnum;
use UnitEnum;

class RetireeResource extends Resource
{
    protected static ?string $model = Retiree::class;

    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-user-group';
    protected static string | UnitEnum | null $navigationGroup = 'Gestion de jubilados';

    protected static ?string $modelLabel = 'Jubilado';

    protected static ?string $pluralModelLabel = 'Jubilados';

    protected static ?string $recordTitleAttribute = 'full_name';

    public static function form(Schema $schema): Schema
    {
        return RetireeForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return RetireesTable::configure($table);
    }

    public static function getRelations(): array
    {
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
