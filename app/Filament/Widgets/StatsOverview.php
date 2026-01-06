<?php

namespace App\Filament\Widgets;

use App\Enums\AttendanceType;
use App\Models\AttendanceLog;
use App\Models\Retiree;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    // Auto-actualizar cada 15 segundos sin recargar la página
    protected ?string $pollingInterval = '15s';

    protected function getStats(): array
    {
        // 1. Ocupación Actual (Contar jubilados con flag 'is_present')
        $presentCount = Retiree::where('is_present', true)->count();

        // 2. Entradas de Hoy (Usando el Enum para filtrar)
        $todayCheckIns = AttendanceLog::whereDate('created_at', today())
            ->where('type', AttendanceType::CHECK_IN)
            ->count();

        // 3. Salidas de Hoy
        $todayCheckOuts = AttendanceLog::whereDate('created_at', today())
            ->where('type', AttendanceType::CHECK_OUT)
            ->count();

        return [
            Stat::make('PERSONAS DENTRO', $presentCount)
                ->description('Ocupación en tiempo real')
                ->descriptionIcon('heroicon-m-user-group')
                ->color($presentCount > 0 ? 'success' : 'gray') // Verde si hay gente, Gris si está vacío
                ->chart([$presentCount, $presentCount, $presentCount]) // Pequeña gráfica visual
                ->extraAttributes([
                    'class' => 'cursor-pointer ring-2 ring-primary-500 shadow-lg', // Resaltar este cuadro sobre los demás
                ]),

            Stat::make('Entradas Hoy', $todayCheckIns)
                ->description('Total del día')
                ->descriptionIcon('heroicon-m-arrow-right-end-on-rectangle')
                ->color('info'),

            Stat::make('Salidas Hoy', $todayCheckOuts)
                ->description('Ya se retiraron')
                ->descriptionIcon('heroicon-m-arrow-left-start-on-rectangle')
                ->color('warning'),
        ];
    }
}
