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

class TurnoConsultationTest extends TestCase
{
    use RefreshDatabase;

    private User $responsableTurnos;

    private Cliente $cliente;

    private Vehiculo $vehiculo;

    private EstadoTurno $estadoPendiente;

    protected function setUp(): void
    {
        parent::setUp();

        $this->responsableTurnos = User::factory()->create(['name' => 'Responsable Agenda']);
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
            'color' => 'Gris',
            'activo' => true,
        ]);

        $this->estadoPendiente = EstadoTurno::query()->create([
            'codigo' => 'pendiente',
            'nombre' => 'Pendiente',
        ]);
    }

    public function test_guest_cannot_access_the_appointment_list(): void
    {
        $this->get('/turnos')->assertRedirect('/login');
    }

    public function test_user_without_an_allowed_role_cannot_access_the_appointment_list(): void
    {
        $this->actingAs(User::factory()->create())
            ->get('/turnos')
            ->assertForbidden();
    }

    public function test_responsible_user_can_list_registered_appointments(): void
    {
        $turno = $this->createAppointment();

        $this->actingAs($this->responsableTurnos)
            ->get('/turnos')
            ->assertOk()
            ->assertSee("#{$turno->id}")
            ->assertSee('Martínez, Ana')
            ->assertSee('AB123CD')
            ->assertSee('Pendiente');
    }

    public function test_appointments_can_be_searched_by_identifier_client_and_vehicle(): void
    {
        $turno = $this->createAppointment();

        foreach (["{$turno->id}", 'Martínez', '30111222', 'AB123CD'] as $search) {
            $this->actingAs($this->responsableTurnos)
                ->get('/turnos?'.http_build_query(['buscar' => $search]))
                ->assertOk()
                ->assertSee("#{$turno->id}")
                ->assertSee('AB123CD');
        }
    }

    public function test_appointments_can_be_filtered_by_state_and_date(): void
    {
        $turno = $this->createAppointment(['fecha' => '2026-11-03']);
        $estadoCancelado = EstadoTurno::query()->create([
            'codigo' => 'cancelado',
            'nombre' => 'Cancelado',
        ]);
        $otroTurno = $this->createAppointment([
            'estado_turno_id' => $estadoCancelado->id,
            'fecha' => '2026-11-04',
            'hora' => '15:00',
        ]);

        $this->actingAs($this->responsableTurnos)
            ->get('/turnos?'.http_build_query([
                'estado' => 'pendiente',
                'fecha' => '2026-11-03',
            ]))
            ->assertOk()
            ->assertSee("#{$turno->id}")
            ->assertDontSee("#{$otroTurno->id}");
    }

    public function test_empty_search_result_is_reported_clearly(): void
    {
        $this->createAppointment();

        $this->actingAs($this->responsableTurnos)
            ->get('/turnos?buscar=ZZ999ZZ')
            ->assertOk()
            ->assertSee('No se encontraron turnos');
    }

    public function test_responsible_user_can_view_an_appointment_detail(): void
    {
        $turno = $this->createAppointment();

        $this->actingAs($this->responsableTurnos)
            ->get("/turnos/{$turno->id}")
            ->assertOk()
            ->assertSee("Turno #{$turno->id}")
            ->assertSee('Ana Martínez')
            ->assertSee('AB123CD')
            ->assertSee('03/11/2026')
            ->assertSee('09:30 h')
            ->assertSee('Ruido en el tren delantero.')
            ->assertSee('Responsable Agenda');
    }

    /**
     * Create an appointment with valid related master data.
     *
     * @param  array<string, mixed>  $overrides
     */
    private function createAppointment(array $overrides = []): Turno
    {
        return Turno::query()->create([
            'cliente_id' => $this->cliente->id,
            'vehiculo_id' => $this->vehiculo->id,
            'estado_turno_id' => $this->estadoPendiente->id,
            'registrado_por' => $this->responsableTurnos->id,
            'fecha' => '2026-11-03',
            'hora' => '09:30',
            'motivo' => 'Ruido en el tren delantero.',
            'observaciones' => 'El ruido aumenta al doblar.',
            ...$overrides,
        ]);
    }
}
