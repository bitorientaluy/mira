@extends('layouts.app')

@section('titulo', 'Completá tu cédula')

@section('contenido')
    <div class="pagina-acceso">
        <section class="tarjeta">
            <div class="encabezado">
                <h1>Un paso más</h1>
                <p class="texto-suave">Para reportar incidencias necesitamos tu cédula. Se pide una sola vez.</p>
            </div>

            <form class="formulario" method="POST" action="{{ route('perfil.cedula.store') }}">
                @csrf

                <div class="campo">
                    <label for="cedula">Cédula</label>
                    <input id="cedula" class="entrada @error('cedula') entrada-invalida @enderror" type="text" name="cedula"
                           value="{{ old('cedula') }}" required maxlength="8" inputmode="numeric" autofocus aria-describedby="cedula-ayuda">
                    <span id="cedula-ayuda" class="campo-ayuda">Sin puntos ni guion. Solo se usa para evitar cuentas duplicadas.</span>
                </div>

                <button type="submit" class="boton boton-primario boton-bloque">
                    Guardar y seguir <span class="icono" aria-hidden="true">arrow_forward</span>
                </button>
            </form>
        </section>
    </div>
@endsection
