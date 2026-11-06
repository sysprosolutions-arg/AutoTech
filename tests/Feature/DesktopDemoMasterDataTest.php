<?php

namespace Tests\Feature;

use App\Models\Cliente;
use App\Models\Presupuesto;
use App\Models\Reparacion;
use App\Models\Repuesto;
use App\Models\Turno;
use App\Models\User;
use App\Providers\NativeAppServiceProvider;
use Carbon\CarbonImmutable;
use Database\Seeders\DatabaseSeeder;
use Database\Seeders\DemoOperationalDataSeeder;
use Database\Seeders\PresupuestosMasterDataSeeder;
use Database\Seeders\ReparacionesMasterDataSeeder;
use Database\Seeders\RepuestosMasterDataSeeder;
use Database\Seeders\TurnosMasterDataSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use ReflectionMethod;
use Tests\TestCase;

class DesktopDemoMasterDataTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        CarbonImmutable::setTestNow();

        parent::tearDown();
    }

    public function test_desktop_demo_uses_the_current_release_version(): void
    {
        $this->assertSame('0.4.6', config('nativephp.version'));
    }

    public function test_desktop_php_disables_opcache_for_windows_aslr_compatibility(): void
    {
        $phpIni = app(NativeAppServiceProvider::class)->phpIni();

        $this->assertSame('0', $phpIni['opcache.enable']);
        $this->assertSame('0', $phpIni['opcache.enable_cli']);
    }

    public function test_master_and_operational_data_are_added_to_an_existing_desktop_installation(): void
    {
        CarbonImmutable::setTestNow('2026-11-05 14:00:00');
        User::factory()->create(['username' => 'admin']);

        $provider = app(NativeAppServiceProvider::class);
        $seedDesktopDemo = new ReflectionMethod($provider, 'seedDesktopDemoIfNecessary');
        $seedDesktopDemo->invoke($provider);

        $this->assertDatabaseCount('clientes', 8);
        $this->assertDatabaseCount('vehiculos', 12);
        $this->assertDatabaseCount('empleados', 3);
        $this->assertDatabaseCount('tipos_servicio', 5);
        $this->assertDatabaseCount('proveedores', 3);
        $this->assertDatabaseCount('repuestos', 28);
        $this->assertDatabaseCount('tipos_reparacion', 4);
        $this->assertDatabaseCount('estados_reparacion', 3);
        $this->assertDatabaseCount('turnos', 12);
        $this->assertDatabaseCount('presupuestos', 8);
        $this->assertDatabaseCount('ordenes_trabajo', 5);
        $this->assertDatabaseCount('reparaciones', 5);

        $appointment = Turno::query()->where('observaciones', 'like', '%AUTOTECH-DEMO:future-service%')->firstOrFail();
        $appointment->update(['motivo' => 'Cambio realizado por el evaluador']);

        $seedDesktopDemo->invoke($provider);

        $this->assertDatabaseCount('turnos', 12);
        $this->assertDatabaseCount('presupuestos', 8);
        $this->assertDatabaseCount('ordenes_trabajo', 5);
        $this->assertDatabaseCount('reparaciones', 5);
        $this->assertDatabaseHas('turnos', [
            'id' => $appointment->id,
            'motivo' => 'Cambio realizado por el evaluador',
        ]);
    }

    public function test_reseeding_does_not_overwrite_operational_changes_or_duplicate_data(): void
    {
        User::factory()->create(['username' => 'admin']);
        $this->seedDesktopMasterData();

        Cliente::query()->where('dni', '30111222')->update(['telefono' => '11 9999-0000']);
        Repuesto::query()->where('codigo', 'REP-FIL-001')->update(['stock' => 2]);

        $this->seedDesktopMasterData();

        $this->assertDatabaseHas('clientes', [
            'dni' => '30111222',
            'telefono' => '11 9999-0000',
        ]);
        $this->assertDatabaseHas('repuestos', [
            'codigo' => 'REP-FIL-001',
            'stock' => 2,
        ]);
        $this->assertDatabaseHas('repuestos', [
            'codigo' => 'REP-FIL-001',
            'descripcion' => 'Filtro original compatible con motores Toyota seleccionados.',
        ]);
        $this->assertDatabaseCount('clientes', 8);
        $this->assertDatabaseCount('proveedores', 3);
        $this->assertDatabaseCount('repuestos', 28);
    }

    public function test_fresh_demo_contains_editable_operational_data_relative_to_installation_day(): void
    {
        CarbonImmutable::setTestNow('2026-11-04 14:00:00');

        $this->seed(DatabaseSeeder::class);

        $this->assertDatabaseCount('clientes', 8);
        $this->assertDatabaseCount('vehiculos', 12);
        $this->assertDatabaseCount('repuestos', 28);
        $this->assertDatabaseCount('turnos', 12);
        $this->assertDatabaseCount('presupuestos', 8);
        $this->assertDatabaseCount('ordenes_trabajo', 5);
        $this->assertDatabaseCount('reparaciones', 5);
        $this->assertSame(3, Reparacion::query()->whereHas('estado', fn ($query) => $query->where('codigo', 'en-proceso'))->count());
        $this->assertSame(2, Reparacion::query()->whereHas('estado', fn ($query) => $query->where('codigo', 'finalizada'))->count());
        $this->assertSame(2, Presupuesto::query()->whereHas('aprobacion', fn ($query) => $query->where('estado', 'pendiente'))->count());
        $this->assertSame(4, Turno::query()->whereDate('fecha', '>', '2026-11-04')->doesntHave('presupuesto')->count());

        $appointment = Turno::query()->where('observaciones', 'like', '%AUTOTECH-DEMO:future-service%')->firstOrFail();
        $appointment->update(['motivo' => 'Motivo editado durante la prueba']);

        $this->assertDatabaseHas('turnos', [
            'id' => $appointment->id,
            'motivo' => 'Motivo editado durante la prueba',
        ]);
    }

    public function test_operational_demo_seeding_is_idempotent(): void
    {
        CarbonImmutable::setTestNow('2026-11-04 14:00:00');
        $this->seed(DatabaseSeeder::class);

        $this->seed(DemoOperationalDataSeeder::class);

        $this->assertDatabaseCount('turnos', 12);
        $this->assertDatabaseCount('presupuestos', 8);
        $this->assertDatabaseCount('ordenes_trabajo', 5);
        $this->assertDatabaseCount('reparaciones', 5);
    }

    public function test_fresh_demo_dashboard_exposes_the_operational_scenario(): void
    {
        CarbonImmutable::setTestNow('2026-11-04 14:00:00');
        $this->seed(DatabaseSeeder::class);

        $administrator = User::query()->where('username', 'admin')->firstOrFail();
        $response = $this->actingAs($administrator)->get('/dashboard');

        $response
            ->assertOk()
            ->assertSee('03/06 ACTIVOS')
            ->assertSee('6 acciones sugeridas')
            ->assertSee('5 atenciones registradas')
            ->assertSee('2 trabajos completados')
            ->assertSee('2 órdenes finalizaron hoy')
            ->assertSee('data-map-vehicle="AB123CD"', false)
            ->assertSee('data-map-vehicle="AE321JK"', false)
            ->assertSee('data-map-vehicle="AH246QR"', false);
        $this->assertSame(3, substr_count($response->getContent(), 'data-map-vehicle='));
    }

    private function seedDesktopMasterData(): void
    {
        foreach ([
            TurnosMasterDataSeeder::class,
            PresupuestosMasterDataSeeder::class,
            RepuestosMasterDataSeeder::class,
            ReparacionesMasterDataSeeder::class,
        ] as $seeder) {
            Artisan::call('db:seed', [
                '--class' => $seeder,
                '--force' => true,
            ]);
        }
    }
}
