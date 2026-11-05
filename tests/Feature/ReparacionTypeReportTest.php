<?php

namespace Tests\Feature;

use App\Models\User;
use Carbon\CarbonImmutable;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReparacionTypeReportTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        CarbonImmutable::setTestNow();

        parent::tearDown();
    }

    public function test_authorized_user_can_generate_repair_counts_grouped_by_type(): void
    {
        CarbonImmutable::setTestNow('2026-11-04 14:00:00');
        $this->seed(DatabaseSeeder::class);
        $administrator = User::query()->where('username', 'admin')->firstOrFail();

        $this->actingAs($administrator)
            ->get(route('reparaciones.reports.types', [
                'desde' => '2026-10-01',
                'hasta' => '2026-11-30',
            ]))
            ->assertOk()
            ->assertSee('Reparaciones por tipo')
            ->assertSee('Total reparaciones')
            ->assertSee('5')
            ->assertSee('Mantenimiento preventivo')
            ->assertSee('Diagnóstico')
            ->assertSee('Valores exactos')
            ->assertSee('Imprimir');
    }

    public function test_report_without_repairs_shows_a_clear_empty_state(): void
    {
        CarbonImmutable::setTestNow('2026-11-04 14:00:00');
        $this->seed(DatabaseSeeder::class);
        $administrator = User::query()->where('username', 'admin')->firstOrFail();

        $this->actingAs($administrator)
            ->get(route('reparaciones.reports.types', [
                'desde' => '2020-01-01',
                'hasta' => '2020-01-31',
            ]))
            ->assertOk()
            ->assertSee('No existen reparaciones en el período seleccionado')
            ->assertDontSee('Valores exactos');
    }

    public function test_report_rejects_an_invalid_period(): void
    {
        CarbonImmutable::setTestNow('2026-11-04 14:00:00');
        $this->seed(DatabaseSeeder::class);
        $administrator = User::query()->where('username', 'admin')->firstOrFail();

        $this->actingAs($administrator)
            ->from(route('reparaciones.reports.types'))
            ->get(route('reparaciones.reports.types', [
                'desde' => '2026-11-30',
                'hasta' => '2026-11-01',
            ]))
            ->assertRedirect(route('reparaciones.reports.types'))
            ->assertSessionHasErrors('hasta');
    }

    public function test_report_requires_authentication_and_repair_role(): void
    {
        $this->get(route('reparaciones.reports.types'))->assertRedirect('/login');

        $this->actingAs(User::factory()->create())
            ->get(route('reparaciones.reports.types'))
            ->assertForbidden();
    }
}
