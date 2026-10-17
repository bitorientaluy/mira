@extends('layouts.app')

@section('titulo', 'Iniciar sesión')

@section('contenido')
    <h1>Iniciar sesión</h1>

    <form method="POST" action="{{ route('login.store') }}">
        @csrf

        <p>
            <label for="email">Email</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username">
        </p>

        <p>
            <label for="password">Contraseña</label>
            <input id="password" type="password" name="password" required autocomplete="current-password">
        </p>

        <p>
            <label>
                <input type="checkbox" name="recordarme" value="1"> Recordarme
            </label>
        </p>

        <button type="submit">Entrar</button>
    </form>

    <p>¿No tenés cuenta? <a href="{{ route('registro') }}">Registrate</a></p>
@endsection
