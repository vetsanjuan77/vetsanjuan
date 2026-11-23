<?php

namespace App\Filament\Veterinario\Widgets;

use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use App\Models\Citas;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;

class CitasHoyWidget extends BaseWidget
{
    protected static ?string $heading = 'Mis Citas de Hoy';

    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Citas::query()
                    ->where('veterinario_id', Auth::id())
                    ->whereDate('fecha', Carbon::today())
                    ->with(['cliente', 'mascota'])
                    ->orderBy('hora')
            )
            ->columns([
                TextColumn::make('hora')
                    ->label('Hora')
                    ->time('H:i')
                    ->sortable(),

                TextColumn::make('cliente.nombre')
                    ->label('Cliente')
                    ->formatStateUsing(fn ($record) => $record->cliente->nombre . ' ' . $record->cliente->apellido),

                TextColumn::make('mascota.nombre_mascota')
                    ->label('Mascota')
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
                        'peluqueria' => 'primary',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'consulta' => 'Consulta',
                        'vacunacion' => 'Vacunación',
                        'cirugia' => 'Cirugía',
                        'emergencia' => 'Emergencia',
                        'control' => 'Control',
                        'peluqueria' => 'Peluquería',
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
            ])
            ->actions([
                Tables\Actions\Action::make('iniciar_consulta')
                    ->label('Iniciar')
                    ->icon('heroicon-m-play')
                    ->color('success')
                    ->visible(fn ($record) => in_array($record->estado, ['programada', 'confirmada']))
                    ->action(function ($record) {
                        $record->update(['estado' => 'en_proceso']);
                        $this->dispatch('refresh');
                    }),

                Tables\Actions\Action::make('completar')
                    ->label('Completar')
                    ->icon('heroicon-m-check')
                    ->color('primary')
                    ->visible(fn ($record) => $record->estado === 'en_proceso')
                    ->action(function ($record) {
                        $record->update(['estado' => 'completada']);
                        $this->dispatch('refresh');
                    }),
            ])
            ->emptyStateHeading('No tienes citas programadas para hoy')
            ->emptyStateDescription('Todas las citas de hoy han sido completadas o no hay citas programadas.')
            ->emptyStateIcon('heroicon-o-calendar-days');
    }
}