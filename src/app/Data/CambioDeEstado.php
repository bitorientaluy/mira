<?php

namespace App\Data;

use App\Enums\EstadoIncidencia;
use App\Enums\MotivoDescarte;
use App\Models\Area;

/**
 * Lo que pide un funcionario al cambiar el estado de una incidencia. Lo arma CambiarEstadoRequest
 * y lo aplica IncidenciaService::cambiarEstado(), que decide si es válido.
 */
final readonly class CambioDeEstado
{
    public function __construct(
        public EstadoIncidencia $estado,
        public ?string $comentario = null,
        public ?MotivoDescarte $motivo = null,  // solo al descartar
        public ?Area $area = null,              // solo al validar: el operador confirma o corrige el área
    ) {}
}
