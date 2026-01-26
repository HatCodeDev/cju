<x-filament-panels::page>
    <x-filament::section>
        {{ $this->form }}
    </x-filament::section>

    <div class="mt-8">
        @php
            $currentLocationId = $this->filters['location_id'] ?? null;
        @endphp

        {{-- DEBUG: Esto imprimirá en pantalla qué ID tiene la página --}}
        <div class="p-4 mb-4 bg-gray-800 text-white rounded">
            <strong>DEBUG VISTA:</strong> ID enviado al widget: {{ $currentLocationId ?? 'NULO' }}
        </div>

        @if($currentLocationId)
            {{-- CAMBIO CRÍTICO: Pasamos 'currentLocationId' como variable directa, no dentro de 'filters' --}}
            @livewire(\App\Filament\Widgets\WorkshopCalendarWidget::class, [
                'currentLocationId' => $currentLocationId
            ], key('calendar-' . $currentLocationId))
        @endif
    </div>
</x-filament-panels::page>
