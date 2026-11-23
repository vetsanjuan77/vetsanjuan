<?php

namespace App\Filament\Resources;

use App\Filament\Resources\HorarioTrabajoResource\Pages;
use App\Models\HorarioTrabajo;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class HorarioTrabajoResource extends Resource
{
    protected static ?string $model = HorarioTrabajo::class;

    protected static ?string $navigationIcon = 'heroicon-o-clock';
    protected static ?string $navigationLabel = 'Horarios de Trabajo';
    protected static ?string $modelLabel = 'Horario de Trabajo';
    protected static ?string $pluralModelLabel = 'Horarios de Trabajo';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('user_id')
                ->label('Profesional')
                ->options(function () {
                    return User::query()
                        ->whereIn('role', ['veterinario', 'peluquero'])
                        ->where('estado', 'activo')
                        ->pluck('name', 'id');
                })
                ->searchable()
                ->required(),
            Forms\Components\Select::make('rol')
                ->label('Rol')
                ->options([
                    'veterinario' => 'Veterinario',
                    'peluquero' => 'Peluquero',
                ])
                ->required(),
            Forms\Components\Select::make('dia_semana')
                ->label('Día de la semana')
                ->options([
                    0 => 'Domingo',
                    1 => 'Lunes',
                    2 => 'Martes',
                    3 => 'Miércoles',
                    4 => 'Jueves',
                    5 => 'Viernes',
                    6 => 'Sábado',
                ])
                ->required(),
            Forms\Components\TimePicker::make('hora_inicio')
                ->label('Hora inicio')
                ->seconds(false)
                ->required(),
            Forms\Components\TimePicker::make('hora_fin')
                ->label('Hora fin')
                ->seconds(false)
                ->required(),
            Forms\Components\TextInput::make('intervalo_minutos')
                ->label('Intervalo (min)')
                ->numeric()
                ->default(60)
                ->required(),
            Forms\Components\Toggle::make('activo')
                ->label('Activo')
                ->default(true),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('user.name')->label('Profesional')->searchable()->sortable(),
            Tables\Columns\TextColumn::make('rol')->label('Rol')->sortable(),
            Tables\Columns\TextColumn::make('dia_semana')->label('Día')->formatStateUsing(fn ($state) => [0=>'Dom',1=>'Lun',2=>'Mar',3=>'Mié',4=>'Jue',5=>'Vie',6=>'Sáb'][$state] ?? $state)->sortable(),
            Tables\Columns\TextColumn::make('hora_inicio')->label('Inicio')->time('H:i'),
            Tables\Columns\TextColumn::make('hora_fin')->label('Fin')->time('H:i'),
            Tables\Columns\TextColumn::make('intervalo_minutos')->label('Intervalo'),
            Tables\Columns\IconColumn::make('activo')->label('Activo')->boolean(),
            Tables\Columns\TextColumn::make('updated_at')->label('Actualizado')->dateTime('d/m/Y H:i')->toggleable(isToggledHiddenByDefault: true),
        ])
        ->filters([
            Tables\Filters\SelectFilter::make('rol')->label('Rol')->options(['veterinario'=>'Veterinario','peluquero'=>'Peluquero']),
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
        ->defaultSort('user_id');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListHorarioTrabajos::route('/'),
            'create' => Pages\CreateHorarioTrabajo::route('/create'),
            'edit' => Pages\EditHorarioTrabajo::route('/{record}/edit'),
        ];
    }
}