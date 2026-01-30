<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\EmergencyContact;
use App\Models\MedicalService;
use App\Models\Retiree;
use App\Models\Workshop; // Asegúrate de importar el modelo Workshop si ya existe
use Illuminate\Database\Seeder;

class RetireeSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Aseguramos que existan servicios médicos antes de asignar
        $this->call(MedicalServiceSeeder::class);

        // Obtenemos todos los servicios y talleres disponibles
        $medicalServices = MedicalService::all();
        // Si tienes talleres creados, descomenta esto:
        // $workshops = Workshop::all();

        // 2. Creamos 20 jubilados
        Retiree::factory()
            ->count(20)
            ->create()
            ->each(function (Retiree $retiree) use ($medicalServices) {

                // A. Crear Contacto de Emergencia (Relación 1:1 o 1:N)
                EmergencyContact::factory()->create([
                    'retiree_id' => $retiree->id
                ]);

                // B. Asignar Servicios Médicos (Relación N:M)
                // Asignamos aleatoriamente entre 0 y 3 servicios a cada jubilado
                if ($medicalServices->count() > 0) {
                    $retiree->medicalServices()->attach(
                        $medicalServices->random(rand(0, 3))->pluck('id')->toArray()
                    );
                }

                // C. Asignar Talleres (Si existen)
                // if (isset($workshops) && $workshops->count() > 0) {
                //    $retiree->workshops()->attach(
                //        $workshops->random(rand(0, 2))->pluck('id')->toArray()
                //    );
                // }
            });
    }
}
