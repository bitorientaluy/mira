@extends('layouts.app')

@section('titulo', 'Panel')
@section('subtitulo', 'Gestión de incidencias')

@section('contenido')
    <div class="encabezado">
        <h1>Incidencias</h1>
        <p class="texto-suave">Revisá los reportes y actualizá su estado.</p>
    </div>

    <div class="grilla grilla-3 grilla-datos">
        <section class="tarjeta tarjeta-dato">
            <span class="icono" aria-hidden="true">pending_actions</span>
            <span class="texto-suave texto-chico">Pendientes</span>
            <strong>{{ $conteos['Pendiente'] }}</strong>
        </section>
        <section class="tarjeta tarjeta-dato">
            <span class="icono" aria-hidden="true">engineering</span>
            <span class="texto-suave texto-chico">En proceso</span>
            <strong>{{ $conteos['En proceso'] }}</strong>
        </section>
        <section class="tarjeta tarjeta-dato">
            <span class="icono" aria-hidden="true">task_alt</span>
            <span class="texto-suave texto-chico">Resueltas</span>
            <strong>{{ $conteos['Resuelto'] }}</strong>
        </section>
    </div>

    <nav class="chips" aria-label="Filtrar por estado">
        <a class="chip {{ $filtro === null ? 'chip-activo' : '' }}" href="{{ route('panel.inicio') }}"
           @if ($filtro === null) aria-current="page" @endif>
            Todas <span class="chip-numero">{{ array_sum($conteos) }}</span>
        </a>
        @foreach ($conteos as $estado => $total)
            <a class="chip {{ $filtro?->value === $estado ? 'chip-activo' : '' }}"
               href="{{ route('panel.inicio', ['estado' => $estado]) }}"
               @if ($filtro?->value === $estado) aria-current="page" @endif>
                {{ $estado }} <span class="chip-numero">{{ $total }}</span>
            </a>
        @endforeach
    </nav>

    @if ($incidencias->isEmpty())
        <section class="tarjeta estado-vacio">
            <span class="icono" aria-hidden="true">inbox</span>
            <p class="texto-suave">
                {{ $filtro ? "No hay incidencias en estado «{$filtro->value}»." : 'Todavía no hay incidencias reportadas.' }}
            </p>
        </section>
    @else
        <ul class="lista-incidencias">
            @foreach ($incidencias as $incidencia)
                <li>
                    <a class="fila-incidencia" href="{{ route('panel.incidencias.show', $incidencia->id) }}">
                        <span class="fila-cabeza">
                            <strong>{{ $incidencia->codigo }}</strong>
                            @include('partials.insignia-estado', ['estado' => $incidencia->estado])
                        </span>
                        <span class="fila-descripcion">{{ \Illuminate\Support\Str::limit($incidencia->descripcion, 120) }}</span>
                        <span class="fila-datos texto-suave texto-chico">
                            <span>{{ $incidencia->categoria->nombre }}</span>
                            <span>{{ $incidencia->creado_en->format('d/m/Y H:i') }}</span>
                            @if ($incidencia->foto_ruta)
                                <span class="icono" title="Tiene foto" aria-label="Tiene foto">photo_camera</span>
                            @endif
                        </span>
                    </a>
                </li>
            @endforeach
        </ul>

        {{ $incidencias->links('partials.paginacion') }}
    @endif
@endsection
