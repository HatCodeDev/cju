<?php

namespace App\Filament\Resources\Workshops;

use App\Filament\Resources\Workshops\Pages\CreateWorkshop;
use App\Filament\Resources\Workshops\Pages\EditWorkshop;
use App\Filament\Resources\Workshops\Pages\ListWorkshops;
use App\Filament\Resources\Workshops\Schemas\WorkshopForm;
use App\Filament\Resources\Workshops\Tables\WorkshopsTable;
use App\Models\Workshop;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class WorkshopResource extends Resource
{
    protected static ?string $model = Workshop::class;

    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-academic-cap';
    protected static string | UnitEnum | null $navigationGroup = 'Gestion de Talleres';

    protected static ?string $modelLabel = 'Taller';
    protected static ?string $pluralModelLabel = 'Talleres';

    public static function form(Schema $schema): Schema
    {
        return WorkshopForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return WorkshopsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListWorkshops::route('/'),
            'create' => CreateWorkshop::route('/create'),
            'edit' => EditWorkshop::route('/{record}/edit'),
        ];
    }
}
