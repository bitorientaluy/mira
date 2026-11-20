<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * El ciudadano que entró con Google y todavía no cargó la cédula va a completarla antes de seguir (T-411).
 * Se guarda la página que pidió para volver ahí después.
 */
class ExigirCedula
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user() !== null && $request->user()->cedula === null) {
            return redirect()->guest(route('perfil.cedula'));
        }

        return $next($request);
    }
}
