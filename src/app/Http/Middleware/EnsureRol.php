<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Deja pasar solo a los usuarios con alguno de los roles indicados.
 * Uso en rutas: ->middleware('rol:operador,admin')
 */
class EnsureRol
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        abort_unless(
            in_array($request->user()?->rol?->nombre, $roles, true),
            403,
            'No tenés permiso para acceder a esta página.'
        );

        return $next($request);
    }
}
