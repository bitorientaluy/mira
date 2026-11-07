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
     * Estados a los que se puede pasar desde este: el avance normal, un paso atrás para corregir
     * errores (En proceso → Validada, Validada → Pendiente) y la reapertura (Resuelto → Pendiente).
     * Descartado no tiene salida.
     *
     * @return list<self>
     */
    public function siguientes(): array
    {
        return match ($this) {
            self::Pendiente => [self::Validada, self::Descartado],
            self::Validada => [self::EnProceso, self::Pendiente, self::Descartado],
            self::EnProceso => [self::Resuelto, self::Validada],
            self::Resuelto => [self::Pendiente],
            self::Descartado => [],
        };
    }

    public function puedePasarA(self $nuevo): bool
    {
        return in_array($nuevo, $this->siguientes(), true);
    }

    /**
     * True si pasar a $nuevo es avanzar en el flujo (o descartar); false si es volver atrás o reabrir.
     */
    public function avanzaA(self $nuevo): bool
    {
        if ($nuevo === self::Descartado) {
            return true;
        }
        $orden = [self::Pendiente, self::Validada, self::EnProceso, self::Resuelto];

        return array_search($nuevo, $orden, true) > array_search($this, $orden, true);
    }

    /**
     * Texto del botón que lleva de este estado a $nuevo (ej. "Validar", "Reabrir").
     */
    public function accionHacia(self $nuevo): string
    {
        return match ($nuevo) {
            self::Validada => $this === self::EnProceso ? 'Volver a validada' : 'Validar',
            self::EnProceso => 'Empezar trabajo',
            self::Resuelto => 'Marcar como resuelta',
            self::Pendiente => $this === self::Resuelto ? 'Reabrir' : 'Volver a pendiente',
            self::Descartado => 'Descartar',
        };
    }
}
