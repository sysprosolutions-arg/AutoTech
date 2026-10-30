<?php

namespace Tests\Feature;

use App\Models\Proveedor;
use App\Models\Repuesto;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RepuestoConsultationTest extends TestCase
{
    use RefreshDatabase;

    private User $responsableRepuestos;

    private Proveedor $proveedor;

    private Repuesto $repuesto;

    protected function setUp(): void
    {
        parent::setUp();

        $this->responsableRepuestos = User::factory()->create();
        $role = Role::query()->create(['name' => 'repuestos', 'display_name' => 'Responsable de Repuestos']);
        $this->responsableRepuestos->roles()->attach($role);

        $this->proveedor = Proveedor::query()->create([
            'cuit' => '30-70123456-7',
            'razon_social' => 'Autopartes del Plata S.A.',
            'nombre_fantasia' => 'Autopartes del Plata',
            'localidad' => 'Ciudad Autónoma de Buenos Aires',
            'telefono' => '11 4588-1200',
            'email' => 'ventas@autopartes.example',
            'activo' => true,
        ]);

        $this->repuesto = Repuesto::query()->create([
            'proveedor_id' => $this->proveedor->id,
            'codigo' => 'REP-FIL-001',
            'nombre' => 'Filtro de aceite',
            'descripcion' => 'Filtro original compatible con motores Toyota seleccionados.',
            'marca' => 'Toyota',
            'original' => true,
            'precio_venta' => '18500.00',
            'stock' => 12,
            'activo' => true,
        ]);
    }

    public function test_guest_cannot_access_spare_part_consultation(): void
    {
        $this->get('/repuestos')->assertRedirect('/login');
    }

    public function test_user_without_an_allowed_role_cannot_access_spare_part_consultation(): void
    {
        $this->actingAs(User::factory()->create())
            ->get('/repuestos')
            ->assertForbidden();
    }

    public function test_responsible_user_can_list_registered_spare_parts(): void
    {
        $this->actingAs($this->responsableRepuestos)
            ->get('/repuestos')
            ->assertOk()
            ->assertSee('REP-FIL-001')
            ->assertSee('Filtro de aceite')
            ->assertSee('Toyota')
            ->assertSee('Autopartes del Plata S.A.')
            ->assertSee('Original')
            ->assertSee('12');
    }

    public function test_spare_parts_can_be_searched_by_available_identification_data(): void
    {
        foreach ([(string) $this->repuesto->id, 'REP-FIL-001', 'Filtro de aceite', 'Toyota', 'Autopartes del Plata'] as $search) {
            $this->actingAs($this->responsableRepuestos)
                ->get('/repuestos?'.http_build_query(['buscar' => $search]))
                ->assertOk()
                ->assertSee('REP-FIL-001');
        }
    }

    public function test_spare_parts_can_be_filtered_by_type_and_state(): void
    {
        Repuesto::query()->create([
            'proveedor_id' => $this->proveedor->id,
            'codigo' => 'REP-ALT-001',
            'nombre' => 'Filtro alternativo',
            'marca' => 'Bosch',
            'original' => false,
            'precio_venta' => '12000.00',
            'stock' => 0,
            'activo' => false,
        ]);

        $this->actingAs($this->responsableRepuestos)
            ->get('/repuestos?tipo=original&estado=activo')
            ->assertOk()
            ->assertSee('REP-FIL-001')
            ->assertDontSeeText('REP-ALT-001');

        $this->actingAs($this->responsableRepuestos)
            ->get('/repuestos?tipo=alternativo&estado=inactivo')
            ->assertOk()
            ->assertSee('REP-ALT-001')
            ->assertDontSeeText('REP-FIL-001');
    }

    public function test_empty_search_result_is_reported_without_changing_data(): void
    {
        $this->actingAs($this->responsableRepuestos)
            ->get('/repuestos?buscar=REP-INEXISTENTE')
            ->assertOk()
            ->assertSee('No se encontraron repuestos')
            ->assertSee('Nueva búsqueda');

        $this->assertDatabaseHas('repuestos', ['id' => $this->repuesto->id, 'stock' => 12]);
    }

    public function test_responsible_user_can_view_the_complete_spare_part_record(): void
    {
        $this->actingAs($this->responsableRepuestos)
            ->get("/repuestos/{$this->repuesto->id}")
            ->assertOk()
            ->assertSee('REP-FIL-001')
            ->assertSee('Filtro de aceite')
            ->assertSee('Filtro original compatible con motores Toyota seleccionados.')
            ->assertSee('Original')
            ->assertSee('Toyota')
            ->assertSee('$18.500,00')
            ->assertSee('12 unidades')
            ->assertSee('Autopartes del Plata S.A.')
            ->assertSee('30-70123456-7');
    }
}
