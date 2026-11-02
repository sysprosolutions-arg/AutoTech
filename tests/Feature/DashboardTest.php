<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_from_protected_dashboard(): void
    {
        $this->get('/dashboard')->assertRedirect('/login');
    }

    public function test_authenticated_user_can_view_dashboard_and_role(): void
    {
        $user = User::factory()->create();
        $role = Role::query()->create([
            'name' => 'administrador',
            'display_name' => 'Administrador',
            'description' => 'Acceso completo.',
        ]);
        $user->roles()->attach($role);

        $this->actingAs($user)
            ->get('/dashboard')
            ->assertOk()
            ->assertSee('Panel general')
            ->assertSee('Administrador')
            ->assertSee('Proveedores')
            ->assertSee('AUTOTECH')
            ->assertSee('TELEMETRÍA')
            ->assertSee('MAPA DEL CIRCUITO')
            ->assertDontSee('SAKURA')
            ->assertDontSee('Incremento técnico inicial')
            ->assertDontSee('Base técnica · v0.1.0')
            ->assertDontSee('Mapa funcional');
    }

    public function test_authenticated_user_can_compare_twenty_seven_dashboard_concepts(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get('/maquetas/panel')
            ->assertOk()
            ->assertSee('Veintisiete direcciones visuales')
            ->assertSee('Torre de control')
            ->assertSee('Plano de taller')
            ->assertSee('Atardecer mecánico')
            ->assertSee('Ruta nocturna')
            ->assertSee('Libro de taller')
            ->assertSee('Aurora')
            ->assertSee('Atelier blanco')
            ->assertSee('Pit Lane')
            ->assertSee('Jardín de servicio')
            ->assertSee('Geometría pop')
            ->assertSee('Cielo abierto')
            ->assertSee('Sistema suizo')
            ->assertSee('Pole Position')
            ->assertSee('Mónaco Data Club')
            ->assertSee('Aero Pulse')
            ->assertSee('Grand Prix Atelier')
            ->assertSee('Riviera Grid')
            ->assertSee('Parc Fermé')
            ->assertSee('Monza Pit Wall')
            ->assertSee('Silverstone Command')
            ->assertSee('Suzuka Flow')
            ->assertSee('Suzuka Sakura en Vivo')
            ->assertSee('Monaco Harbour Live')
            ->assertSee('Interlagos Pulse')
            ->assertSee('Spa Rain Radar')
            ->assertSee('Zandvoort Dune Live')
            ->assertSee('Singapore Night Grid');

        foreach (['control-tower', 'blueprint', 'sunset', 'night-drive', 'ledger', 'aurora', 'white-atelier', 'pit-lane', 'garden', 'bauhaus', 'open-sky', 'swiss-grid', 'pole-position', 'monaco-telemetry', 'aero-pulse', 'grand-prix-atelier', 'riviera-grid', 'parc-ferme', 'monza-pit-wall', 'silverstone-command', 'suzuka-flow', 'suzuka-sakura-live', 'monaco-harbour-live', 'interlagos-pulse', 'spa-rain-radar', 'zandvoort-dune-live', 'singapore-night-grid'] as $concept) {
            $this->actingAs($user)
                ->get("/maquetas/panel/{$concept}")
                ->assertOk()
                ->assertSee('Datos ilustrativos');
        }
    }
}
