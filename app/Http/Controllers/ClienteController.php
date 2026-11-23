<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ClienteController extends Controller
{
    public function update(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'apellido' => 'required|string|max:255',
            'telefono' => 'required|string|max:20',
            'email' => 'required|email|max:255',
            'direccion' => 'nullable|string|max:500',
        ]);

        $cliente = Auth::user()->cliente;

        if (!$cliente) {
            return redirect()->back()->with('error', 'No se encontró el perfil del cliente.');
        }

        // Actualizar los datos del cliente
        $cliente->update([
            'nombre' => $request->nombre,
            'apellido' => $request->apellido,
            'telefono' => $request->telefono,
            'email' => $request->email,
            'direccion' => $request->direccion,
        ]);

        // Actualizar nombre completo y el email en la tabla users si es diferente
        $user = Auth::user();
        $updates = [];
        $fullName = trim($request->nombre . ' ' . $request->apellido);

        if ($user->name !== $fullName) {
            $updates['name'] = $fullName;
        }

        if ($user->email !== $request->email) {
            $updates['email'] = $request->email;
        }

        if (!empty($updates)) {
            $user->update($updates);
        }

        return redirect()->back()->with('success', 'Perfil actualizado correctamente.');
    }
}