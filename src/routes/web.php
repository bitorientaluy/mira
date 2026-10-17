<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegistroController;
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
        // Provisorio: lo reemplaza el reporte en el mapa (T-111)
        Route::view('/inicio', 'ciudadano.inicio')->name('ciudadano.inicio');
    });

    Route::middleware('rol:'.implode(',', [Rol::OPERADOR, Rol::JEFE_AREA, Rol::EJECUTOR, Rol::ADMIN]))->group(function () {
        // Provisorio: lo reemplaza el panel de operador (T-116)
        Route::view('/panel', 'panel.inicio')->name('panel.inicio');
    });
});
