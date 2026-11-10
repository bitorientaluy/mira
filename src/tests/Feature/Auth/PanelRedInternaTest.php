<?php

namespace Tests\Feature\Auth;

use App\Enums\EstadoIncidencia;
use App\Models\Categoria;
use App\Models\Incidencia;
use App\Models\Rol;
use App\Models\User;
use App\Services\IncidenciaService;
use Database\Seeders\AreaCategoriaSeeder;
use Database\Seeders\RolSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Log;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

/**
 * T-202: el panel solo responde desde las redes internas (Programación 4.b).
 * Las IP son las de la topología: VLAN 30 (funcionarios), proxy de la DMZ y una IP pública cualquiera.
 */
class PanelRedInternaTest extends TestCase
{
    use RefreshDatabase;

    private const IP_VLAN30 = '192.168.30.50';
    private const IP_PROXY_DMZ = '192.168.20.10';
    private const IP_INTERNET = '190.64.10.20';

    private User $operador;

    private Incidencia $incidencia;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed([RolSeeder::class, AreaCategoriaSeeder::class]);
        config(['mira.redes_panel' => ['192.168.30.0/24']]); // como en SRV-POSIX

        $this->operador = User::factory()->conRol(Rol::OPERADOR)->create();
        $this->incidencia = app(IncidenciaService::class)->crear(
            ['categoria_id' => Categoria::first()->id, 'descripcion' => 'Contenedor desbordado', 'lat' => -34.9011, 'lng' => -56.1645],
            User::factory()->conRol(Rol::CIUDADANO)->create(),
        );
    }

    private function desde(string $ip, array $cabeceras = []): static
    {
        return $this->withServerVariables(['REMOTE_ADDR' => $ip])->withHeaders($cabeceras);
    }

    public function test_desde_la_vlan_30_el_operador_usa_el_panel(): void
    {
        $this->desde(self::IP_VLAN30)->actingAs($this->operador)->get('/panel')->assertOk();
        $this->desde(self::IP_VLAN30)->actingAs($this->operador)
            ->get(route('panel.incidencias.show', $this->incidencia->id))->assertOk();
    }

    public static function origenesExternos(): array
    {
        return ['proxy de la DMZ' => [self::IP_PROXY_DMZ], 'internet' => [self::IP_INTERNET]];
    }

    #[DataProvider('origenesExternos')]
    public function test_desde_afuera_el_panel_da_404_aunque_haya_sesion_de_operador(string $ip): void
    {
        $this->desde($ip)->actingAs($this->operador)->get('/panel')->assertNotFound();
        $this->desde($ip)->actingAs($this->operador)
            ->get(route('panel.incidencias.show', $this->incidencia->id))->assertNotFound();
        $this->desde($ip)->actingAs($this->operador)
            ->get(route('panel.incidencias.foto', $this->incidencia))->assertNotFound();
        $this->desde($ip)->actingAs($this->operador)
            ->patch(route('panel.incidencias.estado', $this->incidencia), ['estado' => 'Validada'])->assertNotFound();

        $this->assertSame(EstadoIncidencia::Pendiente, $this->incidencia->refresh()->estado);
    }

    public function test_desde_internet_un_invitado_no_ve_ni_el_login_del_panel(): void
    {
        // Da 404 antes de llegar a auth: no redirige al login, así no se revela que el panel existe
        $this->desde(self::IP_INTERNET)->get('/panel')->assertNotFound();
    }

    public function test_un_x_forwarded_for_falso_no_abre_el_panel(): void
    {
        $this->desde(self::IP_INTERNET, ['X-Forwarded-For' => self::IP_VLAN30])
            ->actingAs($this->operador)
            ->get('/panel')
            ->assertNotFound();
    }

    public function test_el_rechazo_queda_en_el_log_con_la_ip_y_la_ruta(): void
    {
        Log::spy();

        $this->desde(self::IP_INTERNET)->get('/panel');

        Log::shouldHaveReceived('warning')->once()->withArgs(
            fn (string $mensaje, array $contexto) => str_contains($mensaje, 'fuera de la red interna')
                && $contexto['ip'] === self::IP_INTERNET
                && $contexto['ruta'] === 'panel',
        );
    }

    public function test_las_rutas_publicas_y_del_ciudadano_siguen_funcionando_desde_internet(): void
    {
        $this->desde(self::IP_INTERNET)->get('/login')->assertOk();
        $this->desde(self::IP_INTERNET)->get('/registro')->assertOk();

        $ciudadano = User::factory()->conRol(Rol::CIUDADANO)->create();
        $this->desde(self::IP_INTERNET)->actingAs($ciudadano)->get('/reportar')->assertOk();
    }

    public function test_desde_la_vlan_30_el_ciudadano_sigue_sin_entrar_al_panel(): void
    {
        // Estar en la red interna no alcanza: después siguen el login y el rol
        $ciudadano = User::factory()->conRol(Rol::CIUDADANO)->create();

        $this->desde(self::IP_VLAN30)->actingAs($ciudadano)->get('/panel')->assertForbidden();
    }

    public function test_desde_la_vlan_30_el_invitado_va_al_login(): void
    {
        $this->desde(self::IP_VLAN30)->get('/panel')->assertRedirect(route('login'));
    }
}
