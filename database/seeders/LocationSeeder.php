<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Location;
use Illuminate\Database\Seeder;

class LocationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $locations = [
            'Patio 1',
            'Patio 2',
            'Sala Audiovisual',
            'Cine',
            'Cocina',
            'Salón 106',
            'Salón 107',
            'Salón 108',
            'Sala de Juegos 109',
            'Salón 110',
            'Salón de Computación',
            'Salón de Tatami',
        ];

        foreach ($locations as $name) {
            Location::firstOrCreate(
                ['name' => $name], // Busca por nombre
                [
                    'capacity' => null, // Aún no disponible
                    'is_active' => true,
                ]
            );
        }
    }
}
