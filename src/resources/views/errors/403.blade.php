@extends('layouts.app')

@section('titulo', 'Acceso denegado')

@section('contenido')
    <div class="pagina-acceso">
        <section class="tarjeta estado-vacio">
            <span class="icono" aria-hidden="true">lock</span>
            <h1>Acceso denegado</h1>
            {{-- __(): el mensaje por defecto de las Policies viene en inglés y se traduce en lang/es.json --}}
            <p class="texto-suave">{{ __($exception->getMessage() ?: 'This action is unauthorized.') }}</p>
            <a class="boton boton-secundario boton-bloque" href="{{ url('/') }}">
                <span class="icono" aria-hidden="true">home</span> Volver al inicio
            </a>
        </section>
    </div>
@endsection
