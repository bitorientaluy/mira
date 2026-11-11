<?php

namespace App\ViewModels;

use App\Enums\EstadoIncidencia;

/**
 * Un botón de la tarjeta "Acciones" del panel: a qué estado lleva, con qué texto, ícono y estilo.
 * Saca de la vista la decisión de qué mostrar y en qué orden.
 */
final readonly class AccionEstado
{
    public const AVANZAR = 'avanzar';
    public const RETROCEDER = 'retroceder';
    public const DESCARTAR = 'descartar';

    private const ICONOS = [
        'Validada' => 'verified',
        'En proceso' => 'engineering',
        'Resuelto' => 'task_alt',
        'Descartado' => 'block',
    ];

    public function __construct(
        public EstadoIncidencia $destino,
        public string $texto,
        public string $icono,
        public string $tipo,
    ) {}

    /**
     * Acciones posibles desde el estado actual: primero las que avanzan, después volver atrás o reabrir,
     * y al final descartar.
     *
     * @return list<self>
     */
    public static function desde(EstadoIncidencia $actual): array
    {
        $acciones = array_map(fn (EstadoIncidencia $destino) => self::crear($actual, $destino), $actual->siguientes());
        $orden = [self::AVANZAR => 0, self::RETROCEDER => 1, self::DESCARTAR => 2];
        usort($acciones, fn (self $a, self $b) => $orden[$a->tipo] <=> $orden[$b->tipo]);

        return $acciones;
    }

    private static function crear(EstadoIncidencia $actual, EstadoIncidencia $destino): self
    {
        $tipo = match (true) {
            $destino === EstadoIncidencia::Descartado => self::DESCARTAR,
            $actual->avanzaA($destino) => self::AVANZAR,
            default => self::RETROCEDER,
        };
        $icono = $tipo === self::RETROCEDER
            ? ($actual === EstadoIncidencia::Resuelto ? 'replay' : 'undo')
            : self::ICONOS[$destino->value];

        return new self($destino, $actual->accionHacia($destino), $icono, $tipo);
    }

    /**
     * Validar (desde Pendiente) es el momento en que el operador confirma o corrige el área.
     */
    public function confirmaArea(): bool
    {
        return $this->destino === EstadoIncidencia::Validada && $this->tipo === self::AVANZAR;
    }

    public function esDescarte(): bool
    {
        return $this->tipo === self::DESCARTAR;
    }

    public function claseBoton(): string
    {
        return match ($this->tipo) {
            self::AVANZAR => 'boton-primario',
            self::RETROCEDER => 'boton-secundario',
            self::DESCARTAR => 'boton-peligro',
        };
    }
}
