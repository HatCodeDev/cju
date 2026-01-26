<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use App\Models\Location;
use BackedEnum;
use Filament\Forms\Components\Select;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Concerns\InteractsWithSchemas; // Importante para v4
use Filament\Schemas\Contracts\HasSchemas;          // Importante para v4
use Filament\Schemas\Schema;
use UnitEnum;

class WorkshopCalendarPage extends Page implements HasSchemas
{
    use InteractsWithSchemas;

    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-calendar-days';
    protected static string | UnitEnum | null $navigationGroup = 'Gestion de Talleres';
    protected static ?string $navigationLabel = 'Calendario de Ocupación';
    protected static ?string $title = 'Calendario de Talleres';
    protected static ?int $navigationSort = 10;

    protected string $view = 'filament.pages.workshop-calendar-page';

    // Propiedad pública para guardar el estado del filtro
    public ?array $filters = [];

    public function mount(): void
    {
        // Inicializamos el filtro con el primer salón activo
        $defaultId = Location::where('is_active', true)->value('id');

        $this->form->fill([
            'location_id' => $defaultId,
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->schema([
                        Select::make('location_id')
                            ->label('Salón / Espacio')
                            ->options(Location::where('is_active', true)->pluck('name', 'id'))
                            ->searchable()
                            ->preload()
                            ->selectablePlaceholder(false)
                            ->required()
                            ->live(), // Reactividad total
                    ])
                    ->columns(1),
            ])
            ->statePath('filters'); // Guardamos los datos en la variable $filters
    }
    public function updatedFilters(): void
    {
        // Esto fuerza a Livewire a "despertar" y repintar la vista
        // No necesitas código aquí, la sola presencia del método ayuda al ciclo de vida
    }
}
