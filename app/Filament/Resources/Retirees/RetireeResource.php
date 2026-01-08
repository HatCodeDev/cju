<?php

declare(strict_types=1);

namespace App\Filament\Resources\Retirees;

use App\Filament\Resources\Retirees\Pages;
use App\Models\Retiree;
use App\Enums\Gender;
use App\Enums\PatientType;
use Filament\Forms;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Database\Eloquent\Collection;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Str;
use BackedEnum;
use UnitEnum;

class RetireeResource extends Resource
{
    protected static ?string $model = Retiree::class;
    protected static string | BackedEnum | null $navigationIcon  = 'heroicon-o-user-group';
    protected static string | UnitEnum | null $navigationGroup = 'Gestión';
    protected static ?string $modelLabel = 'Jubilado';
    protected static ?string $pluralModelLabel = 'Jubilados';
    protected static ?string $recordTitleAttribute = 'full_name';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Información Personal')
                    ->schema([
                        Forms\Components\FileUpload::make('photo_path')
                            ->label('Fotografía')
                            ->avatar()
                            ->imageEditor()
                            ->directory('retirees-photos')
                            ->columnSpanFull(),

                        Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('full_name')
                                    ->label('Nombre Completo')
                                    ->required()
                                    ->maxLength(255)
                                    ->dehydrateStateUsing(fn (?string $state): string => Str::title($state ?? '')),

                                Forms\Components\TextInput::make('curp')
                                    ->label('CURP')
                                    ->required()
                                    ->minLength(18)
                                    ->maxLength(18)
                                    ->unique(ignoreRecord: true)
                                    ->extraInputAttributes(['style' => 'text-transform: uppercase'])
                                    ->dehydrateStateUsing(fn (string $state): string => strtoupper($state))
                                    ->placeholder('CLAVE ÚNICA DE REGISTRO DE POBLACIÓN'),

                                Forms\Components\Select::make('patient_type')
                                    ->label('Tipo de Paciente')
                                    ->options(PatientType::class)
                                    ->native(false)
                                    ->required(),
                            ]),

                        Grid::make(2)
                            ->schema([
                                Forms\Components\Select::make('gender')
                                    ->label('Sexo')
                                    ->options(Gender::class)
                                    ->native(false),

                                Forms\Components\DatePicker::make('birth_date')
                                    ->label('Fecha de Nacimiento')
                                    ->maxDate(now())
                                    ->required(),
                            ]),

                        Forms\Components\Toggle::make('is_present')
                            ->label('¿Está presente hoy?')
                            ->onColor('success')
                            ->offColor('danger')
                            ->default(false),
                    ]),

                Section::make('Datos de Emergencia y Médicos')
                    ->schema([
                        Forms\Components\TextInput::make('emergency_contact1')
                            ->label('Contacto de Emergencia 1')
                            ->tel()
                            ->numeric()
                            ->minLength(10)
                            ->maxLength(10)
                            ->required()
                            ->placeholder('10 dígitos sin espacios'),

                        Forms\Components\TextInput::make('emergency_contact2')
                            ->label('Contacto de Emergencia 2')
                            ->tel()
                            ->numeric()
                            ->minLength(10)
                            ->maxLength(10)
                            ->placeholder('Opcional'),

                        Forms\Components\Textarea::make('medical_notes')
                            ->label('Notas Médicas')
                            ->rows(3)
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('photo_path')
                    ->label('Foto')
                    ->circular(),

                Tables\Columns\TextColumn::make('full_name')
                    ->label('Nombre')
                    ->searchable()
                    ->sortable()
                    ->description(fn (Retiree $record) => $record->gender?->getLabel()),

                Tables\Columns\TextColumn::make('curp')
                    ->label('CURP')
                    ->searchable()
                    ->copyable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('uuid')
                    ->label('UUID')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('patient_type')
                    ->label('Tipo')
                    ->badge()
                    ->sortable(),

                Tables\Columns\TextColumn::make('age_calc')
                    ->label('Edad')
                    ->state(fn (Retiree $record) => $record->age ? $record->age . ' años' : 'Sin fecha')
                    ->sortable(query: function (Builder $query, string $direction): Builder {
                        return $query->orderBy('birth_date', $direction);
                    }),

                Tables\Columns\IconColumn::make('is_present')
                    ->label('Asistencia')
                    ->boolean()
                    ->sortable(),

                Tables\Columns\TextColumn::make('emergency_contact1')
                    ->label('Tel. Emergencia')
                    ->icon('heroicon-m-phone')
                    ->copyable()
                    ->formatStateUsing(fn (string $state): string => preg_replace("/^(\d{3})(\d{3})(\d{4})$/", "($1) $2-$3", $state)), // Formato visual (XXX) XXX-XXXX
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
                Tables\Filters\SelectFilter::make('patient_type')
                    ->label('Tipo de Paciente')
                    ->options(PatientType::class),
            ])
            ->recordActions([
                Action::make('print_qr')
                    ->label('PDF')
                    ->icon('heroicon-o-printer')
                    ->color('warning')
                    ->action(function (Retiree $record) {
                        $pdf = Pdf::loadView('pdf.retiree-card', ['retirees' => [$record]]);
                        $filename = Str::slug($record->full_name) . '-QR.pdf';
                        return response()->streamDownload(function () use ($pdf) { echo $pdf->output(); }, $filename);
                    }),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                    BulkAction::make('print_selected')
                        ->label('Imprimir Credenciales')
                        ->icon('heroicon-o-printer')
                        ->color('warning')
                        ->action(function (Collection $records) {
                            $pdf = Pdf::loadView('pdf.retiree-card', ['retirees' => $records]);
                            $filename = 'Credenciales-Masivas-' . date('Y-m-d-His') . '.pdf';
                            return response()->streamDownload(function () use ($pdf) { echo $pdf->output(); }, $filename);
                        })
                        ->deselectRecordsAfterCompletion(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRetirees::route('/'),
            'create' => Pages\CreateRetiree::route('/create'),
            'edit' => Pages\EditRetiree::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
