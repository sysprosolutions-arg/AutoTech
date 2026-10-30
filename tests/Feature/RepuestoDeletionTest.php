<?php

namespace Tests\Feature;

use App\Models\Repuesto;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RepuestoDeletionTest extends TestCase
{
    use RefreshDatabase;

    private User $responsable;

    private Repuesto $repuesto;

    protected function setUp(): void
    {
        parent::setUp();
        $this->responsable = User::factory()->create();
        $role = Role::query()->create(['name' => 'repuestos', 'display_name' => 'Responsable de Repuestos']);
        $this->responsable->roles()->attach($role);
        $this->repuesto = Repuesto::query()->create(['codigo' => 'REP-BAJA-001', 'nombre' => 'Repuesto sin uso', 'marca' => 'Bosch', 'original' => false, 'precio_venta' => 1000, 'stock' => 0, 'activo' => true]);
    }

    public function test_guest_and_unauthorized_user_cannot_delete_a_spare_part(): void
    {
        $this->delete("/repuestos/{$this->repuesto->id}", ['confirmar' => '1'])->assertRedirect('/login');
        $this->actingAs(User::factory()->create())->delete("/repuestos/{$this->repuesto->id}", ['confirmar' => '1'])->assertForbidden();
        $this->assertTrue($this->repuesto->fresh()->activo);
    }

    public function test_responsible_user_can_logically_delete_an_eligible_spare_part(): void
    {
        $this->actingAs($this->responsable)->delete("/repuestos/{$this->repuesto->id}", ['confirmar' => '1'])->assertRedirect('/repuestos')->assertSessionHas('status');
        $this->assertFalse($this->repuesto->fresh()->activo);
        $this->assertDatabaseHas('repuestos', ['id' => $this->repuesto->id, 'activo' => false]);
    }

    public function test_confirmation_is_required(): void
    {
        $this->actingAs($this->responsable)->from("/repuestos/{$this->repuesto->id}")->delete("/repuestos/{$this->repuesto->id}")->assertSessionHasErrors('confirmar');
        $this->assertTrue($this->repuesto->fresh()->activo);
    }

    public function test_spare_part_with_stock_cannot_be_deleted(): void
    {
        $this->repuesto->update(['stock' => 2]);
        $this->actingAs($this->responsable)->from("/repuestos/{$this->repuesto->id}")->delete("/repuestos/{$this->repuesto->id}", ['confirmar' => '1'])->assertSessionHasErrors('repuesto');
        $this->assertTrue($this->repuesto->fresh()->activo);
    }

    public function test_inactive_spare_part_cannot_be_deleted_again(): void
    {
        $this->repuesto->update(['activo' => false]);
        $this->actingAs($this->responsable)->from("/repuestos/{$this->repuesto->id}")->delete("/repuestos/{$this->repuesto->id}", ['confirmar' => '1'])->assertSessionHasErrors('repuesto');
        $this->assertFalse($this->repuesto->fresh()->activo);
    }
}
