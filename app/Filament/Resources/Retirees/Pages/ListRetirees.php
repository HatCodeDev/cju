<?php

namespace App\Filament\Resources\Retirees\Pages;

use App\Filament\Resources\Retirees\RetireeResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListRetirees extends ListRecords
{
    protected static string $resource = RetireeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
