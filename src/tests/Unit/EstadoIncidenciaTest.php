<?php

namespace Tests\Unit;

use App\Enums\EstadoIncidencia as E;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * PU-06: transiciones permitidas del flujo Pendiente → Validada → En proceso → Resuelto,
 * más Descartado y la reapertura Resuelto → Pendiente.
 */
class EstadoIncidenciaTest extends TestCase
{
    public static function permitidas(): array
    {
        return [
            'validar' => [E::Pendiente, E::Validada],
            'descartar pendiente' => [E::Pendiente, E::Descartado],
            'empezar' => [E::Validada, E::EnProceso],
            'descartar validada' => [E::Validada, E::Descartado],
            'resolver' => [E::EnProceso, E::Resuelto],
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
}
