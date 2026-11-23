<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CambiarPasswordPrimerLogin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();
        
        // Solo aplicar a veterinarios
        if ($user && $user->role === 'veterinario') {
            // Verificar si es el primer login (password_changed_at es null)
            if (is_null($user->password_changed_at)) {
                // Si no está en la página de cambio de contraseña, redirigir
                if (!$request->routeIs('filament.veterinario.auth.change-password')) {
                    return redirect()->route('filament.veterinario.auth.change-password')
                        ->with('message', 'Debe cambiar su contraseña temporal antes de continuar.');
                }
            }
        }
        
        return $next($request);
    }
}