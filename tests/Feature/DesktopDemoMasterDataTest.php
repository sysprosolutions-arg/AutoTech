<?php

namespace Tests\Feature;

use App\Models\Cliente;
use App\Models\Repuesto;
use App\Models\User;
use Database\Seeders\PresupuestosMasterDataSeeder;
use Database\Seeders\ReparacionesMasterDataSeeder;
use Database\Seeders\RepuestosMasterDataSeeder;
use Database\Seeders\TurnosMasterDataSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class DesktopDemoMasterDataTest extends TestCase
{
    use RefreshDatabase;

    public function test_sprint_one_uses_a_new_desktop_release_version(): void
    {
        $this->assertSame('0.2.0', config('nativephp.version'));
    }

    public function test_master_data_is_added_to_an_existing_desktop_installation(): void
    {
        User::factory()->create(['username' => 'admin']);

        $this->seedDesktopMasterData();

        $this->assertDatabaseCount('clientes', 3);
        $this->assertDatabaseCount('vehiculos', 4);
        $this->assertDatabaseCount('empleados', 3);
        $this->assertDatabaseCount('tipos_servicio', 5);
        $this->assertDatabaseCount('proveedores', 3);
        $this->assertDatabaseCount('repuestos', 4);
        $this->assertDatabaseCount('tipos_reparacion', 4);
        $this->assertDatabaseCount('estados_reparacion', 3);
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
        $this->assertDatabaseCount('clientes', 3);
        $this->assertDatabaseCount('proveedores', 3);
        $this->assertDatabaseCount('repuestos', 4);
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
