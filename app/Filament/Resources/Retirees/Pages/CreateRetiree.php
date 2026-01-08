<?php

namespace App\Filament\Resources\Retirees\Pages;

use App\Filament\Resources\Retirees\RetireeResource;
use Filament\Resources\Pages\CreateRecord;

class CreateRetiree extends CreateRecord
{
    protected static string $resource = RetireeResource::class;
    protected function getRedirectUrl(): string
    {
        // Retorna la URL del índice (la tabla)
        return $this->getResource()::getUrl('index');
    }
}
