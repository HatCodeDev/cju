<?php

namespace App\Filament\Resources\AttendanceLogs\Pages;

use App\Filament\Resources\AttendanceLogs\AttendanceLogResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewAttendanceLog extends ViewRecord
{
    protected static string $resource = AttendanceLogResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
