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
            ->assertDontSee('Incremento técnico inicial')
            ->assertDontSee('Base técnica · v0.1.0')
            ->assertDontSee('Mapa funcional');
    }

    public function test_authenticated_user_can_compare_six_dashboard_concepts(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get('/maquetas/panel')
            ->assertOk()
            ->assertSee('Seis direcciones visuales')
            ->assertSee('Torre de control')
            ->assertSee('Plano de taller')
            ->assertSee('Atardecer mecánico')
            ->assertSee('Ruta nocturna')
            ->assertSee('Libro de taller')
            ->assertSee('Aurora');

        foreach (['control-tower', 'blueprint', 'sunset', 'night-drive', 'ledger', 'aurora'] as $concept) {
            $this->actingAs($user)
                ->get("/maquetas/panel/{$concept}")
                ->assertOk()
                ->assertSee('Datos ilustrativos');
        }
    }
}
