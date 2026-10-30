<?php

namespace Tests\Feature;

use App\Models\Proveedor;
use App\Models\Repuesto;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RepuestoRegistrationTest extends TestCase
{
    use RefreshDatabase;

    private User $responsableRepuestos;

    private Proveedor $proveedor;

    protected function setUp(): void
    {
        parent::setUp();

        $this->responsableRepuestos = User::factory()->create();
        $role = Role::query()->create([
            'name' => 'repuestos',
            'display_name' => 'Responsable de Repuestos',
        ]);
        $this->responsableRepuestos->roles()->attach($role);

        $this->proveedor = Proveedor::query()->create([
            'cuit' => '30-70123456-7',
            'razon_social' => 'Autopartes del Plata S.A.',
            'localidad' => 'Ciudad Autónoma de Buenos Aires',
            'activo' => true,
        ]);
    }

    public function test_guest_cannot_access_spare_part_registration(): void
    {
        $this->get('/repuestos/crear')->assertRedirect('/login');
    }

    public function test_user_without_an_allowed_role_cannot_register_a_spare_part(): void
    {
        $this->actingAs(User::factory()->create())
            ->post('/repuestos', $this->validPayload())
            ->assertForbidden();

        $this->assertDatabaseCount('repuestos', 0);
    }

    public function test_responsible_user_can_view_the_registration_form(): void
    {
        $this->actingAs($this->responsableRepuestos)
            ->get('/repuestos/crear')
            ->assertOk()
            ->assertSee('Registrar repuesto')
            ->assertSee('Autopartes del Plata S.A.')
            ->assertSee('Original')
            ->assertSee('Alternativo');
    }

    public function test_responsible_user_can_register_a_spare_part(): void
    {
        $this->actingAs($this->responsableRepuestos)
            ->post('/repuestos', $this->validPayload())
            ->assertRedirect('/repuestos/crear')
            ->assertSessionHas('status');

        $this->assertDatabaseHas('repuestos', [
            'proveedor_id' => $this->proveedor->id,
            'codigo' => 'REP-FIL-002',
            'nombre' => 'Filtro de aire',
            'descripcion' => 'Filtro de aire compatible con motores seleccionados.',
            'marca' => 'Bosch',
            'original' => true,
            'precio_venta' => '24500.00',
            'stock' => 6,
            'activo' => true,
        ]);
    }

    public function test_duplicate_code_is_rejected_without_creating_another_spare_part(): void
    {
        Repuesto::query()->create([
            'codigo' => 'REP-FIL-002',
            'nombre' => 'Filtro existente',
            'marca' => 'Bosch',
            'original' => true,
            'precio_venta' => '20000.00',
            'stock' => 2,
            'activo' => true,
        ]);

        $this->actingAs($this->responsableRepuestos)
            ->from('/repuestos/crear')
            ->post('/repuestos', [...$this->validPayload(), 'codigo' => ' rep-fil-002 '])
            ->assertRedirect('/repuestos/crear')
            ->assertSessionHasErrors('codigo');

        $this->assertDatabaseCount('repuestos', 1);
    }

    public function test_invalid_or_incomplete_data_does_not_create_a_spare_part(): void
    {
        $this->actingAs($this->responsableRepuestos)
            ->from('/repuestos/crear')
            ->post('/repuestos', [
                'codigo' => '',
                'nombre' => '',
                'descripcion' => '',
                'marca' => '',
                'tipo' => 'desconocido',
                'proveedor_id' => 999999,
                'precio_venta' => -1,
                'stock' => -1,
            ])
            ->assertRedirect('/repuestos/crear')
            ->assertSessionHasErrors([
                'codigo',
                'nombre',
                'descripcion',
                'marca',
                'tipo',
                'proveedor_id',
                'precio_venta',
                'stock',
            ]);

        $this->assertDatabaseCount('repuestos', 0);
    }

    public function test_inactive_provider_cannot_be_assigned(): void
    {
        $this->proveedor->update(['activo' => false]);

        $this->actingAs($this->responsableRepuestos)
            ->from('/repuestos/crear')
            ->post('/repuestos', $this->validPayload())
            ->assertRedirect('/repuestos/crear')
            ->assertSessionHasErrors('proveedor_id');

        $this->assertDatabaseCount('repuestos', 0);
    }

    /** @return array<string, mixed> */
    private function validPayload(): array
    {
        return [
            'codigo' => 'rep-fil-002',
            'nombre' => 'Filtro de aire',
            'descripcion' => 'Filtro de aire compatible con motores seleccionados.',
            'marca' => 'Bosch',
            'tipo' => 'original',
            'proveedor_id' => $this->proveedor->id,
            'precio_venta' => '24500.00',
            'stock' => 6,
        ];
    }
}
