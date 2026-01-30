<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\EmergencyContact;
use Illuminate\Database\Eloquent\Factories\Factory;

class EmergencyContactFactory extends Factory
{
    protected $model = EmergencyContact::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'phone' => $this->faker->numerify('##########'),
            'relationship' => $this->faker->randomElement(['Hijo/a', 'Esposo/a', 'Nieto/a', 'Hermano/a', 'Vecino/a']),

            // Lógica para si el contacto también es trabajador BUAP
            'is_university_worker' => $isWorker = $this->faker->boolean(30),
            'dependency' => $isWorker ? $this->faker->randomElement(['Facultad de Ingeniería', 'DASU', 'Rectoría', 'Biblioteca']) : null,
        ];
    }
}
