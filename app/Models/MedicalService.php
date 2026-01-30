<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class MedicalService extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Relación inversa: Un servicio médico lo usan muchos jubilados.
     */
    public function retirees(): BelongsToMany
    {
        return $this->belongsToMany(Retiree::class, 'medical_service_retiree');
    }
}
