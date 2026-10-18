@extends('layouts.app')

@section('titulo', 'Panel')
@section('subtitulo', 'Gestión de incidencias')

@section('contenido')
    <div class="encabezado">
        <h1>Panel interno</h1>
        <p class="texto-suave">Desde acá vas a gestionar las incidencias.</p>
    </div>

    {{-- Provisorio: los datos reales llegan con el panel de operador (T-116) --}}
    <div class="grilla grilla-3">
        <section class="tarjeta tarjeta-dato">
            <span class="icono" aria-hidden="true">pending_actions</span>
            <span class="texto-suave texto-chico">Pendientes</span>
            <strong>—</strong>
        </section>
        <section class="tarjeta tarjeta-dato">
            <span class="icono" aria-hidden="true">engineering</span>
            <span class="texto-suave texto-chico">En proceso</span>
            <strong>—</strong>
        </section>
        <section class="tarjeta tarjeta-dato">
            <span class="icono" aria-hidden="true">task_alt</span>
            <span class="texto-suave texto-chico">Resueltas</span>
            <strong>—</strong>
        </section>
    </div>
@endsection
