<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\IpUtils;
use Symfony\Component\HttpFoundation\Response;

/**
 * Deja pasar solo los pedidos que vienen de las redes internas (config mira.redes_panel), T-202.
 *
 * Es la segunda capa: la primera es el Nginx de la DMZ, que no reenvía /panel. Si el proxy
 * se configurara mal, lo que llega desde él (192.168.20.10) igual queda afuera, porque no está en la lista.
 * Laravel no confía en X-Forwarded-For mientras no se configure TrustProxies, así que la IP no se puede falsificar
 * con esa cabecera. Responde 404 para no revelar que el panel existe.
 */
class SoloRedInterna
{
    public function handle(Request $request, Closure $next): Response
    {
        $ip = (string) $request->ip();

        if (! IpUtils::checkIp($ip, config('mira.redes_panel'))) {
            Log::warning('Acceso al panel desde fuera de la red interna', [
                'ip' => $ip,
                'ruta' => $request->path(),
                'usuario_id' => $request->user()?->id,
            ]);

            abort(404);
        }

        return $next($request);
    }
}
