<?php

namespace App\Exceptions;

use App\Enums\EstadoIncidencia;
use DomainException;

/**
 * Cambio de estado que el flujo de la incidencia no permite (ej. Pendiente → Resuelto).
 * El mensaje se puede mostrar tal cual al usuario.
 */
class TransicionInvalidaException extends DomainException
{
    public static function entre(EstadoIncidencia $actual, EstadoIncidencia $nuevo): self
    {
        return new self("Una incidencia en estado «{$actual->value}» no puede pasar a «{$nuevo->value}».");
    }

    public static function faltaMotivo(): self
    {
        return new self('Para descartar una incidencia hay que indicar el motivo.');
    }

    public static function motivoSobrante(): self
    {
        return new self('El motivo solo corresponde cuando la incidencia se descarta.');
    }
}
