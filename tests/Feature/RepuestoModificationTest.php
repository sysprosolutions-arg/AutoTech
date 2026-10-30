<?php

namespace Tests\Feature;

use App\Models\Proveedor;
use App\Models\Repuesto;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RepuestoModificationTest extends TestCase
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
        $this->proveedor = Proveedor::query()->create(['cuit' => '30-70123456-7', 'razon_social' => 'Autopartes del Plata S.A.', 'localidad' => 'Buenos Aires', 'activo' => true]);
        $this->repuesto = Repuesto::query()->create([
            'proveedor_id' => $this->proveedor->id,
            'codigo' => 'REP-FIL-001',
            'nombre' => 'Filtro de aceite',
            'descripcion' => 'Descripción original.',
            'marca' => 'Toyota',
            'original' => true,
            'precio_venta' => '18500.00',
            'stock' => 12,
            'activo' => true,
        ]);
    }

    public function test_guest_cannot_access_spare_part_editing(): void
    {
        $this->get("/repuestos/{$this->repuesto->id}/editar")->assertRedirect('/login');
    }

    public function test_user_without_an_allowed_role_cannot_update_a_spare_part(): void
    {
        $this->actingAs(User::factory()->create())
            ->put("/repuestos/{$this->repuesto->id}", $this->validPayload())
            ->assertForbidden();

        $this->assertSame('Filtro de aceite', $this->repuesto->fresh()->nombre);
    }

    public function test_responsible_user_can_view_the_edit_form_with_current_values(): void
    {
        $this->actingAs($this->responsableRepuestos)
            ->get("/repuestos/{$this->repuesto->id}/editar")
            ->assertOk()
            ->assertSee('Modificar repuesto')
            ->assertSee('REP-FIL-001')
            ->assertSee('Filtro de aceite')
            ->assertSee('Autopartes del Plata S.A.');
    }

    public function test_responsible_user_can_update_a_spare_part(): void
    {
        $this->actingAs($this->responsableRepuestos)
            ->put("/repuestos/{$this->repuesto->id}", $this->validPayload())
            ->assertRedirect("/repuestos/{$this->repuesto->id}")
            ->assertSessionHas('status');

        $this->assertDatabaseHas('repuestos', [
            'id' => $this->repuesto->id,
            'codigo' => 'REP-FIL-001',
            'nombre' => 'Filtro premium de aceite',
            'descripcion' => 'Descripción actualizada.',
            'marca' => 'Bosch',
            'original' => false,
            'precio_venta' => '22000.00',
            'stock' => 12,
        ]);
    }

    public function test_duplicate_code_is_rejected_and_previous_values_are_preserved(): void
    {
        Repuesto::query()->create(['codigo' => 'REP-OTRO-001', 'nombre' => 'Otro repuesto', 'marca' => 'Otra marca', 'original' => false, 'precio_venta' => '1000.00', 'stock' => 1, 'activo' => true]);

        $this->actingAs($this->responsableRepuestos)
            ->from("/repuestos/{$this->repuesto->id}/editar")
            ->put("/repuestos/{$this->repuesto->id}", [...$this->validPayload(), 'codigo' => 'REP-OTRO-001'])
            ->assertSessionHasErrors('codigo');

        $this->assertSame('Filtro de aceite', $this->repuesto->fresh()->nombre);
    }

    public function test_invalid_data_is_rejected_and_previous_values_are_preserved(): void
    {
        $this->actingAs($this->responsableRepuestos)
            ->from("/repuestos/{$this->repuesto->id}/editar")
            ->put("/repuestos/{$this->repuesto->id}", [...$this->validPayload(), 'nombre' => '', 'precio_venta' => -1])
            ->assertSessionHasErrors(['nombre', 'precio_venta']);

        $this->assertDatabaseHas('repuestos', ['id' => $this->repuesto->id, 'nombre' => 'Filtro de aceite', 'precio_venta' => '18500.00']);
    }

    /** @return array<string, mixed> */
    private function validPayload(): array
    {
        return [
            'codigo' => 'REP-FIL-001',
            'nombre' => 'Filtro premium de aceite',
            'descripcion' => 'Descripción actualizada.',
            'marca' => 'Bosch',
            'tipo' => 'alternativo',
            'proveedor_id' => $this->proveedor->id,
            'precio_venta' => '22000.00',
            'stock' => 15,
        ];
    }
}
