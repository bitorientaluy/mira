@extends('layouts.app')

@section('titulo', 'Inicio')
@section('subtitulo', 'Tus reportes')

@section('contenido')
    <div class="encabezado">
        <h1>Hola, {{ auth()->user()->nombre }}</h1>
        <p class="texto-suave">Desde acá vas a poder reportar incidencias y seguir tus reportes.</p>
    </div>

    <section class="tarjeta">
        <h2>¿Viste un problema de limpieza en la calle?</h2>
        <p class="texto-suave">Marcá el lugar en el mapa, elegí la categoría y, si querés, sumá una foto.</p>
        <div class="acciones">
            <a class="boton boton-primario" href="{{ route('incidencias.create') }}">
                <span class="icono" aria-hidden="true">add_location_alt</span> Reportar incidencia
            </a>
            {{-- Se habilita con T-214 --}}
            <button type="button" class="boton boton-secundario" disabled>
                <span class="icono" aria-hidden="true">list_alt</span> Mis reportes
            </button>
        </div>
        <span class="insignia insignia-punto">"Mis reportes" disponible próximamente</span>
    </section>
@endsection
