<?php

namespace Database\Seeders;

use App\Models\Retiree;
use Illuminate\Database\Seeder;

class RetireeSeeder extends Seeder
{
    public function run(): void
    {
        // Crea 15 registros usando la fábrica definida arriba
        Retiree::factory()->count(15)->create();
    }
}
