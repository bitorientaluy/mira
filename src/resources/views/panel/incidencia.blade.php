@extends('layouts.app')

@section('titulo', $incidencia->codigo)
@section('subtitulo', 'Detalle de incidencia')
@section('errores_propios', '1')

@push('estilos')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
          integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="">
@endpush

@section('contenido')
    <a class="volver texto-chico" href="{{ route('panel.inicio') }}">
        <span class="icono" aria-hidden="true">arrow_back</span> Volver al listado
    </a>

    <div class="encabezado encabezado-fila">
        <h1>{{ $incidencia->codigo }}</h1>
        @include('partials.insignia-estado', ['estado' => $incidencia->estado])
    </div>

    @if (session('exito'))
        <div class="alerta alerta-exito" role="status">
            <span class="icono" aria-hidden="true">check_circle</span>
            <div>{{ session('exito') }}</div>
        </div>
    @endif

    <div class="detalle">
        <div class="detalle-principal">
            <section class="tarjeta">
                <h2>Reporte</h2>
                <p class="descripcion-incidencia">{{ $incidencia->descripcion }}</p>

                <dl class="datos-reporte">
                    <div><dt>Tipo de problema</dt><dd>{{ $incidencia->categoria->nombre }}</dd></div>
                    <div><dt>Área</dt><dd>{{ $incidencia->area->nombre }}</dd></div>
                    @if ($incidencia->direccion)
                        <div><dt>Referencia</dt><dd>{{ $incidencia->direccion }}</dd></div>
                    @endif
                    <div><dt>Reportó</dt><dd>{{ $incidencia->ciudadano->nombre }}</dd></div>
                    <div><dt>Fecha</dt><dd>{{ $incidencia->creado_en->format('d/m/Y H:i') }}</dd></div>
                    @if ($incidencia->resuelto_en)
                        <div><dt>Resuelta</dt><dd>{{ $incidencia->resuelto_en->format('d/m/Y H:i') }}</dd></div>
                    @endif
                    @if ($incidencia->motivo_descarte)
                        <div><dt>Motivo de descarte</dt><dd>{{ $incidencia->motivo_descarte->value }}</dd></div>
                    @endif
                </dl>
            </section>

            <section class="tarjeta">
                <h2>Ubicación</h2>
                <div id="mapa-detalle" class="mapa-detalle" role="img"
                     aria-label="Mapa con la ubicación de la incidencia"
                     data-lat="{{ $incidencia->lat }}" data-lng="{{ $incidencia->lng }}"></div>
            </section>

            @if ($incidencia->foto_ruta)
                <section class="tarjeta">
                    <h2>Foto</h2>
                    <a href="{{ route('panel.incidencias.foto', $incidencia) }}" target="_blank" rel="noopener">
                        <img class="foto-incidencia" src="{{ route('panel.incidencias.foto', $incidencia) }}"
                             alt="Foto enviada con el reporte {{ $incidencia->codigo }}" loading="lazy">
                    </a>
                </section>
            @endif
        </div>

        <div class="detalle-lateral">
            @if ($puedeCambiar && count($opciones) > 0)
                <section class="tarjeta">
                    <h2>Acciones</h2>
                    @include('partials.errores')

                    <form class="formulario" method="POST" action="{{ route('panel.incidencias.estado', $incidencia) }}">
                        @csrf
                        @method('PATCH')

                        <div class="campo">
                            <label for="comentario">Comentario <span class="texto-suave">(opcional)</span></label>
                            <textarea id="comentario" name="comentario" class="entrada @error('comentario') entrada-invalida @enderror"
                                      maxlength="500" placeholder="Ej.: se coordinó la cuadrilla para mañana">{{ old('comentario') }}</textarea>
                            <span class="campo-ayuda">Queda en el historial junto con el cambio.</span>
                        </div>

                        {{-- Cada transición permitida es un botón: primero las que avanzan, después volver atrás o reabrir --}}
                        @php
                            $actual = $incidencia->estado;
                            $iconos = ['Validada' => 'verified', 'En proceso' => 'engineering', 'Resuelto' => 'task_alt'];
                            usort($opciones, fn ($a, $b) => $actual->avanzaA($b) <=> $actual->avanzaA($a));
                        @endphp
                        <div class="acciones-estado">
                            @foreach ($opciones as $opcion)
                                @php $avanza = $actual->avanzaA($opcion); @endphp
                                <button type="submit" name="estado" value="{{ $opcion->value }}"
                                        class="boton boton-bloque {{ $avanza ? 'boton-primario' : 'boton-secundario' }}">
                                    <span class="icono" aria-hidden="true">{{ $avanza ? ($iconos[$opcion->value] ?? 'arrow_forward') : ($actual === \App\Enums\EstadoIncidencia::Resuelto ? 'replay' : 'undo') }}</span>
                                    {{ $actual->accionHacia($opcion) }}
                                </button>
                            @endforeach
                        </div>
                    </form>
                </section>
            @endif

            <section class="tarjeta">
                <h2>Historial</h2>
                <ol class="historial">
                    @foreach ($incidencia->historial as $paso)
                        <li>
                            <span class="historial-estado">
                                @if ($paso->estado_anterior)
                                    {{ $paso->estado_anterior->value }} <span class="icono" aria-label="a">arrow_forward</span>
                                @endif
                                <strong>{{ $paso->estado_nuevo->value }}</strong>
                            </span>
                            <span class="texto-suave texto-chico">
                                {{ $paso->creado_en->format('d/m/Y H:i') }} · {{ $paso->usuario->nombre }}
                            </span>
                            @if ($paso->comentario)
                                <span class="historial-comentario texto-chico">{{ $paso->comentario }}</span>
                            @endif
                        </li>
                    @endforeach
                </ol>
            </section>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
            integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <script src="{{ asset('js/panel-incidencia.js') }}"></script>
@endpush
