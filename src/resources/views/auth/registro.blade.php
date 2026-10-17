@extends('layouts.app')

@section('titulo', 'Crear cuenta')

@section('contenido')
    <h1>Crear cuenta</h1>
    <p>Necesitás una cuenta para reportar incidencias.</p>

    <form method="POST" action="{{ route('registro.store') }}">
        @csrf

        <p>
            <label for="nombre">Nombre</label>
            <input id="nombre" type="text" name="nombre" value="{{ old('nombre') }}" required maxlength="100" autocomplete="name">
        </p>

        <p>
            <label for="email">Email</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required maxlength="150" autocomplete="email">
        </p>

        <p>
            <label for="cedula">Cédula (sin puntos ni guion)</label>
            <input id="cedula" type="text" name="cedula" value="{{ old('cedula') }}" required maxlength="8" inputmode="numeric">
        </p>

        <p>
            <label for="telefono">Teléfono (opcional)</label>
            <input id="telefono" type="tel" name="telefono" value="{{ old('telefono') }}" maxlength="20" autocomplete="tel">
        </p>

        <p>
            <label for="password">Contraseña (mínimo 8 caracteres)</label>
            <input id="password" type="password" name="password" required minlength="8" autocomplete="new-password">
        </p>

        <p>
            <label for="password_confirmation">Repetir contraseña</label>
            <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password">
        </p>

        <button type="submit">Crear cuenta</button>
    </form>

    <p>¿Ya tenés cuenta? <a href="{{ route('login') }}">Iniciá sesión</a></p>
@endsection
