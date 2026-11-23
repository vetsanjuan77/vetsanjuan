<?php

namespace App\Services\Scheduling;

use App\Models\HorarioTrabajo;
use App\Models\Citas;
use App\Models\CitaPeluqueria;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class SlotService
{
    /**
     * Devuelve opciones de franjas disponibles (key=>label) para Select de Filament.
     * @param int|null $userId ID del profesional
     * @param string|null $rol 'veterinario'|'peluquero'
     * @param string|\DateTimeInterface|null $fecha
     * @return array<string,string>
     */
    public static function availableSlots(?int $userId, ?string $rol, $fecha): array
    {
        if (!$userId || !$rol || !$fecha) {
            return [];
        }
        try {
            $date = $fecha instanceof \DateTimeInterface ? Carbon::instance(new \DateTime($fecha->format('Y-m-d'))) : Carbon::parse($fecha);
        } catch (\Exception $e) {
            return [];
        }
        // Configuración por defecto
        [$start, $end, $interval] = self::defaultScheduleForDate($date);
        // Override por HorarioTrabajo
        $ht = HorarioTrabajo::query()
            ->where('user_id', $userId)
            ->where('rol', $rol)
            ->where('dia_semana', $date->dayOfWeek)
            ->where('activo', true)
            ->first();
        if ($ht) {
            $start = Carbon::parse($date->toDateString().' '.$ht->hora_inicio);
            $end = Carbon::parse($date->toDateString().' '.$ht->hora_fin);
            $interval = (int) $ht->intervalo_minutos ?: 60;
        }
        if ($end->lessThanOrEqualTo($start)) {
            return [];
        }
        $slots = self::generateSlots($start, $end, $interval);
        $booked = self::bookedTimes($userId, $rol, $date);
        // Excluir reservadas
        $available = array_values(array_diff($slots, $booked));
        // Formato opciones
        $options = [];
        foreach ($available as $h) {
            $options[$h] = $h;
        }
        return $options;
    }

    /** Default schedule rules: Mon-Sat 08:00-19:30, Sun 08:00-13:00, 60-minute */
    public static function defaultScheduleForDate(Carbon $date): array
    {
        $startStr = '08:00';
        $interval = 60;
        $endStr = $date->isSunday() ? '13:00' : '19:30';
        $start = Carbon::parse($date->toDateString().' '.$startStr);
        $end = Carbon::parse($date->toDateString().' '.$endStr);
        return [$start, $end, $interval];
    }

    /** Genera slots inclusivos [start, end) cada $interval minutos */
    public static function generateSlots(Carbon $start, Carbon $end, int $interval): array
    {
        $slots = [];
        $cursor = $start->copy();
        while ($cursor->lt($end)) {
            $slots[] = $cursor->format('H:i');
            $cursor->addMinutes($interval);
        }
        return $slots;
    }

    /** Horas reservadas para el profesional en esa fecha */
    public static function bookedTimes(int $userId, string $rol, Carbon $date): array
    {
        if ($rol === 'veterinario') {
            return Citas::query()
                ->whereDate('fecha', $date)
                ->where('veterinario_id', $userId)
                ->pluck('hora')
                ->map(fn ($h) => substr((string)$h, 0, 5))
                ->all();
        }
        if ($rol === 'peluquero') {
            return CitaPeluqueria::query()
                ->whereDate('fecha', $date)
                ->where('peluquero_id', $userId)
                ->pluck('hora')
                ->map(fn ($h) => substr((string)$h, 0, 5))
                ->all();
        }
        return [];
    }
}