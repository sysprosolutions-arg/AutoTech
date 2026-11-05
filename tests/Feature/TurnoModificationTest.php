<?php

namespace Tests\Feature;

use App\Models\Cliente;
use App\Models\EstadoTurno;
use App\Models\Role;
use App\Models\Turno;
use App\Models\User;
use App\Models\Vehiculo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TurnoModificationTest extends TestCase
{
    use RefreshDatabase;

    private User $responsableTurnos;

    private Cliente $cliente;

    private Vehiculo $vehiculo;

    private EstadoTurno $pendiente;

    private EstadoTurno $confirmado;

    private EstadoTurno $atendido;

    private EstadoTurno $cancelado;

    protected function setUp(): void
    {
        parent::setUp();

        $this->responsableTurnos = User::factory()->create();
        $role = Role::query()->create(['name' => 'turnos', 'display_name' => 'Responsable de Turnos']);
        $this->responsableTurnos->roles()->attach($role);

        $this->cliente = Cliente::query()->create([
            'dni' => '30111222',
            'nombre' => 'Ana',
            'apellido' => 'Martínez',
            'telefono' => '1141237788',
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

        $this->pendiente = EstadoTurno::query()->create(['codigo' => 'pendiente', 'nombre' => 'Pendiente']);
        $this->confirmado = EstadoTurno::query()->create(['codigo' => 'confirmado', 'nombre' => 'Confirmado']);
        $this->atendido = EstadoTurno::query()->create(['codigo' => 'atendido', 'nombre' => 'Atendido']);
        $this->cancelado = EstadoTurno::query()->create(['codigo' => 'cancelado', 'nombre' => 'Cancelado']);
    }

    public function test_responsible_user_can_open_the_appointment_edit_form(): void
    {
        $turno = $this->createAppointment();

        $this->actingAs($this->responsableTurnos)
            ->get(route('turnos.edit', $turno))
            ->assertOk()
            ->assertSee("Modificar turno #{$turno->id}")
            ->assertSee('Ana')
            ->assertSee('AB123CD')
            ->assertSee('Confirmado')
            ->assertSee('Cancelado');
    }

    public function test_responsible_user_can_modify_an_appointment_and_confirm_it(): void
    {
        $turno = $this->createAppointment();

        $this->actingAs($this->responsableTurnos)
            ->put(route('turnos.update', $turno), [
                'cliente_id' => $this->cliente->id,
                'vehiculo_id' => $this->vehiculo->id,
                'estado_turno_id' => $this->confirmado->id,
                'fecha' => now()->addDays(3)->toDateString(),
                'hora' => '11:30',
                'motivo' => 'Diagnóstico de frenos actualizado.',
                'observaciones' => 'El cliente confirmó la asistencia.',
            ])
            ->assertRedirect(route('turnos.show', $turno))
            ->assertSessionHas('status');

        $this->assertDatabaseHas('turnos', [
            'id' => $turno->id,
            'estado_turno_id' => $this->confirmado->id,
            'hora' => '11:30',
            'motivo' => 'Diagnóstico de frenos actualizado.',
        ]);
    }

    public function test_invalid_state_transition_is_rejected_without_changing_the_appointment(): void
    {
        $turno = $this->createAppointment();

        $this->actingAs($this->responsableTurnos)
            ->from(route('turnos.edit', $turno))
            ->put(route('turnos.update', $turno), [
                'cliente_id' => $this->cliente->id,
                'vehiculo_id' => $this->vehiculo->id,
                'estado_turno_id' => $this->atendido->id,
                'fecha' => now()->addDay()->toDateString(),
                'hora' => '10:00',
                'motivo' => 'Cambio no permitido.',
            ])
            ->assertRedirect(route('turnos.edit', $turno))
            ->assertSessionHasErrors('estado_turno_id');

        $this->assertDatabaseHas('turnos', [
            'id' => $turno->id,
            'estado_turno_id' => $this->pendiente->id,
            'motivo' => 'Service programado.',
        ]);
    }

    public function test_incomplete_data_is_rejected_without_partial_changes(): void
    {
        $turno = $this->createAppointment();

        $this->actingAs($this->responsableTurnos)
            ->from(route('turnos.edit', $turno))
            ->put(route('turnos.update', $turno), [
                'cliente_id' => $this->cliente->id,
                'vehiculo_id' => $this->vehiculo->id,
                'estado_turno_id' => $this->confirmado->id,
                'fecha' => now()->subDay()->toDateString(),
                'hora' => '18:00',
                'motivo' => '',
            ])
            ->assertRedirect(route('turnos.edit', $turno))
            ->assertSessionHasErrors(['fecha', 'hora', 'motivo']);

        $this->assertDatabaseHas('turnos', [
            'id' => $turno->id,
            'estado_turno_id' => $this->pendiente->id,
            'hora' => '09:30',
            'motivo' => 'Service programado.',
        ]);
    }

    public function test_finished_or_cancelled_appointments_cannot_be_edited(): void
    {
        foreach ([$this->atendido, $this->cancelado] as $state) {
            $turno = $this->createAppointment(['estado_turno_id' => $state->id]);

            $this->actingAs($this->responsableTurnos)
                ->get(route('turnos.edit', $turno))
                ->assertRedirect(route('turnos.show', $turno))
                ->assertSessionHas('error');
        }
    }

    public function test_finished_appointment_cannot_be_modified_through_a_direct_request(): void
    {
        $turno = $this->createAppointment(['estado_turno_id' => $this->atendido->id]);

        $this->actingAs($this->responsableTurnos)
            ->from(route('turnos.show', $turno))
            ->put(route('turnos.update', $turno), [
                'cliente_id' => $this->cliente->id,
                'vehiculo_id' => $this->vehiculo->id,
                'estado_turno_id' => $this->atendido->id,
                'fecha' => now()->addDay()->toDateString(),
                'hora' => '10:00',
                'motivo' => 'Intento de modificación directa.',
            ])
            ->assertRedirect(route('turnos.show', $turno))
            ->assertSessionHasErrors('estado_turno_id');

        $this->assertDatabaseHas('turnos', [
            'id' => $turno->id,
            'motivo' => 'Service programado.',
        ]);
    }

    public function test_unauthorized_users_cannot_modify_appointments(): void
    {
        $turno = $this->createAppointment();

        $this->put(route('turnos.update', $turno), [])->assertRedirect('/login');
        $this->actingAs(User::factory()->create())
            ->put(route('turnos.update', $turno), [])
            ->assertForbidden();
    }

    /** @param array<string, mixed> $overrides */
    private function createAppointment(array $overrides = []): Turno
    {
        return Turno::query()->create([
            'cliente_id' => $this->cliente->id,
            'vehiculo_id' => $this->vehiculo->id,
            'estado_turno_id' => $this->pendiente->id,
            'registrado_por' => $this->responsableTurnos->id,
            'fecha' => now()->addDay()->toDateString(),
            'hora' => '09:30',
            'motivo' => 'Service programado.',
            'observaciones' => null,
            ...$overrides,
        ]);
    }
}
