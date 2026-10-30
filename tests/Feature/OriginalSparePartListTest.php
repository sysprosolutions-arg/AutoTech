<?php

namespace Tests\Feature;

use App\Models\Repuesto;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OriginalSparePartListTest extends TestCase
{
    use RefreshDatabase;

    private User $responsable;

    protected function setUp(): void
    {
        parent::setUp();

        $this->responsable = User::factory()->create();
        $role = Role::query()->create(['name' => 'repuestos', 'display_name' => 'Responsable de Repuestos']);
        $this->responsable->roles()->attach($role);
    }

    public function test_guest_and_unauthorized_user_cannot_access_original_spare_part_list(): void
    {
        $this->get('/repuestos/listados/originales')->assertRedirect('/login');
        $this->actingAs(User::factory()->create())->get('/repuestos/listados/originales')->assertForbidden();
    }

    public function test_list_contains_only_active_original_spare_parts(): void
    {
        $this->createPart('REP-ORI-001', true, true);
        $this->createPart('REP-ALT-001', false, true);
        $this->createPart('REP-ORI-002', true, false);

        $this->actingAs($this->responsable)
            ->get('/repuestos/listados/originales')
            ->assertOk()
            ->assertSee('Repuestos originales')
            ->assertSee('REP-ORI-001')
            ->assertDontSeeText('REP-ALT-001')
            ->assertDontSeeText('REP-ORI-002');
    }

    public function test_list_explains_when_there_are_no_active_original_spare_parts(): void
    {
        $this->createPart('REP-ALT-001', false, true);

        $this->actingAs($this->responsable)
            ->get('/repuestos/listados/originales')
            ->assertOk()
            ->assertSee('No hay repuestos originales activos')
            ->assertDontSeeText('REP-ALT-001');
    }

    private function createPart(string $codigo, bool $original, bool $activo): void
    {
        Repuesto::query()->create([
            'codigo' => $codigo,
            'nombre' => 'Repuesto de prueba',
            'marca' => 'Marca de prueba',
            'original' => $original,
            'precio_venta' => 1000,
            'stock' => 2,
            'activo' => $activo,
        ]);
    }
}
