<?php

namespace App\Exceptions;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

/**
 * Deja en el log cada acceso denegado (403), venga del middleware de rol o de una Policy (CP-06).
 * Laravel no registra los 403 por defecto. Devuelve null para que la respuesta sea la de siempre.
 */
class RegistrarAccesoDenegado
{
    public function __invoke(HttpExceptionInterface $e, Request $request): null
    {
        if ($e->getStatusCode() === 403) {
            Log::warning('Acceso denegado', [
                'usuario_id' => $request->user()?->id,
                'rol' => $request->user()?->rol?->nombre,
                'metodo' => $request->method(),
                'ruta' => $request->path(),
                'ip' => $request->ip(),
            ]);
        }

        return null;
    }
}
