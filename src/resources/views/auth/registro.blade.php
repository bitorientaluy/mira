@extends('layouts.app')

@section('titulo', 'Crear cuenta')

@section('contenido')
    <div class="pagina-acceso">
        <section class="tarjeta">
            <div class="encabezado">
                <h1>Crear cuenta</h1>
                <p class="texto-suave">Necesitás una cuenta para reportar incidencias.</p>
            </div>

            <form class="formulario" method="POST" action="{{ route('registro.store') }}">
                @csrf

                <div class="campo">
                    <label for="nombre">Nombre</label>
                    <input id="nombre" class="entrada @error('nombre') entrada-invalida @enderror" type="text" name="nombre"
                           value="{{ old('nombre') }}" required maxlength="100" autocomplete="name">
                </div>

                <div class="campo">
                    <label for="email">Email</label>
                    <input id="email" class="entrada @error('email') entrada-invalida @enderror" type="email" name="email"
                           value="{{ old('email') }}" required maxlength="150" autocomplete="email" placeholder="nombre@ejemplo.com">
                </div>

                <div class="campo">
                    <label for="cedula">Cédula</label>
                    <input id="cedula" class="entrada @error('cedula') entrada-invalida @enderror" type="text" name="cedula"
                           value="{{ old('cedula') }}" required maxlength="8" inputmode="numeric" aria-describedby="cedula-ayuda">
                    <span id="cedula-ayuda" class="campo-ayuda">Sin puntos ni guion. Solo se usa para evitar cuentas duplicadas.</span>
                </div>

                <div class="campo">
                    <label for="telefono">Teléfono <span class="texto-suave">(opcional)</span></label>
                    <input id="telefono" class="entrada @error('telefono') entrada-invalida @enderror" type="tel" name="telefono"
                           value="{{ old('telefono') }}" maxlength="20" autocomplete="tel">
                </div>

                <div class="campo">
                    <label for="password">Contraseña</label>
                    <input id="password" class="entrada @error('password') entrada-invalida @enderror" type="password" name="password"
                           required minlength="8" autocomplete="new-password" aria-describedby="password-ayuda">
                    <span id="password-ayuda" class="campo-ayuda">Mínimo 8 caracteres.</span>
                </div>

                <div class="campo">
                    <label for="password_confirmation">Repetir contraseña</label>
                    <input id="password_confirmation" class="entrada" type="password" name="password_confirmation" required autocomplete="new-password">
                </div>

                <button type="submit" class="boton boton-primario boton-bloque">
                    Crear cuenta <span class="icono" aria-hidden="true">arrow_forward</span>
                </button>
            </form>

            <p class="texto-chico texto-suave">¿Ya tenés cuenta? <a href="{{ route('login') }}">Iniciá sesión</a></p>
        </section>
    </div>
@endsection
