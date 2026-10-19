@extends('layouts.app')

@section('titulo', 'Nuevo reporte')
@section('subtitulo', 'Nuevo reporte')
@section('clase_main', 'pantalla-mapa')
@section('errores_propios', '1')

@push('estilos')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
          integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="">
@endpush

@section('contenido')
    <form class="reporte" method="POST" action="{{ route('incidencias.store') }}" enctype="multipart/form-data" novalidate>
        @csrf

        <div id="mapa" class="mapa" role="application" aria-label="Mapa: tocá el lugar de la incidencia"
             data-lat="{{ $mapa['lat'] }}" data-lng="{{ $mapa['lng'] }}" data-zoom="{{ $mapa['zoom'] }}"
             data-lat-min="{{ $limites['lat_min'] }}" data-lat-max="{{ $limites['lat_max'] }}"
             data-lng-min="{{ $limites['lng_min'] }}" data-lng-max="{{ $limites['lng_max'] }}"></div>

        <p id="aviso-mapa" class="aviso-mapa" aria-live="polite">
            <span class="icono" aria-hidden="true">touch_app</span> Tocá el mapa donde está el problema
        </p>

        <button type="button" id="ubicarme" class="fab fab-mapa" title="Usar mi ubicación">
            <span class="icono" aria-hidden="true">my_location</span>
            <span class="solo-lectores">Usar mi ubicación</span>
        </button>

        <section class="hoja hoja-reporte" aria-labelledby="titulo-reporte">
            <div class="hoja-asa" aria-hidden="true"></div>

            <div class="encabezado">
                <h2 id="titulo-reporte">Reportar incidencia</h2>
                <p class="texto-suave texto-chico">Completá los datos para enviar el reporte al equipo municipal.</p>
            </div>

            @include('partials.errores')

            <div class="campo">
                <span class="etiqueta">Ubicación</span>
                <div id="estado-ubicacion" class="ubicacion {{ old('lat') ? 'ubicacion-lista' : '' }} @error('lat') entrada-invalida @enderror">
                    <span class="icono" aria-hidden="true">location_on</span>
                    <span id="texto-ubicacion">{{ old('lat') ? 'Punto marcado en el mapa' : 'Todavía no marcaste el lugar' }}</span>
                </div>
                <input type="hidden" name="lat" id="lat" value="{{ old('lat') }}">
                <input type="hidden" name="lng" id="lng" value="{{ old('lng') }}">
            </div>

            <fieldset class="campo">
                <legend class="etiqueta">Tipo de problema</legend>
                <div class="chips">
                    @foreach ($categorias as $categoria)
                        <label class="chip">
                            <input type="radio" name="categoria_id" value="{{ $categoria->id }}" class="solo-lectores"
                                   @checked((string) old('categoria_id') === (string) $categoria->id) required>
                            {{ $categoria->nombre }}
                        </label>
                    @endforeach
                </div>
            </fieldset>

            <div class="campo">
                <label for="descripcion">Descripción</label>
                <textarea id="descripcion" name="descripcion" class="entrada @error('descripcion') entrada-invalida @enderror"
                          maxlength="1000" required placeholder="Ej.: el contenedor está lleno y hay bolsas en la vereda">{{ old('descripcion') }}</textarea>
            </div>

            <div class="campo">
                <span class="etiqueta">Foto <span class="texto-suave">(opcional)</span></span>
                <label class="boton boton-secundario boton-bloque" for="foto">
                    <span class="icono" aria-hidden="true">photo_camera</span>
                    <span id="texto-foto">Agregar foto</span>
                </label>
                <input type="file" id="foto" name="foto" accept="image/jpeg,image/png" class="solo-lectores">
                <span class="campo-ayuda">JPG o PNG de hasta 5 MB. Se le quitan los datos de ubicación de la cámara.</span>
                <img id="vista-foto" class="vista-foto" alt="Vista previa de la foto" hidden>
            </div>

            <button type="submit" class="boton boton-primario boton-bloque">
                Enviar reporte <span class="icono" aria-hidden="true">arrow_forward</span>
            </button>
        </section>
    </form>

    <noscript>
        <div class="alerta alerta-error">Para marcar la ubicación en el mapa necesitás tener JavaScript activado.</div>
    </noscript>
@endsection

@push('scripts')
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
            integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <script src="{{ asset('js/reportar.js') }}"></script>
@endpush
