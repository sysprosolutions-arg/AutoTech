<?php

namespace Tests\Feature;

use App\Models\Proveedor;
use App\Models\Repuesto;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RepuestoSupplierReportTest extends TestCase
{
    use RefreshDatabase;

    private User $responsable;

    private Proveedor $proveedorBosch;

    private Proveedor $proveedorToyota;

    private Proveedor $proveedorSinRepuestos;

    protected function setUp(): void
    {
        parent::setUp();

        $this->responsable = User::factory()->create();
        $role = Role::query()->create(['name' => 'repuestos', 'display_name' => 'Responsable de Repuestos']);
        $this->responsable->roles()->attach($role);

        $this->proveedorBosch = $this->createSupplier('30-11111111-1', 'Autopartes Bosch S.A.');
        $this->proveedorToyota = $this->createSupplier('30-22222222-2', 'Toyota Repuestos S.A.');
        $this->proveedorSinRepuestos = $this->createSupplier('30-33333333-3', 'Proveedor sin stock S.A.');

        $this->createPart('REP-BOS-001', 'Pastillas', $this->proveedorBosch);
        $this->createPart('REP-TOY-001', 'Filtro', $this->proveedorToyota);
    }

    public function test_guest_and_unauthorized_user_cannot_access_supplier_report(): void
    {
        $this->get('/repuestos/reportes/proveedores')->assertRedirect('/login');
        $this->actingAs(User::factory()->create())->get('/repuestos/reportes/proveedores')->assertForbidden();
    }

    public function test_report_groups_all_active_spare_parts_by_supplier(): void
    {
        $this->actingAs($this->responsable)
            ->get('/repuestos/reportes/proveedores')
            ->assertOk()
            ->assertSee('Autopartes Bosch S.A.')
            ->assertSee('Toyota Repuestos S.A.')
            ->assertSee('REP-BOS-001')
            ->assertSee('REP-TOY-001');
    }

    public function test_report_filters_one_supplier_without_including_others(): void
    {
        $this->actingAs($this->responsable)
            ->get('/repuestos/reportes/proveedores?'.http_build_query(['proveedor_id' => $this->proveedorBosch->id]))
            ->assertOk()
            ->assertSee('REP-BOS-001')
            ->assertDontSeeText('REP-TOY-001');
    }

    public function test_report_explains_when_selected_supplier_has_no_spare_parts(): void
    {
        $this->actingAs($this->responsable)
            ->get('/repuestos/reportes/proveedores?'.http_build_query(['proveedor_id' => $this->proveedorSinRepuestos->id]))
            ->assertOk()
            ->assertSee('No hay repuestos para el proveedor seleccionado')
            ->assertDontSeeText('REP-BOS-001');
    }

    public function test_report_rejects_a_supplier_that_does_not_exist(): void
    {
        $this->actingAs($this->responsable)
            ->get('/repuestos/reportes/proveedores?proveedor_id=999999')
            ->assertRedirect()
            ->assertSessionHasErrors('proveedor_id');
    }

    private function createSupplier(string $cuit, string $razonSocial): Proveedor
    {
        return Proveedor::query()->create([
            'cuit' => $cuit,
            'razon_social' => $razonSocial,
            'localidad' => 'Buenos Aires',
            'activo' => true,
        ]);
    }

    private function createPart(string $codigo, string $nombre, Proveedor $proveedor): void
    {
        Repuesto::query()->create([
            'proveedor_id' => $proveedor->id,
            'codigo' => $codigo,
            'nombre' => $nombre,
            'marca' => 'Marca de prueba',
            'original' => false,
            'precio_venta' => 1000,
            'stock' => 2,
            'activo' => true,
        ]);
    }
}
