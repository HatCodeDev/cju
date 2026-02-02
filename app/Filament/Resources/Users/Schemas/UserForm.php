<?php

declare(strict_types=1);

namespace App\Filament\Resources\Users\Schemas;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Operation;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Información del Usuario')
                    ->columnSpanFull()
                    ->description('Administre los detalles de la cuenta y los roles de acceso.')
                    ->icon('heroicon-o-user')
                    ->columns(2) // Grid de 2 columnas dentro de la sección
                    ->schema([
                        FileUpload::make('avatar_url')
                            ->label('Fotografía de Perfil')
                            ->directory('user-avatars')
                            ->disk('public')
                            ->visibility('public')
                            ->avatar() // Formato circular en preview
                            ->image()
                            ->imageEditor()
                            ->columnSpanFull() // Centrado arriba o mover a una columna lateral
                            ->maxSize(2048),
                        TextInput::make('name')
                            ->label('Nombre Completo')
                            ->dehydrateStateUsing(fn (?string $state): string => Str::title($state ?? ''))
                            ->required()
                            ->maxLength(255),

                        TextInput::make('email')
                            ->label('Correo Electrónico')
                            ->email()
                            ->required()
                            ->unique(ignoreRecord: true) // Ignora el ID actual al editar
                            ->maxLength(255),

                        // Lógica de Contraseña Refactorizada
                        TextInput::make('password')
                            ->label('Contraseña')
                            ->password()
                            ->revealable()
                            ->dehydrated(fn (?string $state): bool => filled($state)) // Solo guardar si tiene valor
                            ->required(fn (string $operation): bool => $operation === Operation::Create->value) // Obligatorio solo al crear
                            ->maxLength(255)
                            ->dehydrateStateUsing(fn (string $state): string => Hash::make($state)) // Asegurar Hash
                            ->columnSpan(1),

                        // Integración con Filament Shield
                        Select::make('roles')
                            ->label('Roles Asignados')
                            ->relationship('roles', 'name') // Relación Spatie
                            ->multiple()
                            ->preload()
                            ->searchable()
                            ->columnSpanFull(), // Ocupa todo el ancho abajo
                    ]),
            ]);
    }
}
