<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class VeterinarioMiddleware
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
            return redirect()->route('filament.veterinario.auth.login');
        }

        // Verificar que el usuario tenga rol de veterinario
        if (!Auth::user()->isVeterinario()) {
            Auth::logout();
            return redirect()->route('filament.veterinario.auth.login')
                ->withErrors(['email' => 'No tienes permisos para acceder a este panel.']);
        }

        // Verificar que el veterinario esté activo
        if (Auth::user()->estado !== 'activo') {
            Auth::logout();
            return redirect()->route('filament.veterinario.auth.login')
                ->withErrors(['email' => 'Tu cuenta está inactiva. Contacta al administrador.']);
        }

        return $next($request);
    }
}