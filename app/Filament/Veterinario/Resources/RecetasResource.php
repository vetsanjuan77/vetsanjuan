<?php

namespace App\Filament\Veterinario\Resources;

use App\Filament\Veterinario\Resources\RecetasResource\Pages;
use App\Models\Receta;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Repeater;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\DatePicker;

class RecetasResource extends Resource
{
    protected static ?string $model = Receta::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationLabel = 'Mis Recetas';

    protected static ?string $modelLabel = 'Receta Médica';

    protected static ?string $pluralModelLabel = 'Recetas Médicas';

    protected static ?string $navigationGroup = 'Atención Médica';

    protected static ?int $navigationSort = 3;

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('veterinario_id', auth()->id());
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Información de la Receta')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Select::make('consulta_id')
                                    ->label('Consulta Médica')
                                    ->relationship('consulta', 'id')
                                    ->getOptionLabelFromRecordUsing(fn ($record) => 
                                        "#{$record->id} - {$record->cita->cliente->nombre} ({$record->cita->mascota->nombre})"
                                    )
                                    ->searchable()
                                    ->preload()
                                    ->required(),
                                
                                Select::make('veterinario_id')
                                    ->label('Veterinario')
                                    ->relationship('veterinario', 'name')
                                    ->default(auth()->id())
                                    ->disabled()
                                    ->required(),
                            ]),
                        
                        Grid::make(2)
                            ->schema([
                                DateTimePicker::make('fecha_emision')
                                    ->label('Fecha de Emisión')
                                    ->default(now())
                                    ->required(),
                                
                                DateTimePicker::make('fecha_vencimiento')
                                    ->label('Fecha de Vencimiento')
                                    ->default(now()->addDays(30)),
                            ]),
                    ]),

                Section::make('Medicamentos Prescritos')
                    ->schema([
                        Repeater::make('medicamentos')
                            ->label('Medicamentos')
                            ->schema([
                                Grid::make(3)
                                    ->schema([
                                        TextInput::make('nombre')
                                            ->label('Nombre del Medicamento')
                                            ->required(),
                                        
                                        TextInput::make('dosis')
                                            ->label('Dosis')
                                            ->required(),
                                        
                                        TextInput::make('frecuencia')
                                            ->label('Frecuencia')
                                            ->placeholder('Ej: Cada 8 horas')
                                            ->required(),
                                    ]),
                                
                                Grid::make(2)
                                    ->schema([
                                        TextInput::make('duracion')
                                            ->label('Duración del Tratamiento')
                                            ->placeholder('Ej: 7 días')
                                            ->required(),
                                        
                                        TextInput::make('cantidad')
                                            ->label('Cantidad a Dispensar')
                                            ->numeric(),
                                    ]),
                                
                                Textarea::make('instrucciones')
                                    ->label('Instrucciones Especiales')
                                    ->rows(2),
                            ])
                            ->collapsible()
                            ->itemLabel(fn (array $state): ?string => $state['nombre'] ?? null)
                            ->addActionLabel('Agregar Medicamento')
                            ->defaultItems(1)
                            ->required(),
                    ]),

                Section::make('Instrucciones Generales')
                    ->schema([
                        Textarea::make('instrucciones_generales')
                            ->label('Instrucciones Generales para el Propietario')
                            ->rows(4)
                            ->placeholder('Instrucciones sobre cuidados, alimentación, restricciones, etc.'),
                        
                        Textarea::make('observaciones')
                            ->label('Observaciones Adicionales')
                            ->rows(3),
                        
                        Grid::make(2)
                            ->schema([
                                Select::make('estado')
                                    ->label('Estado de la Receta')
                                    ->options([
                                        'activa' => 'Activa',
                                        'dispensada' => 'Dispensada',
                                        'vencida' => 'Vencida',
                                        'cancelada' => 'Cancelada',
                                    ])
                                    ->default('activa')
                                    ->required(),
                                
                                TextInput::make('numero_receta')
                                    ->label('Número de Receta')
                                    ->default(fn () => 'REC-' . now()->format('Ymd') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT))
                                    ->required(),
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('numero_receta')
                    ->label('N° Receta')
                    ->searchable()
                    ->sortable(),
                
                TextColumn::make('consulta.cita.cliente.nombre')
                    ->label('Cliente')
                    ->searchable()
                    ->sortable(),
                
                TextColumn::make('consulta.cita.mascota.nombre_mascota')
                    ->label('Mascota')
                    ->searchable()
                    ->sortable(),
                
                TextColumn::make('fecha_emision')
                    ->label('Fecha Emisión')
                    ->dateTime('d/m/Y')
                    ->sortable(),
                
                TextColumn::make('fecha_vencimiento')
                    ->label('Vencimiento')
                    ->dateTime('d/m/Y')
                    ->sortable(),
                
                TextColumn::make('estado')
                    ->label('Estado')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'activa' => 'success',
                        'dispensada' => 'info',
                        'vencida' => 'danger',
                        'cancelada' => 'gray',
                    }),
                
                TextColumn::make('medicamentos')
                    ->label('Medicamentos')
                    ->formatStateUsing(function ($state) {
                        if (is_array($state) && count($state) > 0) {
                            $count = count($state);
                            $first = $state[0]['nombre'] ?? 'N/A';
                            return $count > 1 ? "{$first} (+{$count} más)" : $first;
                        }
                        return 'Sin medicamentos';
                    })
                    ->limit(30),
                
                TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('estado')
                    ->label('Estado')
                    ->options([
                        'activa' => 'Activa',
                        'dispensada' => 'Dispensada',
                        'vencida' => 'Vencida',
                        'cancelada' => 'Cancelada',
                    ]),
                
                Filter::make('fecha_emision')
                    ->label('Fecha de Emisión')
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
                                fn (Builder $query, $date): Builder => $query->whereDate('fecha_emision', '>=', $date),
                            )
                            ->when(
                                $data['fecha_hasta'],
                                fn (Builder $query, $date): Builder => $query->whereDate('fecha_emision', '<=', $date),
                            );
                    }),
                
                Filter::make('fecha_vencimiento')
                    ->label('Fecha de Vencimiento')
                    ->form([
                        DatePicker::make('vencimiento_desde')
                            ->label('Desde'),
                        DatePicker::make('vencimiento_hasta')
                            ->label('Hasta'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['vencimiento_desde'],
                                fn (Builder $query, $date): Builder => $query->whereDate('fecha_vencimiento', '>=', $date),
                            )
                            ->when(
                                $data['vencimiento_hasta'],
                                fn (Builder $query, $date): Builder => $query->whereDate('fecha_vencimiento', '<=', $date),
                            );
                    }),
            ])
            ->actions([
                ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\Action::make('imprimir')
                        ->label('Imprimir')
                        ->icon('heroicon-o-printer')
                        ->color('info')
                        ->url(fn ($record) => route('recetas.print', $record))
                        ->openUrlInNewTab(),
                    Tables\Actions\DeleteAction::make(),
                ])
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('fecha_emision', 'desc');
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
            'index' => Pages\ListRecetas::route('/'),
            'create' => Pages\CreateRecetas::route('/create'),
            'view' => Pages\ViewRecetas::route('/{record}'),
            'edit' => Pages\EditRecetas::route('/{record}/edit'),
        ];
    }
}