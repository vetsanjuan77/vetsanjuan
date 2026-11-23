<?php

namespace App\Http\Controllers;

use App\Models\Mascotas;
use App\Models\Cliente;
use App\Models\Tamano;
use App\Models\TipoPelo;
use App\Models\Raza;
use App\Models\Especie;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class MascotaController extends Controller
{
    public function getTamanosPorEspecie($especieId)
    {
        // Lógica para filtrar tamaños según la especie
        $especie = Especie::find($especieId);
        
        if (!$especie) {
            return response()->json([]);
        }

        $tamanos = [];
        
        // Si es gato, mostrar: pequeño, mediano, grande
        if (strtolower($especie->nombre_especie) === 'gato') {
            $tamanos = Tamano::whereIn('nombre_tamano', ['Pequeño', 'Mediano', 'Grande'])->get();
        }
        // Si es perro, alinear con el seeder: pequeño, mediano, grande
        elseif (strtolower($especie->nombre_especie) === 'perro') {
            $tamanos = Tamano::whereIn('nombre_tamano', ['Pequeño', 'Mediano', 'Grande'])->get();
        }
        else {
            // Para otras especies, mostrar todos los tamaños
            $tamanos = Tamano::all();
        }

        return response()->json($tamanos);
    }

    public function getRazasPorEspecie($especieId)
    {
        $razas = Raza::where('especie_id', $especieId)->get();
        return response()->json($razas);
    }

    public function getTiposPelo()
    {
        // Filtrar solo los tipos de pelo más comunes: corto, largo, sin pelo
        $tiposPelo = TipoPelo::whereIn('nombre_tipo_pelo', ['Corto', 'Largo', 'Sin pelo'])->get();
        return response()->json($tiposPelo);
    }
    public function store(Request $request)
    {
        $request->validate([
            'nombre_mascota' => 'required|string|max:255',
            'fecha_nacimiento' => 'required|date',
            'especie_id' => 'required|exists:especie,id',
            'sexo_id' => 'required|exists:sexo,id',
            'edad' => 'required|numeric',
            'peso' => 'nullable|numeric',
            'vacunas' => 'nullable|array',
            'vacunas.*' => 'exists:vacunas,id',
            'alergias' => 'nullable|string',
            'comentarios' => 'nullable|string',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'tamano_id' => 'nullable|exists:tamanos,id',
            'tipo_pelo_id' => 'nullable|exists:tipos_pelo,id',
            'raza_id' => 'nullable|exists:razas,id',
        ]);

        // Obtener el cliente asociado al usuario autenticado
        $cliente = Cliente::where('user_id', Auth::id())->first();

        if (!$cliente) {
            return redirect()->back()->with('error', 'No se encontró un perfil de cliente asociado a este usuario.');
        }

        // Manejar la subida de la foto
        $fotoPath = null;
        if ($request->hasFile('foto')) {
            $fotoPath = $request->file('foto')->store('mascotas', 'public');
        }

        $mascota = new Mascotas();
        $mascota->nombre_mascota = $request->nombre_mascota;
        $mascota->fecha_nacimiento = $request->fecha_nacimiento;
        $mascota->cliente_id = $cliente->id;
        $mascota->especie_id = $request->especie_id;
        $mascota->sexo_id = $request->sexo_id;
        $mascota->edad = $request->edad;
        $mascota->peso = $request->peso;
        $mascota->alergias = $request->alergias;
        $mascota->comentarios = $request->comentarios;
        $mascota->foto = $fotoPath;
        $mascota->tamano_id = $request->tamano_id;
        $mascota->tipo_pelo_id = $request->tipo_pelo_id;
        $mascota->raza_id = $request->raza_id;
        
        $mascota->save();

        // Asociar las vacunas seleccionadas
        if ($request->has('vacunas') && is_array($request->vacunas)) {
            $mascota->vacunas()->attach($request->vacunas);
        }

        return redirect()->back()->with('success', 'Mascota registrada exitosamente');
    }

    public function update(Request $request, Mascotas $mascota)
    {
        // Verificar que la mascota pertenece al cliente autenticado
        $cliente = Cliente::where('user_id', Auth::id())->first();
        
        if (!$cliente || $mascota->cliente_id !== $cliente->id) {
            return redirect()->back()->with('error', 'No tienes permisos para editar esta mascota.');
        }

        $request->validate([
            'nombre_mascota' => 'required|string|max:255',
            'fecha_nacimiento' => 'required|date',
            'especie_id' => 'required|exists:especie,id',
            'sexo_id' => 'required|exists:sexo,id',
            'edad' => 'required|numeric',
            'peso' => 'nullable|numeric',
            'vacunas' => 'nullable|array',
            'vacunas.*' => 'exists:vacunas,id',
            'alergias' => 'nullable|string',
            'comentarios' => 'nullable|string',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'tamano_id' => 'nullable|exists:tamanos,id',
            'tipo_pelo_id' => 'nullable|exists:tipos_pelo,id',
            'raza_id' => 'nullable|exists:razas,id',
        ]);

        // Manejar la subida de la nueva foto
        $fotoPath = $mascota->foto; // Mantener la foto actual por defecto
        if ($request->hasFile('foto')) {
            // Eliminar la foto anterior si existe
            if ($mascota->foto && Storage::disk('public')->exists($mascota->foto)) {
                Storage::disk('public')->delete($mascota->foto);
            }
            $fotoPath = $request->file('foto')->store('mascotas', 'public');
        }

        $mascota->update([
            'nombre_mascota' => $request->nombre_mascota,
            'fecha_nacimiento' => $request->fecha_nacimiento,
            'especie_id' => $request->especie_id,
            'sexo_id' => $request->sexo_id,
            'edad' => $request->edad,
            'peso' => $request->peso,
            'alergias' => $request->alergias,
            'comentarios' => $request->comentarios,
            'foto' => $fotoPath,
            'tamano_id' => $request->tamano_id,
            'tipo_pelo_id' => $request->tipo_pelo_id,
            'raza_id' => $request->raza_id,
        ]);

        // Sincronizar las vacunas seleccionadas
        if ($request->has('vacunas') && is_array($request->vacunas)) {
            $mascota->vacunas()->sync($request->vacunas);
        } else {
            $mascota->vacunas()->detach();
        }

        return redirect()->back()->with('success', 'Mascota actualizada exitosamente');
    }

    public function destroy(Mascotas $mascota)
    {
        // Verificar que la mascota pertenece al cliente autenticado
        $cliente = Cliente::where('user_id', Auth::id())->first();
        
        if (!$cliente || $mascota->cliente_id !== $cliente->id) {
            return redirect()->back()->with('error', 'No tienes permisos para eliminar esta mascota.');
        }

        // Eliminar las relaciones con vacunas
        $mascota->vacunas()->detach();
        
        // Eliminar la mascota
        $mascota->delete();

        return redirect()->back()->with('success', 'Mascota eliminada exitosamente');
    }
}