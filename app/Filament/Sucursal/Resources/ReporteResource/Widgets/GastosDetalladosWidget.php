<?php

namespace App\Filament\Sucursal\Resources\ReporteResource\Widgets;

use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use App\Models\Gasto;
use App\Models\CategoriaGasto;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Actions\Action;

class GastosDetalladosWidget extends BaseWidget
{
    protected static ?string $heading = 'Análisis Detallado de Gastos (Este Mes)';
    protected int | string | array $columnSpan = 'full';
    protected static ?int $sort = 2;

    public function table(Table $table): Table
    {
        $sucursalId = Auth::user()->sucursal_id ?? null;
        $fechaInicio = Carbon::now()->startOfMonth();
        
        return $table
            ->query(
                Gasto::query()
                    ->select([
                        'gastos.*',
                        DB::raw('gastos.categoria as categoria_nombre'),
                        DB::raw('"gray" as categoria_color'),
                    ])
                    ->where('gastos.created_at', '>=', $fechaInicio)
                    ->when($sucursalId, fn($query) => $query->where('gastos.sucursal_id', $sucursalId))
                    ->orderBy('gastos.created_at', 'desc')
            )
            ->columns([
                TextColumn::make('fecha')
                    ->label('Fecha')
                    ->date('d/m/Y')
                    ->sortable(),
                
                TextColumn::make('concepto')
                    ->label('Concepto')
                    ->searchable()
                    ->sortable()
                    ->limit(40)
                    ->tooltip(function (TextColumn $column): ?string {
                        $state = $column->getState();
                        if (strlen($state) <= 40) {
                            return null;
                        }
                        return $state;
                    }),
                
                TextColumn::make('categoria_nombre')
                    ->label('Categoría')
                    ->badge()
                    ->color(fn ($record): string => $record->categoria_color ?? 'gray'),
                
                TextColumn::make('monto')
                    ->label('Monto')
                    ->money('COP')
                    ->sortable()
                    ->color('danger'),
                
                TextColumn::make('metodo_pago')
                    ->label('Método de Pago')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'efectivo' => 'success',
                        'tarjeta' => 'info',
                        'transferencia' => 'warning',
                        'cheque' => 'gray',
                        default => 'gray',
                    }),
                
                TextColumn::make('proveedor.nombre')
                    ->label('Proveedor')
                    ->limit(20)
                    ->placeholder('N/A')
                    ->tooltip(function (TextColumn $column): ?string {
                        $state = $column->getState();
                        if (!$state || strlen($state) <= 20) {
                            return null;
                        }
                        return $state;
                    }),
                
                TextColumn::make('numero_factura')
                    ->label('# Factura')
                    ->placeholder('N/A')
                    ->searchable(),
                
                TextColumn::make('estado')
                    ->label('Estado')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pagado' => 'success',
                        'pendiente' => 'warning',
                        'vencido' => 'danger',
                        default => 'gray',
                    }),
                
                TextColumn::make('usuario.name')
                    ->label('Registrado por')
                    ->limit(15),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('categoria')
                    ->label('Categoría')
                    ->options([
                        'servicios' => 'Servicios',
                        'suministros' => 'Suministros',
                        'mantenimiento' => 'Mantenimiento',
                        'marketing' => 'Marketing',
                        'administrativo' => 'Administrativo',
                        'otros' => 'Otros',
                    ]),
                
                Tables\Filters\SelectFilter::make('metodo_pago')
                    ->label('Método de Pago')
                    ->options([
                        'efectivo' => 'Efectivo',
                        'tarjeta' => 'Tarjeta',
                        'transferencia' => 'Transferencia',
                        'cheque' => 'Cheque',
                    ]),
                
                Tables\Filters\SelectFilter::make('estado')
                    ->label('Estado')
                    ->options([
                        'pagado' => 'Pagado',
                        'pendiente' => 'Pendiente',
                        'vencido' => 'Vencido',
                    ]),
                
                Tables\Filters\Filter::make('monto_alto')
                    ->label('Montos Altos (>$100,000)')
                    ->query(fn ($query) => $query->where('monto', '>', 100000)),
                
                Tables\Filters\Filter::make('esta_semana')
                    ->label('Esta Semana')
                    ->query(fn ($query) => $query->where('created_at', '>=', Carbon::now()->startOfWeek())),
            ])
            ->actions([
                Action::make('ver_detalle')
                    ->label('Ver')
                    ->icon('heroicon-o-eye')
                    ->url(fn (Gasto $record): string => route('filament.sucursal.resources.gastos.view', $record))
                    ->openUrlInNewTab(),
                
                Action::make('editar')
                    ->label('Editar')
                    ->icon('heroicon-o-pencil')
                    ->color('warning')
                    ->url(fn (Gasto $record): string => route('filament.sucursal.resources.gastos.edit', $record))
                    ->openUrlInNewTab(),
            ])
            ->bulkActions([
                Tables\Actions\BulkAction::make('marcar_pagado')
                    ->label('Marcar como Pagado')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->action(function ($records) {
                        $records->each(function ($record) {
                            $record->update(['estado' => 'pagado']);
                        });
                    })
                    ->requiresConfirmation(),
                
                Tables\Actions\BulkAction::make('exportar_seleccionados')
                    ->label('Exportar Seleccionados')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('info')
                    ->action(function ($records) {
                        // Implementar exportación de gastos seleccionados
                    }),
            ])
            ->defaultSort('created_at', 'desc')
            ->paginated([10, 25, 50])
            ->poll('60s')
            ->emptyStateHeading('No hay gastos registrados este mes')
            ->emptyStateDescription('Los gastos aparecerán aquí una vez que sean registrados.')
            ->emptyStateIcon('heroicon-o-currency-dollar');
    }
}