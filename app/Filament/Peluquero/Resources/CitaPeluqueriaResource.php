<?php

namespace App\Filament\Peluquero\Resources;

use App\Filament\Peluquero\Resources\CitaPeluqueriaResource\Pages;
use App\Filament\Peluquero\Resources\CitaPeluqueriaResource\RelationManagers;
use App\Models\CitaPeluqueria;
use App\Models\Cliente;
use App\Models\Mascotas;
use App\Models\Servicios;
use App\Services\Scheduling\SlotService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CitaPeluqueriaResource extends Resource
{
    protected static ?string $model = CitaPeluqueria::class;

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('peluquero_id', auth()->id());
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
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
                
                Forms\Components\Select::make('servicio_id')
                    ->label('Servicio')
                    ->relationship('servicio', 'nombre')
                    ->searchable()
                    ->preload()
                    ->required(),
                
                // Reemplazamos DateTimePicker de columna inexistente por campos reales
                Forms\Components\DatePicker::make('fecha')
                    ->label('Fecha')
                    ->reactive()
                    ->required(),
                Forms\Components\Select::make('hora')
                    ->label('Hora')
                    ->reactive()
                    ->options(fn (callable $get) => SlotService::availableSlots(auth()->id(), 'peluquero', $get('fecha')))
                    ->disabled(fn (callable $get) => empty($get('fecha')))
                    ->required(),
                
                Forms\Components\Select::make('estado')
                    ->label('Estado')
                    ->options([
                        'programada' => 'Programada',
                        'confirmada' => 'Confirmada',
                        'en_proceso' => 'En Proceso',
                        'completada' => 'Completada',
                        'cancelada' => 'Cancelada',
                    ])
                    ->default('programada')
                    ->required(),
                
                Forms\Components\Textarea::make('observaciones')
                    ->label('Observaciones')
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
                Tables\Columns\TextColumn::make('nombre_cliente')
                    ->label('Nombre Cliente')
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('correo_cliente')
                    ->label('Correo')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('telefono_cliente')
                    ->label('Teléfono')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\IconColumn::make('tiene_nudos')
                    ->label('Nudos')
                    ->boolean(),
                Tables\Columns\TextColumn::make('reserva_consumibles')
                    ->label('Reserva')
                    ->money('COP')
                    ->sortable(),
                Tables\Columns\TextColumn::make('mascota.nombre_mascota')
                    ->label('Mascota')
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('servicio.nombre')
                    ->label('Servicio')
                    ->searchable()
                    ->sortable(),
                
                // Columna combinada de fecha y hora utilizando los campos reales para ordenar
                Tables\Columns\TextColumn::make('fecha_hora')
                    ->label('Fecha y Hora')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(query: function (Builder $query, string $direction): Builder {
                        return $query->orderBy('fecha', $direction)->orderBy('hora', $direction);
                    }),
                
                Tables\Columns\BadgeColumn::make('estado')
                    ->label('Estado')
                    ->colors([
                        'warning' => 'programada',
                        'info' => 'confirmada',
                        'primary' => 'en_proceso',
                        'success' => 'completada',
                        'danger' => 'cancelada',
                    ]),
                
                Tables\Columns\TextColumn::make('precio')
                    ->label('Precio')
                    ->money('USD')
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Creada')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
                    ]),
                
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
            ->modifyQueryUsing(function (Builder $query) {
                $query->orderByDesc('fecha')->orderByDesc('hora');
            })
            ->defaultSort('fecha', 'desc');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCitaPeluquerias::route('/'),
            'create' => Pages\CreateCitaPeluqueria::route('/create'),
            'edit' => Pages\EditCitaPeluqueria::route('/{record}/edit'),
        ];
    }
}
