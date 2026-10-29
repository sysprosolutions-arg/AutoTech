<?php

namespace Tests\Feature;

use App\Models\Cliente;
use App\Models\Empleado;
use App\Models\Especialidad;
use App\Models\EstadoEmpleado;
use App\Models\EstadoOrdenTrabajo;
use App\Models\EstadoTurno;
use App\Models\Presupuesto;
use App\Models\Repuesto;
use App\Models\Role;
use App\Models\TipoServicio;
use App\Models\Turno;
use App\Models\User;
use App\Models\Vehiculo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PresupuestoWorkflowTest extends TestCase
{
    use RefreshDatabase;

    private User $responsableTurnos;

    private Turno $turno;

    private TipoServicio $servicio;

    private Repuesto $repuesto;

    private Empleado $empleado;

    private EstadoTurno $estadoConfirmado;

    protected function setUp(): void
    {
        parent::setUp();

        $this->responsableTurnos = User::factory()->create(['name' => 'Responsable Presupuestos']);
        $role = Role::query()->create([
            'name' => 'turnos',
            'display_name' => 'Responsable de Turnos',
        ]);
        $this->responsableTurnos->roles()->attach($role);

        $cliente = Cliente::query()->create([
            'dni' => '30111222',
            'nombre' => 'Ana',
            'apellido' => 'Martínez',
            'telefono' => '11 4123-7788',
            'email' => 'ana@example.com',
            'activo' => true,
        ]);
        $vehiculo = Vehiculo::query()->create([
            'cliente_id' => $cliente->id,
            'patente' => 'AB123CD',
            'marca' => 'Toyota',
            'modelo' => 'Corolla',
            'anio' => 2019,
            'color' => 'Gris',
            'activo' => true,
        ]);
        $estadoPendiente = EstadoTurno::query()->create(['codigo' => 'pendiente', 'nombre' => 'Pendiente']);
        $this->estadoConfirmado = EstadoTurno::query()->create(['codigo' => 'confirmado', 'nombre' => 'Confirmado']);
        $this->turno = Turno::query()->create([
            'cliente_id' => $cliente->id,
            'vehiculo_id' => $vehiculo->id,
            'estado_turno_id' => $estadoPendiente->id,
            'registrado_por' => $this->responsableTurnos->id,
            'fecha' => now()->addDay()->toDateString(),
            'hora' => '09:30',
            'motivo' => 'Ruido en el tren delantero.',
        ]);

        $especialidad = Especialidad::query()->create([
            'codigo' => 'mecanica-general',
            'nombre' => 'Mecánica general',
        ]);
        $estadoEmpleadoActivo = EstadoEmpleado::query()->create(['codigo' => 'activo', 'nombre' => 'Activo']);
        $this->empleado = Empleado::query()->create([
            'legajo' => 'EMP-001',
            'nombre' => 'Martín',
            'apellido' => 'Gómez',
            'especialidad_id' => $especialidad->id,
            'estado_empleado_id' => $estadoEmpleadoActivo->id,
        ]);
        $this->servicio = TipoServicio::query()->create([
            'codigo' => 'diagnostico-motor',
            'nombre' => 'Diagnóstico de motor',
            'descripcion' => 'Evaluación mecánica.',
            'especialidad_id' => $especialidad->id,
            'precio_base' => '45000.00',
            'activo' => true,
        ]);
        $this->repuesto = Repuesto::query()->create([
            'codigo' => 'REP-FIL-001',
            'nombre' => 'Filtro de aceite',
            'marca' => 'Toyota',
            'original' => true,
            'precio_venta' => '18500.00',
            'stock' => 12,
            'activo' => true,
        ]);
        EstadoOrdenTrabajo::query()->create(['codigo' => 'emitida', 'nombre' => 'Emitida']);
    }

    public function test_guest_cannot_access_the_budget_form(): void
    {
        $this->get("/turnos/{$this->turno->id}/presupuesto/crear")->assertRedirect('/login');
    }

    public function test_user_without_an_allowed_role_cannot_generate_a_budget(): void
    {
        $this->actingAs(User::factory()->create())
            ->post('/presupuestos', $this->validBudgetPayload())
            ->assertForbidden();

        $this->assertDatabaseCount('presupuestos', 0);
    }

    public function test_responsible_user_can_view_the_budget_form(): void
    {
        $this->actingAs($this->responsableTurnos)
            ->get("/turnos/{$this->turno->id}/presupuesto/crear")
            ->assertOk()
            ->assertSee('Generar presupuesto')
            ->assertSee('Diagnóstico de motor')
            ->assertSee('Filtro de aceite')
            ->assertSee('AB123CD');
    }

    public function test_budget_is_created_atomically_with_server_calculated_total(): void
    {
        $response = $this->actingAs($this->responsableTurnos)
            ->post('/presupuestos', $this->validBudgetPayload());

        $presupuesto = Presupuesto::query()->firstOrFail();

        $response->assertRedirect("/presupuestos/{$presupuesto->id}");
        $response->assertSessionHas('status');
        $this->assertSame('PRE-000001', $presupuesto->numero);
        $this->assertSame('108500.00', $presupuesto->total);
        $this->assertDatabaseCount('detalle_presupuestos', 2);
        $this->assertDatabaseHas('aprobaciones_presupuesto', [
            'presupuesto_id' => $presupuesto->id,
            'estado' => 'pendiente',
            'registrado_por' => null,
        ]);
        $this->actingAs($this->responsableTurnos)
            ->get("/presupuestos/{$presupuesto->id}")
            ->assertOk()
            ->assertSee('PRE-000001')
            ->assertSee('Diagnóstico de motor')
            ->assertSee('Registrar aprobación');
    }

    public function test_budget_without_services_is_rejected_without_partial_records(): void
    {
        $payload = $this->validBudgetPayload();
        $payload['servicios'] = [];

        $this->actingAs($this->responsableTurnos)
            ->from("/turnos/{$this->turno->id}/presupuesto/crear")
            ->post('/presupuestos', $payload)
            ->assertRedirect("/turnos/{$this->turno->id}/presupuesto/crear")
            ->assertSessionHasErrors('servicios');

        $this->assertDatabaseCount('presupuestos', 0);
        $this->assertDatabaseCount('detalle_presupuestos', 0);
        $this->assertDatabaseCount('aprobaciones_presupuesto', 0);
    }

    public function test_services_from_different_specialties_are_rejected(): void
    {
        $otraEspecialidad = Especialidad::query()->create([
            'codigo' => 'electricidad',
            'nombre' => 'Electricidad automotriz',
        ]);
        $otroServicio = TipoServicio::query()->create([
            'codigo' => 'diagnostico-electrico',
            'nombre' => 'Diagnóstico eléctrico',
            'especialidad_id' => $otraEspecialidad->id,
            'precio_base' => '42000.00',
            'activo' => true,
        ]);
        $payload = $this->validBudgetPayload();
        $payload['servicios'][] = ['id' => $otroServicio->id, 'cantidad' => 1];

        $this->actingAs($this->responsableTurnos)
            ->from("/turnos/{$this->turno->id}/presupuesto/crear")
            ->post('/presupuestos', $payload)
            ->assertSessionHasErrors('servicios');

        $this->assertDatabaseCount('presupuestos', 0);
    }

    public function test_client_approval_is_audited_and_emits_one_compatible_work_order(): void
    {
        $presupuesto = $this->createBudget();

        $this->actingAs($this->responsableTurnos)
            ->post("/presupuestos/{$presupuesto->id}/decision", $this->approvalPayload())
            ->assertRedirect("/presupuestos/{$presupuesto->id}")
            ->assertSessionHas('status');

        $this->assertDatabaseHas('aprobaciones_presupuesto', [
            'presupuesto_id' => $presupuesto->id,
            'estado' => 'aprobado',
            'registrado_por' => $this->responsableTurnos->id,
            'medio_confirmacion' => 'whatsapp',
        ]);
        $this->assertDatabaseHas('ordenes_trabajo', [
            'numero' => 'OT-000001',
            'presupuesto_id' => $presupuesto->id,
            'turno_id' => $this->turno->id,
            'empleado_id' => $this->empleado->id,
            'emitida_por' => $this->responsableTurnos->id,
        ]);
        $this->assertDatabaseCount('detalle_ordenes_trabajo', 2);
        $this->assertDatabaseHas('turnos', [
            'id' => $this->turno->id,
            'estado_turno_id' => $this->estadoConfirmado->id,
        ]);
        $this->actingAs($this->responsableTurnos)
            ->get("/presupuestos/{$presupuesto->id}")
            ->assertOk()
            ->assertSee('OT-000001')
            ->assertSee('Martín Gómez')
            ->assertSee('Generada automáticamente');
    }

    public function test_client_rejection_is_audited_without_emitting_a_work_order(): void
    {
        $presupuesto = $this->createBudget();
        $payload = $this->approvalPayload();
        $payload['decision'] = 'rechazado';
        $payload['medio_confirmacion'] = 'email';

        $this->actingAs($this->responsableTurnos)
            ->post("/presupuestos/{$presupuesto->id}/decision", $payload)
            ->assertRedirect("/presupuestos/{$presupuesto->id}");

        $this->assertDatabaseHas('aprobaciones_presupuesto', [
            'presupuesto_id' => $presupuesto->id,
            'estado' => 'rechazado',
            'registrado_por' => $this->responsableTurnos->id,
        ]);
        $this->assertDatabaseCount('ordenes_trabajo', 0);
    }

    public function test_a_recorded_decision_cannot_be_changed_or_duplicate_the_work_order(): void
    {
        $presupuesto = $this->createBudget();

        $this->actingAs($this->responsableTurnos)
            ->post("/presupuestos/{$presupuesto->id}/decision", $this->approvalPayload());

        $secondPayload = $this->approvalPayload();
        $secondPayload['decision'] = 'rechazado';

        $this->actingAs($this->responsableTurnos)
            ->from("/presupuestos/{$presupuesto->id}")
            ->post("/presupuestos/{$presupuesto->id}/decision", $secondPayload)
            ->assertRedirect("/presupuestos/{$presupuesto->id}")
            ->assertSessionHasErrors('decision');

        $this->assertDatabaseHas('aprobaciones_presupuesto', [
            'presupuesto_id' => $presupuesto->id,
            'estado' => 'aprobado',
        ]);
        $this->assertDatabaseCount('ordenes_trabajo', 1);
    }

    public function test_approval_without_a_compatible_active_employee_leaves_no_partial_decision(): void
    {
        $presupuesto = $this->createBudget();
        $inactiveState = EstadoEmpleado::query()->create(['codigo' => 'inactivo', 'nombre' => 'Inactivo']);
        $this->empleado->update(['estado_empleado_id' => $inactiveState->id]);

        $this->actingAs($this->responsableTurnos)
            ->from("/presupuestos/{$presupuesto->id}")
            ->post("/presupuestos/{$presupuesto->id}/decision", $this->approvalPayload())
            ->assertRedirect("/presupuestos/{$presupuesto->id}")
            ->assertSessionHasErrors('decision');

        $this->assertDatabaseHas('aprobaciones_presupuesto', [
            'presupuesto_id' => $presupuesto->id,
            'estado' => 'pendiente',
            'registrado_por' => null,
        ]);
        $this->assertDatabaseCount('ordenes_trabajo', 0);
    }

    /** @return array<string, mixed> */
    private function validBudgetPayload(): array
    {
        return [
            'turno_id' => $this->turno->id,
            'servicios' => [
                ['id' => $this->servicio->id, 'cantidad' => 2],
            ],
            'repuestos' => [
                ['id' => $this->repuesto->id, 'cantidad' => 1],
            ],
            'observaciones' => 'Sujeto a revisión al desarmar.',
        ];
    }

    /** @return array<string, string> */
    private function approvalPayload(): array
    {
        return [
            'decision' => 'aprobado',
            'fecha_decision' => now()->toDateString(),
            'medio_confirmacion' => 'whatsapp',
            'observaciones_decision' => 'Cliente confirma el trabajo.',
        ];
    }

    private function createBudget(): Presupuesto
    {
        $this->actingAs($this->responsableTurnos)
            ->post('/presupuestos', $this->validBudgetPayload())
            ->assertSessionHasNoErrors();

        return Presupuesto::query()->firstOrFail();
    }
}
