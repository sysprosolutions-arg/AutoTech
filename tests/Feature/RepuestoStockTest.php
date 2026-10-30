<?php

namespace Tests\Feature;

use App\Models\Repuesto;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RepuestoStockTest extends TestCase
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
        $this->repuesto = Repuesto::query()->create(['codigo' => 'REP-001', 'nombre' => 'Filtro', 'marca' => 'Bosch', 'original' => false, 'precio_venta' => 1000, 'stock' => 10, 'activo' => true]);
    }

    public function test_guest_and_unauthorized_user_cannot_update_stock(): void
    {
        $this->get("/repuestos/{$this->repuesto->id}/stock")->assertRedirect('/login');
        $this->actingAs(User::factory()->create())->post("/repuestos/{$this->repuesto->id}/stock", $this->payload('entrada', 2))->assertForbidden();
    }

    public function test_responsible_user_can_register_stock_entry(): void
    {
        $this->actingAs($this->responsable)->post("/repuestos/{$this->repuesto->id}/stock", $this->payload('entrada', 5))->assertRedirect("/repuestos/{$this->repuesto->id}/stock");
        $this->assertSame(15, $this->repuesto->fresh()->stock);
        $this->assertDatabaseHas('movimientos_stock', ['repuesto_id' => $this->repuesto->id, 'operacion' => 'entrada', 'cantidad' => 5, 'stock_anterior' => 10, 'stock_resultante' => 15]);
    }

    public function test_responsible_user_can_register_stock_exit(): void
    {
        $this->actingAs($this->responsable)->post("/repuestos/{$this->repuesto->id}/stock", $this->payload('salida', 4));
        $this->assertSame(6, $this->repuesto->fresh()->stock);
    }

    public function test_exit_cannot_produce_negative_stock(): void
    {
        $this->actingAs($this->responsable)->from("/repuestos/{$this->repuesto->id}/stock")->post("/repuestos/{$this->repuesto->id}/stock", $this->payload('salida', 11))->assertSessionHasErrors('cantidad');
        $this->assertSame(10, $this->repuesto->fresh()->stock);
        $this->assertDatabaseCount('movimientos_stock', 0);
    }

    public function test_invalid_movement_preserves_previous_stock(): void
    {
        $this->actingAs($this->responsable)->post("/repuestos/{$this->repuesto->id}/stock", $this->payload('entrada', 0))->assertSessionHasErrors(['cantidad']);
        $this->assertSame(10, $this->repuesto->fresh()->stock);
    }

    /** @return array<string, mixed> */
    private function payload(string $operacion, int $cantidad): array
    {
        return ['operacion' => $operacion, 'cantidad' => $cantidad, 'motivo' => 'Ajuste de inventario físico.'];
    }
}
