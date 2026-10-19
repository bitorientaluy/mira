<?php

namespace Tests\Unit;

use App\Services\IncidenciaService;
use PHPUnit\Framework\TestCase;

/**
 * PU-01 (código de seguimiento) y el WKT del punto, sin base de datos.
 */
class IncidenciaServiceTest extends TestCase
{
    public function test_pu01_el_codigo_respeta_el_formato_mir_anio_y_seis_digitos(): void
    {
        $this->assertSame('MIR-2026-000123', IncidenciaService::codigo(123, 2026));
        $this->assertSame('MIR-2026-000001', IncidenciaService::codigo(1, 2026));
        $this->assertSame('MIR-2027-999999', IncidenciaService::codigo(999999, 2027));
    }

    public function test_pu01_ids_distintos_dan_codigos_distintos(): void
    {
        $codigos = array_map(fn ($id) => IncidenciaService::codigo($id, 2026), range(1, 500));

        $this->assertCount(500, array_unique($codigos));
    }

    public function test_el_punto_va_en_orden_longitud_latitud(): void
    {
        $this->assertSame('POINT(-56.1645000 -34.9011000)', IncidenciaService::punto(-34.9011, -56.1645));
    }
}
