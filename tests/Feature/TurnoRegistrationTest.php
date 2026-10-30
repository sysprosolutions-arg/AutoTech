<?php

namespace Tests\Feature;

use App\Models\Cliente;
use App\Models\EstadoTurno;
use App\Models\Role;
use App\Models\User;
use App\Models\Vehiculo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TurnoRegistrationTest extends TestCase
{
    use RefreshDatabase;

    private User $responsableTurnos;

    private Cliente $cliente;

    private Vehiculo $vehiculo;

    private EstadoTurno $estadoPendiente;

    protected function setUp(): void
    {
        parent::setUp();

        $this->responsableTurnos = User::factory()->create();
        $role = Role::query()->create([
            'name' => 'turnos',
            'display_name' => 'Responsable de Turnos',
        ]);
        $this->responsableTurnos->roles()->attach($role);

        $this->cliente = Cliente::query()->create([
            'dni' => '30111222',
            'nombre' => 'Ana',
            'apellido' => 'Martínez',
            'telefono' => '11 4123-7788',
            'email' => 'ana@example.com',
            'activo' => true,
        ]);

        $this->vehiculo = Vehiculo::query()->create([
            'cliente_id' => $this->cliente->id,
            'patente' => 'AB123CD',
            'marca' => 'Toyota',
            'modelo' => 'Corolla',
            'anio' => 2019,
            'activo' => true,
        ]);

        $this->estadoPendiente = EstadoTurno::query()->create([
            'codigo' => 'pendiente',
            'nombre' => 'Pendiente',
        ]);
    }

    public function test_guest_cannot_access_the_appointment_form(): void
    {
        $this->get('/turnos/crear')->assertRedirect('/login');
    }

    public function test_user_without_an_allowed_role_cannot_access_the_appointment_form(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get('/turnos/crear')
            ->assertForbidden();
    }

    public function test_responsible_user_can_view_the_appointment_form(): void
    {
        $this->actingAs($this->responsableTurnos)
            ->get('/turnos/crear')
            ->assertOk()
            ->assertSee('Registrar turno')
            ->assertSee('Ana')
            ->assertSee('AB123CD')
            ->assertSee('Horario de atención: 08:00 a 17:00, en intervalos de 30 minutos.')
            ->assertSee('value="08:00"', false)
            ->assertSee('value="17:00"', false);
    }

    public function test_responsible_user_can_register_an_appointment(): void
    {
        $response = $this->actingAs($this->responsableTurnos)
            ->post('/turnos', [
                'cliente_id' => $this->cliente->id,
                'vehiculo_id' => $this->vehiculo->id,
                'fecha' => now()->addDay()->toDateString(),
                'hora' => '09:30',
                'motivo' => 'Ruido en el tren delantero.',
                'observaciones' => 'El ruido aumenta al doblar.',
            ]);

        $response->assertRedirect('/turnos/crear');
        $response->assertSessionHas('status');

        $this->assertDatabaseHas('turnos', [
            'cliente_id' => $this->cliente->id,
            'vehiculo_id' => $this->vehiculo->id,
            'estado_turno_id' => $this->estadoPendiente->id,
            'registrado_por' => $this->responsableTurnos->id,
            'hora' => '09:30',
            'motivo' => 'Ruido en el tren delantero.',
        ]);
    }

    public function test_vehicle_must_belong_to_the_selected_client(): void
    {
        $otroCliente = Cliente::query()->create([
            'dni' => '28777666',
            'nombre' => 'Carlos',
            'apellido' => 'Ramírez',
            'telefono' => '11 4988-2301',
            'activo' => true,
        ]);

        $this->actingAs($this->responsableTurnos)
            ->from('/turnos/crear')
            ->post('/turnos', [
                'cliente_id' => $otroCliente->id,
                'vehiculo_id' => $this->vehiculo->id,
                'fecha' => now()->addDay()->toDateString(),
                'hora' => '09:30',
                'motivo' => 'Service programado.',
            ])
            ->assertRedirect('/turnos/crear')
            ->assertSessionHasErrors('vehiculo_id');

        $this->assertDatabaseCount('turnos', 0);
    }

    public function test_invalid_or_incomplete_data_does_not_create_an_appointment(): void
    {
        $this->actingAs($this->responsableTurnos)
            ->from('/turnos/crear')
            ->post('/turnos', [
                'cliente_id' => $this->cliente->id,
                'vehiculo_id' => $this->vehiculo->id,
                'fecha' => now()->subDay()->toDateString(),
                'hora' => 'hora-invalida',
                'motivo' => '',
            ])
            ->assertRedirect('/turnos/crear')
            ->assertSessionHasErrors(['fecha', 'hora', 'motivo']);

        $this->assertDatabaseCount('turnos', 0);
    }

    public function test_appointment_time_must_be_within_business_hours(): void
    {
        foreach (['07:59', '17:01'] as $horaFueraDeHorario) {
            $this->actingAs($this->responsableTurnos)
                ->from('/turnos/crear')
                ->post('/turnos', [
                    'cliente_id' => $this->cliente->id,
                    'vehiculo_id' => $this->vehiculo->id,
                    'fecha' => now()->addDay()->toDateString(),
                    'hora' => $horaFueraDeHorario,
                    'motivo' => 'Service programado.',
                ])
                ->assertRedirect('/turnos/crear')
                ->assertSessionHasErrors('hora');
        }

        $this->assertDatabaseCount('turnos', 0);
    }
}
