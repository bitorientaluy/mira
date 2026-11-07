<?php

namespace Tests\Unit;

use App\Enums\EstadoIncidencia as E;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * PU-06: transiciones permitidas del flujo Pendiente → Validada → En proceso → Resuelto,
 * Descartado, un paso atrás para corregir errores y la reapertura Resuelto → Pendiente.
 */
class EstadoIncidenciaTest extends TestCase
{
    public static function permitidas(): array
    {
        return [
            'validar' => [E::Pendiente, E::Validada],
            'descartar pendiente' => [E::Pendiente, E::Descartado],
            'empezar' => [E::Validada, E::EnProceso],
            'volver a pendiente' => [E::Validada, E::Pendiente],
            'descartar validada' => [E::Validada, E::Descartado],
            'resolver' => [E::EnProceso, E::Resuelto],
            'volver a validada' => [E::EnProceso, E::Validada],
            'reabrir' => [E::Resuelto, E::Pendiente],
        ];
    }

    #[DataProvider('permitidas')]
    public function test_transicion_permitida(E $actual, E $nuevo): void
    {
        $this->assertTrue($actual->puedePasarA($nuevo));
    }

    public function test_cualquier_otra_transicion_se_rechaza(): void
    {
        $permitidas = array_map(fn ($par) => $par[0]->value.'→'.$par[1]->value, self::permitidas());

        foreach (E::cases() as $actual) {
            foreach (E::cases() as $nuevo) {
                if (! in_array($actual->value.'→'.$nuevo->value, $permitidas, true)) {
                    $this->assertFalse($actual->puedePasarA($nuevo), "{$actual->value} → {$nuevo->value} no debería estar permitida");
                }
            }
        }
    }

    public function test_descartado_no_tiene_salida(): void
    {
        $this->assertSame([], E::Descartado->siguientes());
    }

    public function test_distingue_avanzar_de_volver_atras(): void
    {
        $this->assertTrue(E::Pendiente->avanzaA(E::Validada));
        $this->assertTrue(E::EnProceso->avanzaA(E::Resuelto));
        $this->assertTrue(E::Validada->avanzaA(E::Descartado));
        $this->assertFalse(E::EnProceso->avanzaA(E::Validada));
        $this->assertFalse(E::Validada->avanzaA(E::Pendiente));
        $this->assertFalse(E::Resuelto->avanzaA(E::Pendiente));
    }

    public function test_cada_transicion_tiene_su_texto_de_boton(): void
    {
        $this->assertSame('Validar', E::Pendiente->accionHacia(E::Validada));
        $this->assertSame('Empezar trabajo', E::Validada->accionHacia(E::EnProceso));
        $this->assertSame('Marcar como resuelta', E::EnProceso->accionHacia(E::Resuelto));
        $this->assertSame('Volver a validada', E::EnProceso->accionHacia(E::Validada));
        $this->assertSame('Volver a pendiente', E::Validada->accionHacia(E::Pendiente));
        $this->assertSame('Reabrir', E::Resuelto->accionHacia(E::Pendiente));
    }
}
