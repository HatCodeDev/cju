<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\MedicalService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class MedicalServiceSeeder extends Seeder
{
    public function run(): void
    {
        $services = [
            'Medicina General',
            'Fisioterapia',
            'Podología',
            'Nutrición',
            'Psicología',
            'Gimnasio',

        ];

        foreach ($services as $service) {
            MedicalService::firstOrCreate(
                ['name' => $service],
                ['slug' => Str::slug($service), 'is_active' => true]
            );
        }
    }
}
