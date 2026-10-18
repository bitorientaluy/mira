@extends('layouts.app')

@section('titulo', 'Iniciar sesión')

@section('contenido')
    <div class="pagina-acceso">
        <section class="tarjeta">
            <div class="encabezado">
                <h1>Iniciar sesión</h1>
                <p class="texto-suave">Entrá para reportar incidencias y seguir tus reportes.</p>
            </div>

            <form class="formulario" method="POST" action="{{ route('login.store') }}">
                @csrf

                <div class="campo">
                    <label for="email">Email</label>
                    <input id="email" class="entrada @error('email') entrada-invalida @enderror" type="email" name="email"
                           value="{{ old('email') }}" required autofocus autocomplete="username" placeholder="nombre@ejemplo.com">
                </div>

                <div class="campo">
                    <label for="password">Contraseña</label>
                    <input id="password" class="entrada" type="password" name="password" required autocomplete="current-password">
                </div>

                <label class="casilla">
                    <input type="checkbox" name="recordarme" value="1"> Recordarme en este dispositivo
                </label>

                <button type="submit" class="boton boton-primario boton-bloque">
                    Entrar <span class="icono" aria-hidden="true">arrow_forward</span>
                </button>
            </form>

            <p class="texto-chico texto-suave">¿No tenés cuenta? <a href="{{ route('registro') }}">Registrate</a></p>
        </section>
    </div>
@endsection
