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

    protected static ?string $navigationLabel = 'Escaneo de asistencia';
    protected static string | UnitEnum | null $navigationGroup = 'Gestion de jubilados';


    protected static ?string $title = 'Estación de Escaneo';

    protected string $view = 'filament.pages.scan-station';

    public function processScan(string $uuid): void
    {
        try {
            DB::transaction(function () use ($uuid) {
                // 1. Buscar al Jubilado y BLOQUEAR la fila para evitar doble escaneo
                $retiree = Retiree::where('uuid', $uuid)
                    ->lockForUpdate()
                    ->firstOrFail();

                // 2. Determinar la acción (Si está presente -> Sale, Si no -> Entra)
                $isCheckIn = ! $retiree->is_present;
                $logType = $isCheckIn ? AttendanceType::CHECK_IN : AttendanceType::CHECK_OUT;

                // 3. Actualizar estado del Jubilado
                $retiree->update(['is_present' => $isCheckIn]);

                // 4. Crear Log de Asistencia
                // No necesitamos 'scanned_at' porque 'created_at' lo maneja Laravel
                AttendanceLog::create([
                    'retiree_id' => $retiree->id,
                    'type' => $logType,
                ]);

                // 5. Notificar
                $this->sendSuccessNotification($retiree, $logType);
            });

        } catch (ModelNotFoundException $e) {
            Notification::make()
                ->title('Código No Encontrado')
                ->body("El código QR ({$uuid}) no pertenece a ningún jubilado registrado.")
                ->danger()
                ->persistent()
                ->send();

            // Opcional: Emitir evento para sonido de error en frontend
            // $this->dispatch('scan-error');

        } catch (\Throwable $e) {
            Notification::make()
                ->title('Error del Sistema')
                ->body('Ocurrió un error inesperado al procesar el escaneo.')
                ->danger()
                ->send();

            // Es buena práctica reportar el error a los logs de Laravel
            report($e);
        }
    }

    private function sendSuccessNotification(Retiree $retiree, AttendanceType $type): void
    {
        // Determinamos textos basados en el tipo
        $isCheckIn = $type === AttendanceType::CHECK_IN;

        $title = $isCheckIn ? '¡Bienvenido/a!' : '¡Hasta luego!';
        $icon = $isCheckIn ? 'heroicon-o-arrow-right-end-on-rectangle' : 'heroicon-o-arrow-left-start-on-rectangle';

        // Usamos los métodos del Enum si implementa HasLabel y HasColor
        // Si tu Enum no tiene getColor(), usa un ternario simple aquí.
        $color = method_exists($type, 'getColor') ? $type->getColor() : ($isCheckIn ? 'success' : 'info');
        $actionLabel = method_exists($type, 'getLabel') ? $type->getLabel() : ($isCheckIn ? 'Entrada' : 'Salida');

        Notification::make()
            ->title($title)
            ->body("<strong>{$retiree->full_name}</strong> ha registrado su {$actionLabel}.")
            ->icon($icon)
            ->color($color)
            ->duration(5000)
            ->send();

        // Opcional: Emitir evento para sonido de éxito
        // $this->dispatch('scan-success');
    }
}
