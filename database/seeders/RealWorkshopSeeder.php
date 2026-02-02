<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Location;
use App\Models\Workshop;
use App\Models\WorkshopSchedule;
use App\Enums\DayOfWeek; // Asegúrate de que este Enum exista, si no, usa enteros
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RealWorkshopSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Array masivo con tus datos reales
        $data = [
            ['LUNES','Pilates','9 a 10 am','Patio 2'],
            ['LUNES','Meditación con Sonidos Relajantes','9 a 10 am','Cine'],
            ['LUNES','Danza Folklórica (Grupo A)','9 a 11 am','Salón 106'],
            ['LUNES','Inglés (Grupo C) Básico','9:30 a 11 am','Salón 107'],
            ['LUNES','Taekwon Do en el Adulto Mayor','9 a 11 am','Salón Tatami'],
            ['LUNES','Zumba','10 a 11 am','Patio 2'],
            ['LUNES','Hatha Yoga','10 a 11 am','Salón 108'],
            ['LUNES','Insuficiencia Venosa','10 a 11 am','Salón 110'],
            ['LUNES','Manualidades y Pintura en Madera y Tela','10 a 11:30 am','Patio 1'],
            ['LUNES','Computación Elemental','10 a 11:30 am','Salón de Computación'],
            ['LUNES','Fotografía','10 a 12 pm','Sala Audiovisual'],
            ['LUNES','Tableros de Sanación (Chakras y Cuarzos)','11 a 12 pm','Salón 110'],
            ['LUNES','Francés Grupo A (Avanzado)','11 a 12:30 pm','Salón 108'],
            ['LUNES','Inglés (Grupo B) Intermedio','11 a 12:30 pm','Salón 107'],
            ['LUNES','Navidad todo el año','11 a 1 pm','Salón 106'],
            ['LUNES','Danzón Individual (Grupo B)','11 a 1 pm','Patio 2'],
            ['LUNES','Manualidades y Pintura en Madera y Tela','12 a 1:30 pm','Patio 1'],
            ['LUNES','Francés Grupo B (Intermedio)','12:30 a 2 pm','Salón 108'],
            ['LUNES','Chachachá','1 a 2 pm','Salón 110'],
            ['LUNES','Solfeo','3 a 4 pm','Salón 108'],
            ['LUNES','Gimnasio Terapéutico','4 a 5 pm','Patio 2'],
            ['LUNES','Violín','4 a 5 pm','Salón 108'],
            ['LUNES','Danza Folklórica (Grupo B)','4 a 6 pm','Salón 106'],
            ['MARTES','Jazz','9 a 10 am','Patio 2'],
            ['MARTES','Asthanga Yoga','9 a 10 am','Salón 110'],
            ['MARTES','Activación Coordinación y Retención de la Memoria','9 a 10 am','Salón 107'],
            ['MARTES','Kickboxing para Adultos Mayores','9 a 10 am','Salón 106'],
            ['MARTES','Piano','9 a 11 am','Salón 108'],
            ['MARTES','Uso del Celular','9:30 a 11 am','Sala Audiovisual'],
            ['MARTES','Pintura al Óleo','9:30 a 11 am','Sala de juegos 109'],
            ['MARTES','Yoga','10 a 11 am','Patio 2'],
            ['MARTES','Canto','10 a 11 am','Salón 106'],
            ['MARTES','Gimnasia Cerebral','10 a 11 am','Salón 107'],
            ['MARTES','Meditación con Sonidos Relajantes','10 a 11 am','Cine'],
            ['MARTES','Insuficiencia Venosa','10 a 11 am','Salón 110'],
            ['MARTES','Nutrición al Sartén','10 a 12 pm','Cocina'],
            ['MARTES','Contempojazz','11 a 12 pm','Patio 2'],
            ['MARTES','Guitarra','11 a 12 pm','Salón 106'],
            ['MARTES','Taller de Estimulación Cognitiva','11 a 12 pm','Salón 107'],
            ['MARTES','Coordinación y Equilibrio','11 a 12 pm','Salón 108'],
            ['MARTES','Danza Árabe','11 a 12 pm','Cine'],
            ['MARTES','Tejido de agujas crochet y amigurumi','11 a 1 pm','Patio 1'],
            ['MARTES','Danzón en Pareja (Grupo A)','11 a 1 pm','Salón 110'],
            ['MARTES','Manualidades Pasta Flexible y Cerámica','11 a 1 pm','Sala de juegos 109'],
            ['MARTES','Ritmos Latinos','12 a 1 pm','Patio 2'],
            ['MARTES','Pintura al Óleo','12 a 1:30 pm','Salón 106'],
            ['MARTES','Inglés (Grupo A)','12 a 2 pm','Salón 108'],
            ['MARTES','Rondalla Grupo Romántico','12 a 2 pm','Salón 107'],
            ['MARTES','Aprendiendo a Constelar (Nivel II)','2 a 4 pm','Salón 107'],
            ['MARTES','Solfeo','3 a 4 pm','Salón 108'],
            ['MARTES','Gimnasio Terapéutico','4 a 5 pm','Patio 2'],
            ['MARTES','Violín','4 a 5 pm','Salón 108'],
            ['MARTES','Danza Folklórica (Grupo B)','4 a 6 pm','Salón 106'],
            ['MARTES','Voces en Plenitud','4 a 6 pm','Salón 110'],
            ['MIERCOLES','Pilates','9 a 10 am','Patio 2'],
            ['MIERCOLES','Meditación con Sonidos Relajantes','9 a 10 am','Cine'],
            ['MIERCOLES','Taekwon Do en el Adulto Mayor','9 a 11 am','Salón Tatami'],
            ['MIERCOLES','Danza Folklórica (Grupo A)','9 a 11 am','Salón 106'],
            ['MIERCOLES','Inglés (Grupo C) Básico','9:30 a 11 am','Salón 107'],
            ['MIERCOLES','Zumba','10 a 11 am','Patio 2'],
            ['MIERCOLES','Aprendiendo a Constelar (Nivel I)','10 a 11 am','Salón 108'],
            ['MIERCOLES','Computación Elemental','10 a 11:30 am','Salón de computación'],
            ['MIERCOLES','Manualidades y Pintura en Madera y Tela','10 a 11:30 am','Patio 1'],
            ['MIERCOLES','Fotografía','10 a 12 pm','Sala Audiovisual'],
            ['MIERCOLES','Meditación China','10:30 a 12:30 pm','Salón 110'],
            ['MIERCOLES','Yoga Restaurativa','11 a 12 pm','Salón 106'],
            ['MIERCOLES','Francés Grupo A (Avanzado)','11 a 12:30 pm','Salón 108'],
            ['MIERCOLES','Inglés (Grupo B) Intermedio','11 a 12:30 pm','Salón 107'],
            ['MIERCOLES','Danzón Individual (Grupo B)','11 a 1 pm','Patio 2'],
            ['MIERCOLES','Manualidades y Pintura en Madera y Tela','12 a 1:30 pm','Patio 1'],
            ['MIERCOLES','Grupo de Teatro: Por amor al arte','12:30 a 2 pm','Salón 110'],
            ['MIERCOLES','Francés Grupo B (Intermedio)','12:30 a 2 pm','Salón 108'],
            ['MIERCOLES','Meditación con Sonidos Relajantes','2 a 3 pm','Cine'],
            ['MIERCOLES','Danza Árabe','3 a 4 pm','Salón 110'],
            ['MIERCOLES','Gimnasio Terapéutico','4 a 5 pm','Patio 2'],
            ['MIERCOLES','Grupo de Trovadores','4 a 6 pm','Salón 110'],
            ['MIERCOLES','Danza Folklórica (Grupo B)','4 a 6 pm','Salón 106'],
            ['JUEVES','Jazz','9 a 10 am','Patio 2'],
            ['JUEVES','Activación Coordinación y Retención de la Memoria','9 a 10 am','Salón 107'],
            ['JUEVES','Kickboxing para Adultos Mayores','9 a 10 am','Salón 106'],
            ['JUEVES','Cocina Contemporánea','9 a 12 pm','Cocina'],
            ['JUEVES','Piano','9 a 11 am','Salón 108'],
            ['JUEVES','Pintura al Óleo','9:30 a 11 am','Sala de juegos 109'],
            ['JUEVES','Yoga','10 a 11 am','Patio 2'],
            ['JUEVES','Canto','10 a 11 am','Salón 106'],
            ['JUEVES','Gimnasia Cerebral','10 a 11 am','Salón 107'],
            ['JUEVES','Meditación con Sonidos Relajantes','10 a 11 am','Cine'],
            ['JUEVES','Insuficiencia Venosa','10 a 11 am','Salón 110'],
            ['JUEVES','Coordinación y Equilibrio','11 a 12 pm','Salón 108'],
            ['JUEVES','Contempojazz','11 a 12 pm','Patio 2'],
            ['JUEVES','Taller de Estimulación Cognitiva','11 a 12 pm','Salón 107'],
            ['JUEVES','Guitarra','11 a 12 pm','Salón 106'],
            ['JUEVES','Danza Árabe','11 a 12 pm','Salón 110'],
            ['JUEVES','Tejido de agujas crochet y amigurumi','11 a 1 pm','Patio 1'],
            ['JUEVES','Manualidades Pasta Flexible y Cerámica','11 a 1 pm','Sala de juegos 109'],
            ['JUEVES','Ritmos Latinos','12 a 1 pm','Patio 2'],
            ['JUEVES','Pintura al Óleo','12 a 1:30 pm','Salón 106'],
            ['JUEVES','Rondalla Grupo Romántico','12 a 2 pm','Salón 107'],
            ['JUEVES','Inglés (Grupo A)','12 a 2 pm','Salón 108'],
            ['JUEVES','Gimnasio Terapéutico','3 a 4 pm','Patio 2'],
            ['JUEVES','Voces en Plenitud','4 a 6 pm','Salón 110'],
            ['VIERNES','Pilates','9 a 10 am','Patio 2'],
            ['VIERNES','Biodescodificación','9 a 10 am','Salón 108'],
            ['VIERNES','Activación Coordinación y Retención de la Memoria','9 a 10 am','Salón 107'],
            ['VIERNES','Liberación de Emociones a través de una puesta en escena','9 a 11 am','Salón 106'],
            ['VIERNES','Taekwon Do en el Adulto Mayor','9 a 11 am','Salón Tatami'],
            ['VIERNES','Uso del Celular','9:30 a 11 am','Sala Audiovisual'],
            ['VIERNES','Zumba','10 a 11 am','Patio 2'],
            ['VIERNES','Hatha Yoga','10 a 11 am','Salón 110'],
            ['VIERNES','Cocina Saludable','10 a 12 pm','Cocina'],
            ['VIERNES','Dibujo y Técnicas Mixtas','10 a 12 pm','Salón 107'],
            ['VIERNES','Francés Grupo A (Avanzado)','11 a 12:30 pm','Salón 108'],
            ['VIERNES','Danzón en Pareja (Grupo A)','11 a 1 pm','Salón 110'],
            ['VIERNES','Navidad todo el año','11 a 1 pm','Patio 1'],
            ['VIERNES','Manualidades Pasta Flexible y Cerámica','11 a 1 pm','Sala de juegos 109'],
            ['VIERNES','Francés Grupo B (Intermedio)','12:30 a 2 pm','Salón 108'],
            ['VIERNES','Taller de Consteladores (Nivel Avanzado)','2 a 5 pm','Salón 107'],
            ['VIERNES','Grupo de Trovadores','4 a 6 pm','Salón 110'],
        ];

        DB::transaction(function () use ($data) {
            foreach ($data as $item) {
                $dayName = $item[0];
                $workshopName = $item[1];
                $timeString = $item[2];
                $locationName = $item[3];

                // 1. Obtener o crear Taller
                $workshop = Workshop::firstOrCreate(
                    ['name' => $workshopName],
                    ['is_active' => true, 'teacher_id' => null]
                );

                // 2. Obtener o crear Ubicación
                $location = Location::firstOrCreate(
                    ['name' => $locationName],
                    ['is_active' => true]
                );

                // 3. Parsear Horario (Ej: "9:30 a 11 am")
                [$start, $end] = $this->parseTime($timeString);

                // 4. Parsear Día
                $dayEnum = $this->parseDay($dayName);

                // 5. Crear Horario (evitando duplicados)
                WorkshopSchedule::firstOrCreate([
                    'workshop_id' => $workshop->id,
                    'location_id' => $location->id,
                    'day_of_week' => $dayEnum,
                    'start_time' => $start,
                    'end_time' => $end,
                ]);
            }
        });
    }

    /**
     * Convierte "LUNES" en 1 o en el Enum correspondiente.
     */
    private function parseDay(string $dayName): int
    {
        $map = [
            'LUNES' => 1,
            'MARTES' => 2,
            'MIERCOLES' => 3,
            'JUEVES' => 4,
            'VIERNES' => 5,
            'SABADO' => 6,
            'DOMINGO' => 7,
        ];

        // Si usas Enums en Laravel 10+, podrías retornar DayOfWeek::from($map[$dayName])
        return $map[strtoupper($dayName)] ?? 1;
    }

    /**
     * Convierte "9 a 10 am" en ["09:00", "10:00"]
     */
    private function parseTime(string $timeString): array
    {
        // Limpiar espacios extra y convertir a minúsculas
        $clean = strtolower(trim($timeString));
        // Separar por " a "
        $parts = explode(' a ', $clean);

        $startStr = trim($parts[0]);
        $endStr = trim($parts[1]);

        // Manejar sufijos AM/PM
        // Si el final tiene pm y el inicio no tiene sufijo:
        // Ej: "4 a 6 pm" -> start=4pm, end=6pm
        // Ej: "10 a 12 pm" -> start=10am, end=12pm (mediodía)

        $endIsPm = str_contains($endStr, 'pm');
        $endIsAm = str_contains($endStr, 'am');

        $startHasSuffix = str_contains($startStr, 'am') || str_contains($startStr, 'pm');

        // Formatear hora de inicio
        $startTime = $startStr;
        if (!$startHasSuffix) {
            // Heurística: Si es menor a 7 y el fin es PM, asumimos PM. (Ej: 4 a 6 pm)
            // Si es 9, 10, 11, asumimos AM aunque el fin sea PM (Ej: 10 a 12 pm)
            $firstHour = (int) explode(':', $startStr)[0];
            if ($endIsPm && $firstHour < 7) {
                $startTime .= ' pm';
            } else {
                $startTime .= ' am';
            }
        }

        // Formatear hora de fin (asegurar espacio antes de am/pm si falta)
        // Carbon es inteligente, pero ayudémosle un poco si el string es raro

        try {
            $start = Carbon::parse($startTime)->format('H:i');
            $end = Carbon::parse($endStr)->format('H:i');
        } catch (\Exception $e) {
            // Fallback por si acaso falla el parseo
            $start = '00:00';
            $end = '00:00';
        }

        return [$start, $end];
    }
}
