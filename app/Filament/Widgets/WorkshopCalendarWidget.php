<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Models\Location;
use App\Models\WorkshopSchedule;
use Carbon\Carbon;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TimePicker;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
// use Filament\Widgets\Concerns\InteractsWithPageFilters; // <--- YA NO LO NECESITAMOS, LO QUITAMOS PARA EVITAR CONFLICTOS
use Guava\Calendar\Filament\CalendarWidget;
use Guava\Calendar\ValueObjects\CalendarEvent;
use Guava\Calendar\ValueObjects\CalendarResource;
use Guava\Calendar\ValueObjects\FetchInfo;
use Illuminate\Support\Collection;

class WorkshopCalendarWidget extends CalendarWidget
{
    // use InteractsWithPageFilters; // <--- COMENTADO O BORRADO

    // DEFINIMOS LA PROPIEDAD PÚBLICA PARA RECIBIR EL DATO DESDE BLADE
    public ?int $currentLocationId = null;

    protected string $calendarView = 'resourceTimeGridWeek';
    protected string | \Illuminate\Support\HtmlString | bool | null $heading = null;
    protected bool $eventClickEnabled = true;

    protected function getHeaderToolbar(): array
    {
        return [
            'left' => 'prev,next today',
            'center' => 'title',
            'right' => 'resourceTimeGridWeek,timeGridDay,listWeek',
        ];
    }

    public function getResources(): Collection|array
    {
        // USAMOS LA PROPIEDAD DIRECTA
        if (! $this->currentLocationId) {
            return [];
        }

        return Location::query()
            ->where('id', $this->currentLocationId)
            ->get()
            ->map(fn (Location $location) => CalendarResource::make($location->id)
                ->title($location->name)
            )
            ->toArray();
    }

    public function getEvents(FetchInfo $info): Collection|array
    {
        if (! $this->currentLocationId) {
            return [];
        }

        $schedules = WorkshopSchedule::with(['workshop.teacher', 'location'])
            ->where('location_id', $this->currentLocationId)
            ->get();

        $events = collect();
        $appTimezone = config('app.timezone', 'America/Mexico_City');
        $currentDate = Carbon::parse($info->start)->setTimezone($appTimezone);
        $endDate = Carbon::parse($info->end)->setTimezone($appTimezone);

        while ($currentDate->lte($endDate)) {
            $currentDayIso = $currentDate->isoWeekday();

            $dailySchedules = $schedules->filter(function ($schedule) use ($currentDayIso) {
                $scheduleDay = $schedule->day_of_week;
                $value = $scheduleDay instanceof \UnitEnum ? $scheduleDay->value : $scheduleDay;
                return (int) $value === (int) $currentDayIso;
            });

            foreach ($dailySchedules as $schedule) {
                $startDateTime = $currentDate->copy()->setTimeFromTimeString($schedule->start_time);
                $endDateTime = $currentDate->copy()->setTimeFromTimeString($schedule->end_time);

                $color = $this->getColorForWorkshop($schedule->workshop_id);

                // CORRECCIÓN AQUÍ: Manejo seguro del profesor nulo
                $teacherName = $schedule->workshop->teacher?->name ?? 'Sin Profesor';

                $events->push(
                    CalendarEvent::make($schedule)
                        // Usamos la variable segura $teacherName
                        ->title("{$schedule->workshop->name}\n({$teacherName})")
                        ->start($startDateTime)
                        ->end($endDateTime)
                        ->resourceId($schedule->location_id)
                        ->backgroundColor($color)
                        ->extendedProps([
                            // Usamos navegación segura ?->name
                            'teacher' => $teacherName,
                            'description' => $schedule->workshop->description ?? '',
                        ])
                        ->action('edit')
                );
            }
            $currentDate->addDay();
        }

        return $events->toArray();
    }
    public function defaultSchema(Schema $schema): Schema
    {
        return $schema->components([
            Section::make()
                ->schema([
                    Select::make('location_id')
                        ->label('Salón')
                        ->options(Location::where('is_active', true)->pluck('name', 'id'))
                        ->required()
                        ->columnSpanFull(),

                    TimePicker::make('start_time')
                        ->label('Inicio')
                        ->seconds(false)
                        ->required(),

                    TimePicker::make('end_time')
                        ->label('Fin')
                        ->seconds(false)
                        ->required()
                        ->after('start_time'),
                ])
                ->columns(2)
        ]);
    }

    private function getColorForWorkshop(int|string $id): string
    {
        $colors = [
            '#e11d48', '#c026d3', '#7c3aed', '#4f46e5', '#2563eb',
            '#0284c7', '#0d9488', '#059669', '#65a30d', '#d97706'
        ];
        return $colors[$id % count($colors)] ?? '#4b5563';
    }

    public static function canView(): bool
    {
        // Mantenemos esto para que no salga en el dashboard
        $currentRoute = request()->route()?->getName();
        return $currentRoute && str_contains($currentRoute, 'workshop-calendar-page');
    }
}
