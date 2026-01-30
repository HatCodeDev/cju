<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Workshop extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /* |--------------------------------------------------------------------------
    | Relaciones
    |--------------------------------------------------------------------------
    */

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function schedules(): HasMany
    {
        return $this->hasMany(WorkshopSchedule::class);
    }

    /**
     * Relación inversa: Obtener los jubilados inscritos en este taller.
     */
    public function retirees(): BelongsToMany
    {
        return $this->belongsToMany(Retiree::class, 'retiree_workshop')
            ->withTimestamps();
    }
}
