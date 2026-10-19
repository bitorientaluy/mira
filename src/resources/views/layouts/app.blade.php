<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="theme-color" content="#1f3864">
    <title>@yield('titulo') · {{ config('app.name') }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0..1,0&display=block">
    <link rel="stylesheet" href="{{ asset('css/mira.css') }}">
    @stack('estilos')
</head>
<body>
    <header class="barra">
        <a class="barra-marca" href="{{ url('/') }}">
            <span class="icono" aria-hidden="true">location_on</span>
            <span class="barra-titulo">
                <strong>{{ config('app.name') }}</strong>
                <small>@yield('subtitulo', 'Mapa de Incidencias, Reportes y Atención')</small>
            </span>
        </a>

        @auth
            <div class="barra-usuario">
                <span class="barra-nombre">
                    <span>{{ auth()->user()->nombre }}</span>
                    <small>{{ auth()->user()->rol->etiqueta() }}</small>
                </span>
                <span class="avatar" aria-hidden="true"><span class="icono">person</span></span>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="boton-icono" title="Cerrar sesión">
                        <span class="icono" aria-hidden="true">logout</span>
                        <span class="solo-lectores">Cerrar sesión</span>
                    </button>
                </form>
            </div>
        @endauth
    </header>

    {{-- Las pantallas a pantalla completa (mapa) cambian la clase y muestran los errores donde les conviene --}}
    <main class="@yield('clase_main', 'contenedor')">
        @sectionMissing('errores_propios')
            @include('partials.errores')
        @endif
        @yield('contenido')
    </main>

    @stack('scripts')
</body>
</html>
