<?php

namespace App\Filament\Veterinario\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Citas;
use App\Models\ConsultaMedica;
use App\Models\Cirugia;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class EstadisticasVeterinarioWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $veterinarioId = Auth::id();
        $hoy = Carbon::today();
        $esteMes = Carbon::now()->startOfMonth();

        // Citas de hoy
        $citasHoy = Citas::where('veterinario_id', $veterinarioId)
            ->whereDate('fecha', $hoy)
            ->count();

        // Consultas completadas este mes
        $consultasEsteMes = ConsultaMedica::where('veterinario_id', $veterinarioId)
            ->where('created_at', '>=', $esteMes)
            ->count();

        // Cirugías programadas pendientes
        $cirugiasPendientes = Cirugia::where('veterinario_id', $veterinarioId)
            ->where('estado', 'programada')
            ->count();

        // Próxima cita
        $proximaCita = Citas::where('veterinario_id', $veterinarioId)
            ->where('fecha', '>=', now())
            ->where('estado', '!=', 'completada')
            ->where('estado', '!=', 'cancelada')
            ->orderBy('fecha')
            ->orderBy('hora')
            ->first();

        $proximaCitaTexto = $proximaCita 
            ? Carbon::parse($proximaCita->fecha . ' ' . $proximaCita->hora)->format('H:i')
            : 'Sin citas';

        return [
            Stat::make('Citas Hoy', $citasHoy)
                ->description('Citas programadas para hoy')
                ->descriptionIcon('heroicon-m-calendar-days')
                ->color('primary'),

            Stat::make('Consultas Este Mes', $consultasEsteMes)
                ->description('Consultas realizadas')
                ->descriptionIcon('heroicon-m-clipboard-document-list')
                ->color('success'),

            Stat::make('Cirugías Pendientes', $cirugiasPendientes)
                ->description('Procedimientos programados')
                ->descriptionIcon('heroicon-m-scissors')
                ->color('warning'),

            Stat::make('Próxima Cita', $proximaCitaTexto)
                ->description($proximaCita ? $proximaCita->cliente->nombre : 'No hay citas pendientes')
                ->descriptionIcon('heroicon-m-clock')
                ->color('info'),
        ];
    }
}