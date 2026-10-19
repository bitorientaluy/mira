@extends('layouts.app')

@section('titulo', 'Reporte enviado')
@section('subtitulo', 'Reporte enviado')

@section('contenido')
    <div class="pagina-acceso">
        <section class="tarjeta estado-vacio">
            <span class="icono icono-exito" aria-hidden="true">task_alt</span>
            <h1>¡Reporte enviado!</h1>
            <p class="texto-suave">Gracias por avisar. Guardá este código para seguir el reporte.</p>

            <p class="codigo-reporte">{{ $incidencia->codigo }}</p>

            <dl class="datos-reporte">
                <div><dt>Estado</dt><dd><span class="insignia insignia-punto">{{ $incidencia->estado->value }}</span></dd></div>
                <div><dt>Tipo de problema</dt><dd>{{ $incidencia->categoria->nombre }}</dd></div>
                <div><dt>Fecha</dt><dd>{{ $incidencia->creado_en->format('d/m/Y H:i') }}</dd></div>
            </dl>

            <div class="acciones">
                <a class="boton boton-primario" href="{{ route('incidencias.create') }}">
                    <span class="icono" aria-hidden="true">add_location_alt</span> Reportar otra
                </a>
                <a class="boton boton-secundario" href="{{ route('ciudadano.inicio') }}">Volver al inicio</a>
            </div>
        </section>
    </div>
@endsection
