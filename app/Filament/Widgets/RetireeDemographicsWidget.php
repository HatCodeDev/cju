<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Enums\RetireeType;
use App\Models\Retiree;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class RetireeDemographicsWidget extends BaseWidget
{

    protected static ?int $sort = 2;

    protected function getStats(): array
    {
        $totalRetirees = Retiree::count();

        $internalCount = Retiree::where('retiree_type', RetireeType::Internal)->count();
        $externalCount = Retiree::where('retiree_type', RetireeType::External)->count();

        return [
            Stat::make('Total Registrados', $totalRetirees)
                ->description('Padrón completo')
                ->descriptionIcon('heroicon-m-users')
                ->color('primary'),

            Stat::make('Jubilados BUAP', $internalCount)
                ->description('Personal interno')
                ->descriptionIcon('heroicon-m-academic-cap')
                ->color(RetireeType::Internal->getColor() ?? 'success'),

            Stat::make('Usuarios Externos', $externalCount)
                ->description('Público general')
                ->descriptionIcon('heroicon-m-globe-alt')
                ->color(RetireeType::External->getColor() ?? 'warning'),
        ];
    }
}
