<?php

use App\Exceptions\RegistrarAccesoDenegado;
use App\Http\Middleware\EnsureRol;
use App\Http\Middleware\SoloRedInterna;
use Illuminate\Contracts\Auth\Middleware\AuthenticatesRequests;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'rol' => EnsureRol::class,
            'red.interna' => SoloRedInterna::class,
        ]);

        // Laravel ordena auth antes que el resto: así la red se revisa primero y desde afuera no aparece ni el login (T-202)
        $middleware->prependToPriorityList(AuthenticatesRequests::class, SoloRedInterna::class);

        // Sin sesión → login; con sesión y en una ruta de invitados → su página de inicio
        $middleware->redirectGuestsTo(fn () => route('login'));
        $middleware->redirectUsersTo(fn (Request $request) => route($request->user()->rutaInicio()));
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Los 403 (rol o Policy) quedan en el log; la respuesta no cambia (CP-06)
        $exceptions->render(new RegistrarAccesoDenegado);

        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );
    })->create();
