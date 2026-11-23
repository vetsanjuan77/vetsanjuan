<?php

namespace App\Http\Controllers;

use App\Models\Citas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CitasController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'fecha' => 'required|date',
            'hora' => 'required',
            'mascota_id' => 'required|exists:mascotas,id',
            // Validar contra la tabla correcta de usuarios (rol veterinario)
            'veterinario_id' => 'required|exists:users,id',
            'tipo_servicio_id' => 'required|exists:servicios,id',
        ]);
    
        $cliente = Auth::user()->cliente;
    
        Citas::create([
            'fecha' => $request->fecha,
            'hora' => $request->hora,
            'cliente_id' => $cliente->id,
            'mascota_id' => $request->mascota_id,
            'veterinario_id' => $request->veterinario_id,
            'tipo_servicio_id' => $request->tipo_servicio_id,
            // tarifa de agendamiento
            'reserva_consumibles' => 10000,
        ]);
    
        return redirect()->back()->with('success', 'Cita registrada correctamente.');
    }

    // Cancelar cita por el cliente
    public function cancelar(Request $request, Citas $cita)
    {
        $cliente = Auth::user()->cliente;
        if (!$cliente || $cita->cliente_id !== $cliente->id) {
            abort(403, 'No autorizado para cancelar esta cita.');
        }

        // Solo actualizar si no está ya cancelada
        if (!$cita->isCancelada()) {
            $cita->estado = 'cancelada';
            $cita->save();
        }

        return redirect()->back()->with('success', 'Cita cancelada. El costo de la reserva no será devuelto.');
    }
}
