<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CitasResource\Pages;
use App\Filament\Resources\CitasResource\RelationManagers;
use App\Models\Citas;
use App\Services\Scheduling\SlotService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CitasResource extends Resource
{
    protected static ?string $model = Citas::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar';

    protected static ?string $navigationLabel = 'Citas Veterinarias';

    protected static ?string $modelLabel = 'Cita Veterinaria';

    protected static ?string $pluralModelLabel = 'Citas Veterinarias';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\DatePicker::make('fecha')
                    ->label('Fecha')
                    ->reactive()
                    ->required(),
                
                Forms\Components\Select::make('hora')
                    ->label('Hora')
                    ->reactive()
                    ->options(fn (callable $get) => SlotService::availableSlots($get('veterinario_id'), 'veterinario', $get('fecha')))
                    ->disabled(fn (callable $get) => empty($get('veterinario_id')) || empty($get('fecha')))
                    ->required(),
                
                Forms\Components\Select::make('veterinario_id')
                    ->label('Veterinario')
                    ->options(function () {
                        return \App\Models\User::where('role', 'veterinario')
                            ->where('estado', 'activo')
                            ->pluck('name', 'id');
                    })
                    ->reactive()
                    ->searchable()
                    ->required(),
                
                Forms\Components\Select::make('cliente_id')
                    ->label('Cliente')
                    ->relationship('cliente', 'nombre')
                    ->searchable()
                    ->preload()
                    ->required(),
                
                Forms\Components\Select::make('mascota_id')
                    ->label('Mascota')
                    ->relationship('mascota', 'nombre_mascota')
                    ->searchable()
                    ->preload()
                    ->required(),
                
                Forms\Components\Select::make('tipo_servicio_id')
                    ->label('Servicio')
                    ->relationship('servicio', 'nombre')
                    ->searchable()
                    ->preload()
                    ->required(),
                
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
                    ->required(),
                
                Forms\Components\Textarea::make('motivo')
                    ->label('Motivo de la consulta')
                    ->rows(3),
                
                Forms\Components\TextInput::make('precio')
                    ->label('Precio')
                    ->numeric()
                    ->prefix('$'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('fecha')
                    ->label('Fecha')
                    ->date('d/m/Y')
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('hora')
                    ->label('Hora')
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('cliente.nombre')
                    ->label('Cliente')
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('mascota.nombre_mascota')
                    ->label('Mascota')
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('veterinario.name')
                    ->label('Veterinario')
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('servicio.nombre')
                    ->label('Servicio')
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\BadgeColumn::make('estado')
                    ->label('Estado')
                    ->colors([
                        'warning' => 'programada',
                        'info' => 'confirmada',
                        'primary' => 'en_proceso',
                        'success' => 'completada',
                        'danger' => 'cancelada',
                        'secondary' => 'no_asistio',
                    ]),
                
                Tables\Columns\TextColumn::make('precio')
                    ->label('Precio')
                    ->money('USD')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('estado')
                    ->label('Estado')
                    ->options([
                        'programada' => 'Programada',
                        'confirmada' => 'Confirmada',
                        'en_proceso' => 'En Proceso',
                        'completada' => 'Completada',
                        'cancelada' => 'Cancelada',
                        'no_asistio' => 'No Asistió',
                    ]),
                
                Tables\Filters\SelectFilter::make('veterinario_id')
                    ->label('Veterinario')
                    ->relationship('veterinario', 'name'),
                
                Tables\Filters\Filter::make('fecha_hoy')
                    ->label('Hoy')
                    ->query(fn (Builder $query): Builder => $query->whereDate('fecha', today())),
                
                Tables\Filters\Filter::make('esta_semana')
                    ->label('Esta semana')
                    ->query(fn (Builder $query): Builder => $query->whereBetween('fecha', [now()->startOfWeek(), now()->endOfWeek()])),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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
            'index' => Pages\ListCitas::route('/'),
            'create' => Pages\CreateCitas::route('/create'),
            'edit' => Pages\EditCitas::route('/{record}/edit'),
        ];
    }
}
