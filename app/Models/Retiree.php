<?php

namespace App\Models;

use App\Enums\Gender;
use App\Enums\PatientType;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class Retiree extends Model
{
    use HasFactory, SoftDeletes, HasUuids;

    protected $fillable = [
        'uuid',
        'full_name',
        // --- CORRECCIÓN: Agregamos los campos permitidos ---
        'patient_type',
        'gender',
        'birth_date',
        // --------------------------------------------------
        'emergency_contact1',
        'emergency_contact2',
        'medical_notes',
        'photo_path',
        'is_present',
    ];

    protected $casts = [
        'is_present' => 'boolean',
        // --- CORRECCIÓN: Restauramos el tipado para Enums ---
        'patient_type' => PatientType::class,
        'gender' => Gender::class,
        'birth_date' => 'date',
    ];

    public function uniqueIds(): array
    {
        return ['uuid'];
    }

    public function logs(): HasMany
    {
        return $this->hasMany(AttendanceLog::class)->latest();
    }

    // --- CORRECCIÓN: Restauramos el cálculo de edad ---
    public function getAgeAttribute(): ?int
    {
        return $this->birth_date ? Carbon::parse($this->birth_date)->age : null;
    }
}
