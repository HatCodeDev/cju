<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\Gender;
use App\Enums\InsuranceType;
use App\Enums\RetireeType; // Antes PatientType
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Retiree extends Model
{
    use HasFactory, SoftDeletes, HasUuids;

    // Usamos guarded vacío para permitir asignación masiva de los nuevos campos
    // sin tener que estar actualizando el array fillable a cada rato.
    protected $guarded = [];

    protected $casts = [
        'is_present' => 'boolean',
        'birth_date' => 'date',
        // Enums
        'gender' => Gender::class,
        'retiree_type' => RetireeType::class,     // Nuevo Enum normalizado
        'insurance_type' => InsuranceType::class, // Nuevo Enum normalizado
    ];

    /**
     * Necesario para el Trait HasUuids
     */
    public function uniqueIds(): array
    {
        return ['uuid'];
    }

    /**
     * Accessor: Edad calculada dinámicamente.
     * Uso: $retiree->age
     */
    protected function age(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->birth_date ? Carbon::parse($this->birth_date)->age : null,
        );
    }

    /* |--------------------------------------------------------------------------
    | Relaciones Existentes (Legacy Support)
    |--------------------------------------------------------------------------
    */

    public function logs(): HasMany
    {
        return $this->hasMany(AttendanceLog::class)->latest();
    }

    /* |--------------------------------------------------------------------------
    | Nuevas Relaciones (Normalización)
    |--------------------------------------------------------------------------
    */

    /**
     * Relación 1:1 con Contacto de Emergencia.
     * Al borrar al jubilado, se borra el contacto (configurado en migración).
     */
    public function emergencyContact(): HasOne
    {
        return $this->hasOne(EmergencyContact::class);
    }

    /**
     * Relación N:M con Servicios Médicos (Pivote).
     */
    public function medicalServices(): BelongsToMany
    {
        return $this->belongsToMany(MedicalService::class, 'medical_service_retiree')
            ->withTimestamps();
    }

    /**
     * Relación N:M con Talleres (Pivote).
     */
    public function workshops(): BelongsToMany
    {
        return $this->belongsToMany(Workshop::class, 'retiree_workshop')
            ->withTimestamps();
    }
}
