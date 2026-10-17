@extends('layouts.app')

@section('titulo', 'Acceso denegado')

@section('contenido')
    <h1>Acceso denegado</h1>
    <p>{{ $exception->getMessage() ?: 'No tenés permiso para acceder a esta página.' }}</p>
    <p><a href="{{ url('/') }}">Volver al inicio</a></p>
@endsection
