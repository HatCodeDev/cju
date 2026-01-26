<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\DayOfWeek;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WorkshopSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'workshop_id',
        'location_id',
        'day_of_week',
        'start_time',
        'end_time',
    ];

    protected $casts = [
        'day_of_week' => DayOfWeek::class,
        'start_time' => 'string',
        'end_time' => 'string',
    ];

    /* -----------------------------------------------------------------
     | Relaciones
     | -----------------------------------------------------------------
     */

    public function workshop(): BelongsTo
    {
        return $this->belongsTo(Workshop::class);
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    /* -----------------------------------------------------------------
     | Scopes (Lógica de Negocio)
     | -----------------------------------------------------------------
     */

    public function scopeOverlapping(
        Builder $query,
        mixed $day,
        string $startTime,
        string $endTime,
        int $locationId
    ): void {
        $query->where('location_id', $locationId)
            ->where('day_of_week', $day instanceof DayOfWeek ? $day->value : $day)
            ->where(function (Builder $q) use ($startTime, $endTime) {
                $q->where('start_time', '<', $endTime)
                    ->where('end_time', '>', $startTime);
            });
    }
}
