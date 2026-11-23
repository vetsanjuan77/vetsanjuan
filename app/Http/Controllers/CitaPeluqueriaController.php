<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\CitaPeluqueria;

class CitaPeluqueriaController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'fecha' => 'required|date',
            'hora' => 'required',
            'mascota_id' => 'required|exists:mascotas,id',
            'peluquero_id' => 'required|exists:users,id',
            'tipo_servicio_id' => 'required|exists:servicios,id',
            // nuevos campos resumen
            'servicio_detalle' => 'required|string',
            'nombre_cliente' => 'required|string',
            'correo_cliente' => 'required|email',
            'tiene_nudos' => 'nullable|string',
            'observaciones' => 'nullable|string',
            'telefono_cliente' => 'nullable|string',
        ]);

        $cliente = Auth::user()->cliente;

        // Normalizar hora a formato MySQL (HH:MM:SS)
        $horaSql = null;
        try {
            $horaSql = \Carbon\Carbon::createFromFormat('h:i A', $request->hora)->format('H:i:s');
        } catch (\Exception $e) {
            try {
                $horaSql = \Carbon\Carbon::createFromFormat('H:i', $request->hora)->format('H:i:s');
            } catch (\Exception $e2) {
                return redirect()->back()->with('error', 'Formato de hora inválido. Usa valores como 12:00 PM.');
            }
        }

        // Parsear servicio_detalle: "Servicio|Detalle"
        [$tipoServicio, $detalle] = array_pad(explode('|', $request->servicio_detalle, 2), 2, null);

        CitaPeluqueria::create([
            'fecha' => $request->fecha,
            'hora' => $horaSql,
            'cliente_id' => $cliente->id,
            'mascota_id' => $request->mascota_id,
            'peluquero_id' => $request->peluquero_id,
            'servicio_id' => $request->tipo_servicio_id,
            'estado' => 'programada',
            'observaciones' => $request->observaciones,
            'motivo' => $detalle,
            'tipo_servicio' => $tipoServicio,
            'precio' => null,
            // resumen cliente
            'nombre_cliente' => $request->nombre_cliente,
            'correo_cliente' => $request->correo_cliente,
            'telefono_cliente' => $request->telefono_cliente,
            'tiene_nudos' => strtolower($request->tiene_nudos ?? '') === 'si',
            // tarifa de agendamiento
            'reserva_consumibles' => 10000,
        ]);

        return redirect()->back()->with('success', 'Cita de peluquería registrada correctamente.');
    }

    // Cancelar cita de peluquería por el cliente
    public function cancelar(Request $request, CitaPeluqueria $cita)
    {
        $cliente = Auth::user()->cliente;
        if (!$cliente || $cita->cliente_id !== $cliente->id) {
            abort(403, 'No autorizado para cancelar esta cita.');
        }

        if ($cita->estado !== 'cancelada') {
            $cita->estado = 'cancelada';
            $cita->save();
        }

        return redirect()->back()->with('success', 'Cita de peluquería cancelada. El costo de la reserva no será devuelto.');
    }
}