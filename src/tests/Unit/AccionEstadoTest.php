<?php

namespace Tests\Unit;

use App\Enums\EstadoIncidencia as E;
use App\ViewModels\AccionEstado;
use PHPUnit\Framework\TestCase;

/**
 * Botones de la tarjeta "Acciones": orden, estilo y texto según el estado actual (T-116, T-201).
 */
class AccionEstadoTest extends TestCase
{
    private function resumen(E $actual): array
    {
        return array_map(fn (AccionEstado $a) => [$a->texto, $a->tipo], AccionEstado::desde($actual));
    }

    public function test_desde_validada_primero_avanza_despues_vuelve_y_al_final_descarta(): void
    {
        $this->assertSame([
            ['Empezar trabajo', AccionEstado::AVANZAR],
            ['Volver a pendiente', AccionEstado::RETROCEDER],
            ['Descartar', AccionEstado::DESCARTAR],
        ], $this->resumen(E::Validada));
    }

    public function test_resuelto_solo_puede_reabrirse(): void
    {
        $acciones = AccionEstado::desde(E::Resuelto);

        $this->assertCount(1, $acciones);
        $this->assertSame('Reabrir', $acciones[0]->texto);
        $this->assertSame('replay', $acciones[0]->icono);
        $this->assertSame('boton-secundario', $acciones[0]->claseBoton());
    }

    public function test_descartado_no_tiene_acciones(): void
    {
        $this->assertSame([], AccionEstado::desde(E::Descartado));
    }

    public function test_solo_validar_desde_pendiente_confirma_el_area(): void
    {
        $confirman = fn (E $actual) => array_values(array_map(
            fn (AccionEstado $a) => $a->texto,
            array_filter(AccionEstado::desde($actual), fn (AccionEstado $a) => $a->confirmaArea()),
        ));

        $this->assertSame(['Validar'], $confirman(E::Pendiente));
        $this->assertSame([], $confirman(E::EnProceso)); // "Volver a validada" no confirma el área
    }

    public function test_descartar_usa_el_estilo_de_peligro(): void
    {
        $descarte = collect(AccionEstado::desde(E::Pendiente))->first(fn (AccionEstado $a) => $a->esDescarte());

        $this->assertNotNull($descarte);
        $this->assertSame('boton-peligro', $descarte->claseBoton());
    }
}
