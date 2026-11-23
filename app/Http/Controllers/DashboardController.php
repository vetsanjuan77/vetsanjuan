<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Mascotas;
use App\Models\User;
use App\Models\Peluquero;
use App\Models\Especie;
use App\Models\Sexo;
use App\Models\Vacuna;
use App\Models\CitaPeluqueria;
use App\Services\Scheduling\SlotService;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $cliente = $user->cliente;
        $mascotas = $cliente->mascotas()->with(['especie', 'sexo', 'vacunas'])->get();
        $citas = $cliente->citas()
            ->where('estado', '!=', 'cancelada')
            ->with(['mascota', 'veterinario', 'servicio'])
            ->get();
        $citasPeluqueria = CitaPeluqueria::where('cliente_id', $cliente->id)
            ->where('estado', '!=', 'cancelada')
            ->with(['mascota', 'peluquero', 'servicio'])
            ->get();
        $veterinarios = User::where('role', 'veterinario')->where('estado', 'activo')->get();
        $especies = Especie::all();
        $sexos = Sexo::all();
        $peluqueros = Peluquero::where('estado', 'activo')->get();
        $vacunas = Vacuna::all();

        return view('dashboard', compact(
            'user',
            'cliente',
            'mascotas',
            'citas',
            'citasPeluqueria',
            'veterinarios',
            'especies',
            'sexos',
            'peluqueros',
            'vacunas'
        ));
    }

    public function peluqueria()
    {
        $user = Auth::user();
        $cliente = $user->cliente;
        $mascotas = $cliente->mascotas()->with(['especie', 'sexo'])->get();
        $peluqueros = Peluquero::where('estado', 'activo')->get();

        return view('peluqueria', compact(
            'user',
            'cliente',
            'mascotas',
            'peluqueros'
        ));
    }

    public function veterinaria()
    {
        $user = Auth::user();
        $cliente = $user->cliente;
        $mascotas = $cliente->mascotas()->with(['especie', 'sexo'])->get();
        $veterinarios = User::where('role', 'veterinario')->where('estado', 'activo')->get();

        return view('veterinaria', compact(
            'user',
            'cliente',
            'mascotas',
            'veterinarios'
        ));
    }

    public function vacunacion()
    {
        $user = Auth::user();
        $cliente = $user->cliente;
        $mascotas = $cliente->mascotas()->with(['especie', 'sexo'])->get();
        $veterinarios = User::where('role', 'veterinario')->where('estado', 'activo')->get();

        return view('vacunacion', compact(
            'user',
            'cliente',
            'mascotas',
            'veterinarios'
        ));
    }

    public function getVacunasByEspecie($especieId)
    {
        $vacunas = Vacuna::where('especie_id', $especieId)->get();
        return response()->json($vacunas);
    }

    public function getMascotaEspecie($mascotaId)
    {
        $user = Auth::user();
        $cliente = $user->cliente;
        
        // Verificar que la mascota pertenece al cliente autenticado
        $mascota = $cliente->mascotas()->with('especie')->find($mascotaId);
        
        if (!$mascota) {
            return response()->json(['error' => 'Mascota no encontrada'], 404);
        }
        
        return response()->json([
            'especie_id' => $mascota->especie->id,
            'especie_nombre' => $mascota->especie->nombre
        ]);
    }

    // Nuevo endpoint: franjas disponibles según profesional y fecha
    public function getAvailableSlots(Request $request)
    {
        $rol = $request->query('rol');
        $userId = (int) $request->query('user_id');
        $fecha = $request->query('fecha');

        if (!$rol || !$userId || !$fecha) {
            return response()->json([]);
        }

        $options = SlotService::availableSlots($userId, $rol, $fecha);
        // Devolver solo la lista de horas (e.g., ["08:00","09:00"]) para simplificar el front
        return response()->json(array_values($options));
    }
}
