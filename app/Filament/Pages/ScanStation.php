<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use App\Enums\AttendanceType;
use App\Models\AttendanceLog;
use App\Models\Retiree;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use BackedEnum;
use UnitEnum;

class ScanStation extends Page
{
    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-viewfinder-circle';

    protected static ?string $navigationLabel = 'Estación de Escaneo';

    protected static ?string $title = 'Estación de Escaneo';

    protected static string | UnitEnum | null $navigationGroup = 'Operaciones';

    protected string $view = 'filament.pages.scan-station';


    public function processScan(string $uuid): void
    {
        try {
            DB::transaction(function () use ($uuid) {
                // 1. Buscar al Jubilado
                $retiree = Retiree::where('uuid', $uuid)->firstOrFail();

                // 2. Lógica de inversión de estado
                $newPresenceStatus = !$retiree->is_present;
                $logType = $newPresenceStatus ? AttendanceType::CHECK_IN : AttendanceType::CHECK_OUT;

                // 3. Actualizar
                $retiree->update(['is_present' => $newPresenceStatus]);

                // 4. Log
                AttendanceLog::create([
                    'retiree_id' => $retiree->id,
                    'type' => $logType,
                ]);

                // 5. Notificar
                $this->sendSuccessNotification($retiree, $logType);
            });

        } catch (ModelNotFoundException $e) {
            Notification::make()
                ->title('Código Inválido')
                ->body("El código QR escaneado ({$uuid}) no pertenece a ningún jubilado activo.")
                ->danger()
                ->persistent() // Se queda en pantalla hasta que lo cierren
                ->send();
        } catch (\Throwable $e) {
            Notification::make()
                ->title('Error del Sistema')
                ->body('Ocurrió un error inesperado.')
                ->danger()
                ->send();

            report($e);
        }
    }

    private function sendSuccessNotification(Retiree $retiree, AttendanceType $type): void
    {
        $title = $type === AttendanceType::CHECK_IN ? '¡Bienvenido/a!' : '¡Hasta luego!';

        Notification::make()
            ->title($title)
            ->body("{$retiree->full_name} ha registrado su {$type->getLabel()}.")
            ->icon($type === AttendanceType::CHECK_IN ? 'heroicon-o-arrow-right-end-on-rectangle' : 'heroicon-o-arrow-left-start-on-rectangle')
            ->color($type->getColor())
            ->duration(5000)
            ->send();
    }
}
