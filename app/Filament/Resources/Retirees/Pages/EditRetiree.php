<?php

namespace App\Filament\Resources\Retirees\Pages;

use App\Filament\Resources\Retirees\RetireeResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\EditRecord;

class EditRetiree extends EditRecord
{
    protected static string $resource = RetireeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }
    protected function getRedirectUrl(): string
    {
        // Retorna la URL del índice (la tabla)
        return $this->getResource()::getUrl('index');
    }
}
