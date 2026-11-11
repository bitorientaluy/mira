<?php

namespace App\Policies;

use App\Models\Incidencia;
use App\Models\Rol;
use App\Models\User;

/**
 * Quién puede hacer qué con una incidencia (T-201). Laravel la encuentra sola por el nombre.
 *
 * Responde solo "quién": si una transición está permitida desde el estado actual
 * lo decide el flujo (EstadoIncidencia) dentro de IncidenciaService.
 */
class IncidenciaPolicy
{
    /**
     * Listado del panel interno.
     */
    public function viewAny(User $usuario): bool
    {
        return $usuario->tieneAlgunRol(Rol::FUNCIONARIOS);
    }

    /**
     * Detalle, foto y confirmación: los funcionarios, o el ciudadano que hizo el reporte.
     */
    public function view(User $usuario, Incidencia $incidencia): bool
    {
        return $usuario->tieneAlgunRol(Rol::FUNCIONARIOS) || $incidencia->ciudadano_id === $usuario->id;
    }

    /**
     * Validar, empezar, resolver, volver atrás, reabrir y descartar.
     */
    public function cambiarEstado(User $usuario, Incidencia $incidencia): bool
    {
        return $usuario->tieneAlgunRol(Rol::GESTORES);
    }
}
