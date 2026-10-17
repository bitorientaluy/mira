<?php

namespace App\Enums;

enum MotivoDescarte: string
{
    use TieneValores;

    case Falso = 'Falso';
    case Duplicado = 'Duplicado';
    case NoCorresponde = 'No corresponde';
}
