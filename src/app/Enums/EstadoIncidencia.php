<?php

namespace App\Enums;

enum EstadoIncidencia: string
{
    use TieneValores;

    case Pendiente = 'Pendiente';
    case Validada = 'Validada';
    case EnProceso = 'En proceso';
    case Resuelto = 'Resuelto';
    case Descartado = 'Descartado';
}
