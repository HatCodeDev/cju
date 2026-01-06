<?php

namespace App\Models;

use App\Enums\AttendanceType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AttendanceLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'retiree_id',
        'type',
    ];

    protected $casts = [
        'type' => AttendanceType::class,
    ];

    public function retiree(): BelongsTo
    {
        return $this->belongsTo(Retiree::class);
    }
}
