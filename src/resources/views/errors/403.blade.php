@extends('layouts.app')

@section('titulo', 'Acceso denegado')

@section('contenido')
    <div class="pagina-acceso">
        <section class="tarjeta estado-vacio">
            <span class="icono" aria-hidden="true">lock</span>
            <h1>Acceso denegado</h1>
            <p class="texto-suave">{{ $exception->getMessage() ?: 'No tenés permiso para acceder a esta página.' }}</p>
            <a class="boton boton-secundario boton-bloque" href="{{ url('/') }}">
                <span class="icono" aria-hidden="true">home</span> Volver al inicio
            </a>
        </section>
    </div>
@endsection
