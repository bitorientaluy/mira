<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('titulo') · {{ config('app.name') }}</title>
</head>
<body>
    <header>
        <strong>{{ config('app.name') }}</strong>
        @auth
            <span>{{ auth()->user()->nombre }} ({{ auth()->user()->rol->nombre }})</span>
            <form method="POST" action="{{ route('logout') }}" style="display:inline">
                @csrf
                <button type="submit">Cerrar sesión</button>
            </form>
        @endauth
    </header>

    <main>
        @include('partials.errores')
        @yield('contenido')
    </main>
</body>
</html>
