<?php

namespace Tests\Unit;

use App\Http\Middleware\EnsureRol;
use App\Models\Rol;
use App\Models\User;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tests\TestCase;

/**
 * PU-10 (parte): el middleware de rol, sin base de datos ni rutas.
 */
class EnsureRolTest extends TestCase
{
    private function pedido(?string $rol): Request
    {
        $pedido = Request::create('/panel');

        if ($rol !== null) {
            $usuario = new User;
            $usuario->setRelation('rol', new Rol(['nombre' => $rol]));
            $pedido->setUserResolver(fn () => $usuario);
        }

        return $pedido;
    }

    private function ejecutar(?string $rol, string ...$permitidos): Response
    {
        return (new EnsureRol)->handle($this->pedido($rol), fn () => new Response('siguiente'), ...$permitidos);
    }

    private function assertCortaCon403(?string $rol, string ...$permitidos): void
    {
        try {
            $this->ejecutar($rol, ...$permitidos);
            $this->fail('El middleware dejó pasar un pedido que debía cortar.');
        } catch (HttpException $e) {
            $this->assertSame(403, $e->getStatusCode());
        }
    }

    public function test_deja_pasar_si_el_rol_esta_permitido(): void
    {
        $this->assertSame('siguiente', $this->ejecutar(Rol::OPERADOR, Rol::OPERADOR, Rol::ADMIN)->getContent());
        $this->assertSame('siguiente', $this->ejecutar(Rol::ADMIN, Rol::OPERADOR, Rol::ADMIN)->getContent());
    }

    public function test_corta_con_403_si_el_rol_no_esta_permitido(): void
    {
        $this->assertCortaCon403(Rol::CIUDADANO, Rol::OPERADOR, Rol::ADMIN);
    }

    public function test_corta_con_403_si_no_hay_usuario(): void
    {
        $this->assertCortaCon403(null, Rol::CIUDADANO);
    }
}
