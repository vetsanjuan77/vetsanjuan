<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class FacturadorMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Verificar que el usuario esté autenticado
        if (!Auth::check()) {
            return redirect()->guest(route('filament.facturador.auth.login'));
        }

        $user = Auth::user();

        // Verificar que el usuario tenga rol de facturador
        if ($user->role !== 'facturador') {
            Auth::logout();
            return redirect()->route('filament.facturador.auth.login')
                ->withErrors(['email' => 'No tiene permisos para acceder a este panel.']);
        }

        // Verificar que el usuario tenga una sucursal asignada
        if (!$user->sucursal_id || !$user->sucursal) {
            Auth::logout();
            return redirect()->route('filament.facturador.auth.login')
                ->withErrors(['email' => 'No tiene una sucursal asignada.']);
        }

        // Verificar que la sucursal esté activa
        if (!$user->sucursal->activa) {
            Auth::logout();
            return redirect()->route('filament.facturador.auth.login')
                ->withErrors(['email' => 'La sucursal no está activa.']);
        }

        // Verificar que el usuario esté activo
        if ($user->estado !== 'activo') {
            Auth::logout();
            return redirect()->route('filament.facturador.auth.login')
                ->withErrors(['email' => 'Su cuenta no está activa.']);
        }

        return $next($request);
    }
}