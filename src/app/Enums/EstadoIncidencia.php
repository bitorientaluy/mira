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

    /**
     * Estados a los que se puede pasar desde este. Resuelto → Pendiente es la reapertura;
     * Descartado no tiene salida.
     *
     * @return list<self>
     */
    public function siguientes(): array
    {
        return match ($this) {
            self::Pendiente => [self::Validada, self::Descartado],
            self::Validada => [self::EnProceso, self::Descartado],
            self::EnProceso => [self::Resuelto],
            self::Resuelto => [self::Pendiente],
            self::Descartado => [],
        };
    }

    public function puedePasarA(self $nuevo): bool
    {
        return in_array($nuevo, $this->siguientes(), true);
    }
}
