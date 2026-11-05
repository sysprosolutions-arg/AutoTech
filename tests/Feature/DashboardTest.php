<?php

namespace Tests\Feature;

use App\Models\Cliente;
use App\Models\Empleado;
use App\Models\Especialidad;
use App\Models\EstadoEmpleado;
use App\Models\EstadoOrdenTrabajo;
use App\Models\EstadoReparacion;
use App\Models\EstadoTurno;
use App\Models\OrdenTrabajo;
use App\Models\Presupuesto;
use App\Models\Reparacion;
use App\Models\Role;
use App\Models\TipoReparacion;
use App\Models\Turno;
use App\Models\User;
use App\Models\Vehiculo;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        CarbonImmutable::setTestNow();

        parent::tearDown();
    }

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

    public function test_dashboard_does_not_render_fake_vehicle_markers(): void
    {
        $user = $this->createUserWithRole('administrador');

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertOk()->assertDontSee('data-map-vehicle=', false);
        $this->assertSame(0, substr_count($response->getContent(), 'data-map-vehicle='));
    }

    public function test_dashboard_renders_the_same_real_active_vehicles_on_map_and_telemetry(): void
    {
        $user = $this->createUserWithRole('administrador');
        $this->createActiveRepair($user, 'AB123CD', 1, 90);
        $this->createActiveRepair($user, 'AE321JK', 2, 150);

        $response = $this->actingAs($user)->get('/dashboard');

        $response
            ->assertOk()
            ->assertSee('data-map-vehicle="AB123CD"', false)
            ->assertSee('data-map-vehicle="AE321JK"', false)
            ->assertSee('02/06 ACTIVOS')
            ->assertSee('ubicación visual, no representa una etapa');
        $this->assertSame(2, substr_count($response->getContent(), 'data-map-vehicle='));
        $this->assertSame(4, substr_count($response->getContent(), '<b>DISPONIBLE</b>'));
    }

    public function test_dashboard_only_shows_future_non_terminal_appointments(): void
    {
        CarbonImmutable::setTestNow('2026-11-04 12:00:00');
        $user = $this->createUserWithRole('administrador');
        $pending = EstadoTurno::query()->create(['codigo' => 'pendiente', 'nombre' => 'Pendiente']);
        $attended = EstadoTurno::query()->create(['codigo' => 'atendido', 'nombre' => 'Atendido']);

        $this->createAppointment($user, 'PAST001', $pending, '2026-11-04', '10:00:00');
        $this->createAppointment($user, 'DONE001', $attended, '2026-11-04', '16:00:00');
        $this->createAppointment($user, 'NEXT001', $pending, '2026-11-04', '15:00:00');
        $this->createAppointment($user, 'NEXT002', $pending, '2026-11-05', '09:00:00');

        $this->actingAs($user)
            ->get('/dashboard')
            ->assertOk()
            ->assertSee('NEXT001')
            ->assertSee('NEXT002')
            ->assertDontSee('PAST001')
            ->assertDontSee('DONE001')
            ->assertSee('data-capacity-date="2026-11-04"', false)
            ->assertDontSee('data-capacity-date="2026-10-30"', false);
    }

    public function test_dashboard_hides_turnos_links_from_unauthorized_roles(): void
    {
        $user = $this->createUserWithRole('reparacion');

        $this->actingAs($user)
            ->get('/dashboard')
            ->assertOk()
            ->assertSee('AGENDA RESTRINGIDA')
            ->assertDontSee('href="'.route('turnos.index').'"', false);
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

    private function createUserWithRole(string $roleName): User
    {
        $user = User::factory()->create();
        $role = Role::query()->firstOrCreate(
            ['name' => $roleName],
            ['display_name' => ucfirst($roleName), 'description' => 'Rol para pruebas del panel.'],
        );
        $user->roles()->attach($role);

        return $user;
    }

    private function createAppointment(User $user, string $patent, EstadoTurno $state, string $date, string $time): Turno
    {
        $client = Cliente::query()->create([
            'dni' => 'DNI-'.$patent,
            'nombre' => 'Cliente',
            'apellido' => $patent,
            'telefono' => '1111111111',
            'email' => strtolower($patent).'@example.test',
            'activo' => true,
        ]);
        $vehicle = Vehiculo::query()->create([
            'cliente_id' => $client->id,
            'patente' => $patent,
            'marca' => 'Toyota',
            'modelo' => 'Corolla',
            'anio' => 2024,
            'activo' => true,
        ]);

        return Turno::query()->create([
            'cliente_id' => $client->id,
            'vehiculo_id' => $vehicle->id,
            'estado_turno_id' => $state->id,
            'registrado_por' => $user->id,
            'fecha' => $date,
            'hora' => $time,
            'motivo' => 'Control de '.$patent,
        ]);
    }

    private function createActiveRepair(User $user, string $patent, int $sequence, int $minutes): Reparacion
    {
        $appointmentState = EstadoTurno::query()->firstOrCreate(['codigo' => 'atendido'], ['nombre' => 'Atendido']);
        $appointment = $this->createAppointment($user, $patent, $appointmentState, '2026-11-04', '09:00:00');
        $specialty = Especialidad::query()->firstOrCreate(['codigo' => 'mecanica'], ['nombre' => 'Mecánica general']);
        $employeeState = EstadoEmpleado::query()->firstOrCreate(['codigo' => 'activo'], ['nombre' => 'Activo']);
        $employee = Empleado::query()->create([
            'legajo' => 'MEC-'.$sequence,
            'nombre' => 'Mecánico',
            'apellido' => (string) $sequence,
            'especialidad_id' => $specialty->id,
            'estado_empleado_id' => $employeeState->id,
        ]);
        $budget = Presupuesto::query()->create([
            'numero' => 'PRE-'.str_pad((string) $sequence, 6, '0', STR_PAD_LEFT),
            'turno_id' => $appointment->id,
            'registrado_por' => $user->id,
            'fecha_emision' => '2026-11-04',
            'valido_hasta' => '2026-11-14',
            'total' => 100000,
        ]);
        $workOrderState = EstadoOrdenTrabajo::query()->firstOrCreate(['codigo' => 'en-proceso'], ['nombre' => 'En proceso']);
        $workOrder = OrdenTrabajo::query()->create([
            'numero' => 'OT-'.str_pad((string) $sequence, 6, '0', STR_PAD_LEFT),
            'presupuesto_id' => $budget->id,
            'turno_id' => $appointment->id,
            'vehiculo_id' => $appointment->vehiculo_id,
            'empleado_id' => $employee->id,
            'estado_orden_trabajo_id' => $workOrderState->id,
            'emitida_por' => $user->id,
            'fecha_emision' => '2026-11-04 09:00:00',
        ]);
        $repairType = TipoReparacion::query()->firstOrCreate(
            ['codigo' => 'mantenimiento'],
            ['nombre' => 'Mantenimiento', 'activo' => true],
        );
        $repairState = EstadoReparacion::query()->firstOrCreate(['codigo' => 'en-proceso'], ['nombre' => 'En proceso']);

        return Reparacion::query()->create([
            'numero' => 'REP-'.str_pad((string) $sequence, 6, '0', STR_PAD_LEFT),
            'orden_trabajo_id' => $workOrder->id,
            'vehiculo_id' => $appointment->vehiculo_id,
            'empleado_id' => $employee->id,
            'tipo_reparacion_id' => $repairType->id,
            'estado_reparacion_id' => $repairState->id,
            'registrado_por' => $user->id,
            'fecha_inicio' => '2026-11-04 09:00:00',
            'tiempo_empleado_minutos' => $minutes,
            'trabajos_realizados' => 'Trabajo de prueba para el panel.',
        ]);
    }
}
