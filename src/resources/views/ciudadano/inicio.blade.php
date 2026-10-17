@extends('layouts.app')

@section('titulo', 'Inicio')
@section('subtitulo', 'Tus reportes')

@section('contenido')
    <div class="encabezado">
        <h1>Hola, {{ auth()->user()->nombre }}</h1>
        <p class="texto-suave">Desde acá vas a poder reportar incidencias y seguir tus reportes.</p>
    </div>

    {{-- Provisorio: las acciones se habilitan con T-111 (reporte) y T-214 (mis reportes) --}}
    <section class="tarjeta">
        <h2>¿Viste un problema de limpieza en la calle?</h2>
        <p class="texto-suave">Marcá el lugar en el mapa, elegí la categoría y, si querés, sumá una foto.</p>
        <div class="acciones">
            <button type="button" class="boton boton-primario" disabled>
                <span class="icono" aria-hidden="true">add_location_alt</span> Reportar incidencia
            </button>
            <button type="button" class="boton boton-secundario" disabled>
                <span class="icono" aria-hidden="true">list_alt</span> Mis reportes
            </button>
        </div>
        <span class="insignia insignia-punto">Disponible próximamente</span>
    </section>
@endsection
