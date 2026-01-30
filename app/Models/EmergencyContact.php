<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmergencyContact extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'is_university_worker' => 'boolean',
    ];

    public function retiree(): BelongsTo
    {
        return $this->belongsTo(Retiree::class);
    }
}
