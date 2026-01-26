<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Traits\HasRoles;
use Filament\Models\Contracts\HasAvatar;
use Illuminate\Database\Eloquent\Casts\Attribute;

class User extends Authenticatable
{
    use HasRoles;
    protected $fillable = [
        'name',
        'email',
        'password',
        'avatar_url',
    ];


    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function workshops(): HasMany
    {
        return $this->hasMany(Workshop::class, 'teacher_id');
    }

    protected function avatarUrl(): Attribute
    {
        return Attribute::make(
            get: function (?string $value) {
                if (! $value) {
                    return null;
                }
                if (filter_var($value, FILTER_VALIDATE_URL)) {
                    return $value;
                }
                return Storage::disk('public')->url($value);
            }
        );
    }

    public function getFilamentAvatarUrl(): ?string
    {
        return $this->avatar_url;
    }

}
