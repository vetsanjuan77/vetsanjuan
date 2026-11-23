<?php

namespace App\Filament\Veterinario\Resources;

use App\Filament\Veterinario\Resources\MisConsultasResource\Pages;
use App\Models\ConsultaMedica;
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
use Filament\Forms\Components\Toggle;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\DatePicker;

class MisConsultasResource extends Resource
{
    protected static ?string $model = ConsultaMedica::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static ?string $navigationLabel = 'Mis Consultas';

    protected static ?string $modelLabel = 'Consulta Médica';

    protected static ?string $pluralModelLabel = 'Consultas Médicas';

    protected static ?string $navigationGroup = 'Atención Médica';

    protected static ?int $navigationSort = 2;

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('veterinario_id', auth()->id());
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Información de la Consulta')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Select::make('cita_id')
                                    ->label('Cita')
                                    ->relationship('cita', 'id')
                                    ->getOptionLabelFromRecordUsing(fn ($record) => 
                                        "#{$record->id} - {$record->cliente->nombre} ({$record->mascota->nombre})"
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
                        
                        DateTimePicker::make('fecha_consulta')
                            ->label('Fecha y Hora de Consulta')
                            ->default(now())
                            ->required(),
                    ]),

                Section::make('Examen Clínico')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextInput::make('peso')
                                    ->label('Peso (kg)')
                                    ->numeric()
                                    ->step(0.1),
                                
                                TextInput::make('temperatura')
                                    ->label('Temperatura (°C)')
                                    ->numeric()
                                    ->step(0.1),
                                
                                TextInput::make('frecuencia_cardiaca')
                                    ->label('Frecuencia Cardíaca')
                                    ->numeric(),
                            ]),
                        
                        Textarea::make('motivo_consulta')
                            ->label('Motivo de Consulta')
                            ->rows(3)
                            ->required(),
                        
                        Textarea::make('sintomas')
                            ->label('Síntomas Observados')
                            ->rows(3),
                        
                        Textarea::make('examen_fisico')
                            ->label('Examen Físico')
                            ->rows(4),
                    ]),

                Section::make('Diagnóstico y Tratamiento')
                    ->schema([
                        Textarea::make('diagnostico')
                            ->label('Diagnóstico')
                            ->rows(3)
                            ->required(),
                        
                        Textarea::make('tratamiento')
                            ->label('Tratamiento Prescrito')
                            ->rows(4),
                        
                        Textarea::make('observaciones')
                            ->label('Observaciones Adicionales')
                            ->rows(3),
                        
                        Grid::make(2)
                            ->schema([
                                DateTimePicker::make('proxima_cita')
                                    ->label('Próxima Cita Sugerida'),
                                
                                Toggle::make('requiere_seguimiento')
                                    ->label('Requiere Seguimiento')
                                    ->default(false),
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),
                
                TextColumn::make('cita.cliente.nombre')
                    ->label('Cliente')
                    ->searchable()
                    ->sortable(),
                
                TextColumn::make('cita.mascota.nombre_mascota')
                    ->label('Mascota')
                    ->searchable()
                    ->sortable(),
                
                TextColumn::make('fecha_consulta')
                    ->label('Fecha Consulta')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
                
                TextColumn::make('motivo_consulta')
                    ->label('Motivo')
                    ->limit(30)
                    ->tooltip(function (TextColumn $column): ?string {
                        $state = $column->getState();
                        return strlen($state) > 30 ? $state : null;
                    }),
                
                TextColumn::make('diagnostico')
                    ->label('Diagnóstico')
                    ->limit(40)
                    ->tooltip(function (TextColumn $column): ?string {
                        $state = $column->getState();
                        return strlen($state) > 40 ? $state : null;
                    }),
                
                TextColumn::make('requiere_seguimiento')
                    ->label('Seguimiento')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        '1' => 'warning',
                        '0' => 'success',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        '1' => 'Requerido',
                        '0' => 'No requerido',
                    }),
                
                TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Filter::make('fecha_consulta')
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
                                fn (Builder $query, $date): Builder => $query->whereDate('fecha_consulta', '>=', $date),
                            )
                            ->when(
                                $data['fecha_hasta'],
                                fn (Builder $query, $date): Builder => $query->whereDate('fecha_consulta', '<=', $date),
                            );
                    })
                    ->label('Fecha de Consulta'),
                
                SelectFilter::make('requiere_seguimiento')
                    ->label('Requiere Seguimiento')
                    ->options([
                        '1' => 'Sí',
                        '0' => 'No',
                    ]),
            ])
            ->actions([
                ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                ])
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('fecha_consulta', 'desc');
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
            'index' => Pages\ListMisConsultas::route('/'),
            'create' => Pages\CreateMisConsultas::route('/create'),
            'view' => Pages\ViewMisConsultas::route('/{record}'),
            'edit' => Pages\EditMisConsultas::route('/{record}/edit'),
        ];
    }
}