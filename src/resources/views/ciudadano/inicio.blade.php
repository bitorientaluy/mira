@extends('layouts.app')

@section('titulo', 'Inicio')

@section('contenido')
    <h1>Hola, {{ auth()->user()->nombre }}</h1>
    <p>Desde acá vas a poder reportar incidencias y seguir tus reportes.</p>
@endsection
