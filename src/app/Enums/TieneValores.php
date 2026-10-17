<?php

namespace App\Enums;

/**
 * Para enums respaldados por string: devuelve sus valores (ej. para columnas ENUM y validaciones).
 */
trait TieneValores
{
    /** @return list<string> */
    public static function valores(): array
    {
        return array_column(self::cases(), 'value');
    }
}
