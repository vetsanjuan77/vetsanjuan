<?php

namespace App\Filament\Veterinario\Resources;

use App\Filament\Veterinario\Resources\MisCitasResource\Pages;
use App\Models\Citas;
use App\Models\Cliente;
use App\Models\Mascotas;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Grid;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\DatePicker;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\TextColumn;

class MisCitasResource extends Resource
{
    protected static ?string $model = Citas::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';

    protected static ?string $navigationLabel = 'Mis Citas';

    protected static ?string $modelLabel = 'Cita';

    protected static ?string $pluralModelLabel = 'Mis Citas';

    protected static ?string $navigationGroup = 'Mi Trabajo';

    protected static ?int $navigationSort = 1;

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('veterinario_id', Auth::id());
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Información de la Cita')
                    ->description('Datos principales de la cita veterinaria')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Forms\Components\Select::make('cliente_id')
                                    ->label('Cliente')
                                    ->relationship('cliente', 'nombre')
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->live()
                                    ->afterStateUpdated(function (callable $set, $state) {
                                        if ($state) {
                                            $set('mascota_id', null);
                                        }
                                    }),

                                Forms\Components\Select::make('mascota_id')
                                    ->label('Mascota')
                                    ->relationship('mascota', 'nombre_mascota')
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->options(function (callable $get) {
                                        $clienteId = $get('cliente_id');
                                        if (!$clienteId) {
                                            return [];
                                        }
                                        return Mascotas::where('cliente_id', $clienteId)
                                            ->pluck('nombre_mascota', 'id');
                                    }),
                            ]),

                        Forms\Components\Hidden::make('veterinario_id')
                            ->default(Auth::id()),

                        Grid::make(2)
                            ->schema([
                                Forms\Components\Select::make('tipo_servicio')
                                    ->label('Tipo de Servicio')
                                    ->options([
                                        'consulta' => 'Consulta General',
                                        'vacunacion' => 'Vacunación',
                                        'cirugia' => 'Cirugía',
                                        'emergencia' => 'Emergencia',
                                        'control' => 'Control',
                                    ])
                                    ->required()
                                    ->native(false),

                                Forms\Components\Select::make('estado')
                                    ->label('Estado')
                                    ->options([
                                        'programada' => 'Programada',
                                        'confirmada' => 'Confirmada',
                                        'en_proceso' => 'En Proceso',
                                        'completada' => 'Completada',
                                        'cancelada' => 'Cancelada',
                                        'no_asistio' => 'No Asistió',
                                    ])
                                    ->default('programada')
                                    ->required()
                                    ->native(false),
                            ]),
                    ])
                    ->columns(1),

                Section::make('Fecha y Hora')
                    ->description('Programación de la cita')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Forms\Components\DatePicker::make('fecha')
                                    ->label('Fecha de la Cita')
                                    ->required()
                                    ->native(false)
                                    ->displayFormat('d/m/Y')
                                    ->minDate(now()),

                                Forms\Components\TimePicker::make('hora')
                                    ->label('Hora de la Cita')
                                    ->required()
                                    ->native(false)
                                    ->minutesStep(15),
                            ]),
                    ])
                    ->columns(1),

                Section::make('Información Adicional')
                    ->description('Detalles y observaciones de la cita')
                    ->schema([
                        Forms\Components\Textarea::make('motivo')
                            ->label('Motivo de la Consulta')
                            ->rows(3)
                            ->maxLength(500)
                            ->columnSpanFull(),

                        Forms\Components\Textarea::make('observaciones')
                            ->label('Observaciones')
                            ->rows(3)
                            ->maxLength(500)
                            ->columnSpanFull(),

                        Forms\Components\TextInput::make('precio')
                            ->label('Precio de la Consulta')
                            ->numeric()
                            ->prefix('$')
                            ->step(0.01)
                            ->minValue(0),
                    ])
                    ->columns(1),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('fecha')
                    ->label('Fecha')
                    ->date('d/m/Y')
                    ->sortable()
                    ->weight(FontWeight::Medium),

                TextColumn::make('hora')
                    ->label('Hora')
                    ->time('H:i')
                    ->sortable(),

                TextColumn::make('cliente.nombre')
                    ->label('Cliente')
                    ->searchable()
                    ->sortable()
                    ->formatStateUsing(fn ($record) => $record->cliente->nombre . ' ' . $record->cliente->apellido),

                TextColumn::make('mascota.nombre_mascota')
                    ->label('Mascota')
                    ->searchable()
                    ->sortable()
                    ->formatStateUsing(fn ($record) => $record->mascota->nombre_mascota . ' (' . $record->mascota->especie . ')'),

                TextColumn::make('tipo_servicio')
                    ->label('Servicio')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'consulta' => 'info',
                        'vacunacion' => 'success',
                        'cirugia' => 'warning',
                        'emergencia' => 'danger',
                        'control' => 'gray',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'consulta' => 'Consulta',
                        'vacunacion' => 'Vacunación',
                        'cirugia' => 'Cirugía',
                        'emergencia' => 'Emergencia',
                        'control' => 'Control',
                        default => $state,
                    }),

                TextColumn::make('estado')
                    ->label('Estado')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'programada' => 'gray',
                        'confirmada' => 'info',
                        'en_proceso' => 'warning',
                        'completada' => 'success',
                        'cancelada' => 'danger',
                        'no_asistio' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'programada' => 'Programada',
                        'confirmada' => 'Confirmada',
                        'en_proceso' => 'En Proceso',
                        'completada' => 'Completada',
                        'cancelada' => 'Cancelada',
                        'no_asistio' => 'No Asistió',
                        default => $state,
                    }),

                TextColumn::make('motivo')
                    ->label('Motivo')
                    ->limit(30)
                    ->tooltip(function (TextColumn $column): ?string {
                        $state = $column->getState();
                        if (strlen($state) <= 30) {
                            return null;
                        }
                        return $state;
                    }),

                TextColumn::make('precio')
                    ->label('Precio')
                    ->money('USD')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('estado')
                    ->label('Estado')
                    ->options([
                        'programada' => 'Programada',
                        'confirmada' => 'Confirmada',
                        'en_proceso' => 'En Proceso',
                        'completada' => 'Completada',
                        'cancelada' => 'Cancelada',
                        'no_asistio' => 'No Asistió',
                    ]),

                SelectFilter::make('tipo_servicio')
                    ->label('Tipo de Servicio')
                    ->options([
                        'consulta' => 'Consulta General',
                        'vacunacion' => 'Vacunación',
                        'cirugia' => 'Cirugía',
                        'emergencia' => 'Emergencia',
                        'control' => 'Control',
                    ]),

                Filter::make('fecha')
                    ->form([
                        DatePicker::make('fecha_desde')
                            ->label('Desde'),
                        DatePicker::make('fecha_hasta')
                            ->label('Hasta'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['fecha_desde'],
                                fn (Builder $query, $date): Builder => $query->whereDate('fecha', '>=', $date),
                            )
                            ->when(
                                $data['fecha_hasta'],
                                fn (Builder $query, $date): Builder => $query->whereDate('fecha', '<=', $date),
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\Action::make('iniciar_consulta')
                    ->label('Iniciar')
                    ->icon('heroicon-m-play')
                    ->color('success')
                    ->visible(fn ($record) => in_array($record->estado, ['programada', 'confirmada']))
                    ->action(function ($record) {
                        $record->update(['estado' => 'en_proceso']);
                    }),

                Tables\Actions\Action::make('completar')
                    ->label('Completar')
                    ->icon('heroicon-m-check')
                    ->color('primary')
                    ->visible(fn ($record) => $record->estado === 'en_proceso')
                    ->action(function ($record) {
                        $record->update(['estado' => 'completada']);
                    }),

                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('fecha', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMisCitas::route('/'),
            'create' => Pages\CreateMisCitas::route('/create'),
            'view' => Pages\ViewMisCitas::route('/{record}'),
            'edit' => Pages\EditMisCitas::route('/{record}/edit'),
        ];
    }
}