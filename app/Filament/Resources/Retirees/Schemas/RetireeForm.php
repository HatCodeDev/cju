<?php

declare(strict_types=1);

namespace App\Filament\Resources\Retirees\Schemas;

use App\Enums\Gender;
use App\Enums\InsuranceType;
use App\Enums\RetireeType;
use App\Models\Retiree;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class RetireeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Ficha del Jubilado')
                    ->columnSpanFull()
                    ->persistTabInQueryString()
                    ->tabs([
                        Tab::make('General')
                            ->icon('heroicon-o-user')
                            ->schema([
                                Section::make('Identidad')
                                    ->columns(2)
                                    ->schema([
                                        FileUpload::make('photo_path')
                                            ->label('Fotografía')
                                            ->avatar()
                                            ->imageEditor()
                                            ->directory('retirees-photos')
                                            ->alignCenter()
                                            ->columnSpanFull(),

                                        TextInput::make('full_name')
                                            ->label('Nombre Completo')
                                            ->required()
                                            ->dehydrateStateUsing(fn (?string $state): string => Str::title($state ?? '')),

                                        TextInput::make('curp')
                                            ->label('CURP')
                                            ->required()
                                            ->length(18)
                                            ->unique(ignoreRecord: true)
                                            ->extraInputAttributes(['style' => 'text-transform: uppercase'])
                                            ->dehydrateStateUsing(fn (string $state): string => strtoupper($state)),

                                        DatePicker::make('birth_date')
                                            ->label('Fecha de Nacimiento')
                                            ->maxDate(now())
                                            ->required(),

                                        Select::make('gender')
                                            ->label('Sexo')
                                            ->options(Gender::class)
                                            ->native(false)
                                            ->required(),

                                        TextInput::make('phone')
                                            ->label('Teléfono Personal')
                                            ->tel()
                                            ->length(10)
                                            ->numeric()
                                            ->required(),
                                    ]),

                                Section::make('Clasificación Institucional')
                                    ->columns(2)
                                    ->schema([
                                        Select::make('retiree_type')
                                            ->label('Tipo de Jubilado')
                                            ->options(RetireeType::class)
                                            ->required()
                                            ->native(false)
                                            ->live()
                                            ->afterStateUpdated(fn (Set $set) => $set('worker_id', null)),

                                        TextInput::make('worker_id')
                                            ->label('ID Trabajador BUAP')
                                            ->prefix('#')
                                            // Corrección de lógica visible: Verifica tanto el Enum como el valor escalar
                                            ->visible(fn (Get $get) => $get('retiree_type') === RetireeType::Internal->value || $get('retiree_type') === RetireeType::Internal)
                                            ->required(fn (Get $get) => $get('retiree_type') === RetireeType::Internal->value || $get('retiree_type') === RetireeType::Internal),
                                    ]),
                            ]),

                        Tab::make('Salud')
                            ->icon('heroicon-o-heart')
                            ->schema([
                                Section::make('Datos Médicos')
                                    ->columns(2)
                                    ->schema([
                                        Select::make('insurance_type')
                                            ->label('Institución de Salud')
                                            ->options(InsuranceType::class)
                                            ->required()
                                            ->native(false)
                                            ->live(),

                                        TextInput::make('insurance_name')
                                            ->label('Especifique Seguro')
                                            ->visible(fn (Get $get) => $get('insurance_type') === InsuranceType::Other->value || $get('insurance_type') === InsuranceType::Other)
                                            ->required(fn (Get $get) => $get('insurance_type') === InsuranceType::Other->value || $get('insurance_type') === InsuranceType::Other),

                                        Textarea::make('allergies')
                                            ->label('Alergias')
                                            ->columnSpanFull(),
                                    ]),

                                Section::make('Contacto de Emergencia')
                                    ->relationship('emergencyContact')
                                    ->schema([
                                        Grid::make(2)->schema([
                                            TextInput::make('name')
                                                ->label('Nombre')
                                                ->required(),

                                            TextInput::make('phone')
                                                ->label('Teléfono')
                                                ->tel()
                                                ->required(),

                                            TextInput::make('relationship')
                                                ->label('Parentesco')
                                                ->required(),

                                            Toggle::make('is_university_worker')
                                                ->label('¿Trabaja en la Universidad?')
                                                ->live()
                                                ->inline(false),
                                        ]),

                                        TextInput::make('dependency')
                                            ->label('Dependencia / Facultad')
                                            ->visible(fn (Get $get) => $get('is_university_worker')),
                                    ]),
                            ]),

                        Tab::make('Actividades')
                            ->icon('heroicon-o-calendar-days')
                            ->schema([
                                Section::make('Servicios')
                                    ->schema([
                                        CheckboxList::make('medicalServices')
                                            ->hiddenLabel()
                                            ->relationship(titleAttribute: 'name')
                                            ->columns(2)
                                            ->bulkToggleable(),

                                        Textarea::make('other_services_notes')
                                            ->label('Notas Adicionales')
                                            ->rows(2),
                                    ]),

                                Section::make('Talleres')
                                    ->schema([
                                        CheckboxList::make('workshops')
                                            ->hiddenLabel()
                                            ->relationship(titleAttribute: 'name')
                                            ->columns(2)
                                            ->searchable()
                                            ->noSearchResultsMessage('No hay talleres disponibles'),
                                    ]),
                            ]),

                        Tab::make('Control de Acceso')
                            ->icon('heroicon-o-qr-code')
                            ->schema([
                                Section::make()
                                    ->columns(2)
                                    ->schema([
                                        Toggle::make('is_present')
                                            ->label('Asistencia Activa')
                                            ->onColor('success')
                                            ->offColor('danger')
                                            ->columnSpanFull(),

                                        TextInput::make('uuid')
                                            ->label('UUID (QR)')
                                            ->disabled()
                                            ->dehydrated(false)
                                            ->copyable(),

                                        Placeholder::make('created_at')
                                            ->label('Fecha de Registro')
                                            ->content(fn (?Retiree $record): string => $record?->created_at?->format('d/m/Y H:i') ?? '-'),
                                    ]),
                            ]),
                    ]),
            ]);
    }
}
