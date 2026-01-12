<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
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

    protected function avatarUrl(): Attribute
    {
        return Attribute::make(
            get: function (?string $value) {
                if (! $value) {
                    return null;
                }

                // Si ya es una URL completa (ej. http...), la devolvemos tal cual
                if (filter_var($value, FILTER_VALIDATE_URL)) {
                    return $value;
                }

                // Si no, construimos la URL pública
                // Usamos 'public' disk explícitamente como vimos en Tinker
                return Storage::disk('public')->url($value);
            }
        );
    }

    // -------------------------------------------------------------------------
    // Implementación de Filament
    // -------------------------------------------------------------------------

    public function getFilamentAvatarUrl(): ?string
    {
        // Al llamar a $this->avatar_url aquí, el Accessor de arriba se ejecuta automáticamente.
        return $this->avatar_url;
    }

}
