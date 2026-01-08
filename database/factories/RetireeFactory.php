<?php

namespace Database\Factories;

use App\Models\Retiree;
use App\Enums\Gender;
use App\Enums\PatientType;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class RetireeFactory extends Factory
{
    /**
     * El modelo asociado a este factory.
     */
    protected $model = Retiree::class;

    public function definition(): array
    {
        // Generador de CURP simulado (Regex para cumplir formato: 4 letras, 6 nums, H/M, etc)
        // Patrón: AAAA 000000 H/M AA AAA 00
        $curpPattern = '[A-Z]{4}[0-9]{6}[HM][A-Z]{2}[B-DF-HJ-NP-TV-Z]{3}[0-9]{2}';

        return [
            // UUID se genera automáticamente por el Trait HasUuids del modelo,

            'full_name' => $this->faker->name(), // Genera nombres como "Juan Perez"

            'curp' => $this->faker->unique()->regexify($curpPattern),

            'patient_type' => $this->faker->randomElement(PatientType::cases()),
            'gender' => $this->faker->randomElement(Gender::cases()),

            'birth_date' => $this->faker->date('Y-m-d', '-60 years'), // Gente mayor de 60 años

            'emergency_contact1' => $this->faker->numerify('##########'),
            'emergency_contact2' => $this->faker->boolean(50) ? $this->faker->numerify('##########') : null,

            'medical_notes' => $this->faker->text(100),
            'photo_path' => null,
            'is_present' => false
        ];
    }
}
