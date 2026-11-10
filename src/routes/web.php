<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegistroController;
use App\Http\Controllers\IncidenciaController;
use App\Http\Controllers\Panel\IncidenciaController as PanelIncidenciaController;
use App\Models\Incidencia;
use App\Models\Rol;
use Illuminate\Support\Facades\Route;

// Provisorio hasta el mapa público (T-115)
Route::get('/', function () {
    return auth()->check()
        ? redirect()->route(auth()->user()->rutaInicio())
        : redirect()->route('login');
});

Route::middleware('guest')->group(function () {
    Route::get('/registro', [RegistroController::class, 'create'])->name('registro');
    Route::post('/registro', [RegistroController::class, 'store'])->name('registro.store');
    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store'])->name('login.store');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');

    Route::middleware('rol:'.Rol::CIUDADANO)->group(function () {
        Route::view('/inicio', 'ciudadano.inicio')->name('ciudadano.inicio');

        Route::get('/reportar', [IncidenciaController::class, 'create'])->name('incidencias.create');
        Route::post('/incidencias', [IncidenciaController::class, 'store'])->name('incidencias.store');
        Route::get('/incidencias/{incidencia}/confirmacion', [IncidenciaController::class, 'confirmacion'])
            ->can('view', 'incidencia')
            ->name('incidencias.confirmacion');
    });
});

// Panel interno: primero la red (desde afuera da 404 sin pedir login, T-202), después la sesión.
// Los permisos salen de IncidenciaPolicy (T-201).
Route::middleware(['red.interna', 'auth', 'can:viewAny,'.Incidencia::class])->group(function () {
    Route::get('/panel', [PanelIncidenciaController::class, 'index'])->name('panel.inicio');
    Route::get('/panel/incidencias/{incidencia}', [PanelIncidenciaController::class, 'show'])
        ->whereNumber('incidencia')->can('view', 'incidencia')->name('panel.incidencias.show');
    Route::get('/panel/incidencias/{incidencia}/foto', [PanelIncidenciaController::class, 'foto'])
        ->can('view', 'incidencia')->name('panel.incidencias.foto');
    Route::patch('/panel/incidencias/{incidencia}/estado', [PanelIncidenciaController::class, 'cambiarEstado'])
        ->can('cambiarEstado', 'incidencia')->name('panel.incidencias.estado');
});
