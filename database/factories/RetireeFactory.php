<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\Gender; // Asegúrate de importar tu Enum original
use App\Enums\InsuranceType;
use App\Enums\RetireeType;
use App\Models\Retiree;
use Illuminate\Database\Eloquent\Factories\Factory;

class RetireeFactory extends Factory
{
    protected $model = Retiree::class;

    public function definition(): array
    {
        // Pre-calculamos valores para lógica condicional
        $retireeType = $this->faker->randomElement(RetireeType::cases());
        $insuranceType = $this->faker->randomElement(InsuranceType::cases());

        return [
            // --- CAMPOS LEGACY (Necesarios para tu sistema actual) ---
            // UUID se genera automático por el Trait HasUuids del modelo
            'gender' => $this->faker->randomElement(Gender::cases()), // ¡CRÍTICO! Esto faltaba
            'is_present' => $this->faker->boolean(20), // Simulamos que algunos están presentes
            'photo_path' => null, // O puedes poner una url falsa si quieres

            // --- DATOS GENERALES ---
            'full_name' => $this->faker->name(),
            'curp' => $this->faker->unique()->regexify('[A-Z]{4}[0-9]{6}[HM][A-Z]{2}[A-Z]{3}[0-9]{2}'),
            'phone' => $this->faker->numerify('##########'),
            'birth_date' => $this->faker->dateTimeBetween('-90 years', '-60 years'),

            // --- NUEVA LÓGICA DE NEGOCIO ---
            'retiree_type' => $retireeType,
            // Solo generamos ID si es interno
            'worker_id' => $retireeType === RetireeType::Internal
                ? (string) $this->faker->unique()->numberBetween(10000000, 99999999)
                : null,

            // --- DATOS MÉDICOS ---
            'allergies' => $this->faker->boolean(20) ? 'Penicilina, Polvo' : null,
            'insurance_type' => $insuranceType,
            'insurance_name' => $insuranceType === InsuranceType::Other
                ? $this->faker->company()
                : null,

            'other_services_notes' => $this->faker->boolean(30) ? $this->faker->text(50) : null,
        ];
    }
}
