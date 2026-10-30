<?php

namespace Tests\Feature;

use App\Models\Repuesto;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RepuestoBrandReportTest extends TestCase
{
    use RefreshDatabase;

    private User $responsable;

    protected function setUp(): void
    {
        parent::setUp();
        $this->responsable = User::factory()->create();
        $role = Role::query()->create(['name' => 'repuestos', 'display_name' => 'Responsable de Repuestos']);
        $this->responsable->roles()->attach($role);
        $this->createPart('REP-BOS-001', 'Pastillas', 'Bosch');
        $this->createPart('REP-BOS-002', 'Bujía', 'Bosch');
        $this->createPart('REP-TOY-001', 'Filtro', 'Toyota');
    }

    public function test_guest_and_unauthorized_user_cannot_access_brand_report(): void
    {
        $this->get('/repuestos/reportes/marcas')->assertRedirect('/login');
        $this->actingAs(User::factory()->create())->get('/repuestos/reportes/marcas')->assertForbidden();
    }

    public function test_report_groups_all_active_spare_parts_by_brand(): void
    {
        $this->actingAs($this->responsable)->get('/repuestos/reportes/marcas')->assertOk()->assertSee('Bosch')->assertSee('Toyota')->assertSee('REP-BOS-001')->assertSee('REP-TOY-001');
    }

    public function test_report_filters_one_brand_without_including_others(): void
    {
        $this->actingAs($this->responsable)->get('/repuestos/reportes/marcas?marca=Bosch')->assertOk()->assertSee('REP-BOS-001')->assertSee('REP-BOS-002')->assertDontSeeText('REP-TOY-001');
    }

    public function test_report_explains_when_selected_brand_has_no_results(): void
    {
        $this->actingAs($this->responsable)->get('/repuestos/reportes/marcas?marca=Inexistente')->assertOk()->assertSee('No hay repuestos para la marca seleccionada')->assertDontSeeText('REP-BOS-001');
    }

    private function createPart(string $codigo, string $nombre, string $marca): void
    {
        Repuesto::query()->create(['codigo' => $codigo, 'nombre' => $nombre, 'marca' => $marca, 'original' => false, 'precio_venta' => 1000, 'stock' => 2, 'activo' => true]);
    }
}
