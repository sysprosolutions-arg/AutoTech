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
            ->assertSee('ATSA-5');
    }
}
