<?php

namespace Tests\Feature;

use App\Models\AprobacionPresupuesto;
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
use App\Models\Repuesto;
use App\Models\Role;
use App\Models\TipoReparacion;
use App\Models\TipoServicio;
use App\Models\Turno;
use App\Models\User;
use App\Models\Vehiculo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReparacionRegistrationTest extends TestCase
{
    use RefreshDatabase;

    private User $responsableReparacion;

    private Turno $turno;

    private Presupuesto $presupuesto;

    private AprobacionPresupuesto $aprobacion;

    private OrdenTrabajo $ordenTrabajo;

    private EstadoOrdenTrabajo $estadoOrdenEnProceso;

    private EstadoOrdenTrabajo $estadoOrdenFinalizada;

    private EstadoReparacion $estadoReparacionEnProceso;

    private EstadoReparacion $estadoReparacionFinalizada;

    private EstadoTurno $estadoTurnoAtendido;

    private TipoReparacion $tipoReparacion;

    private Repuesto $repuesto;

    private int $detalleRepuestoId;

    protected function setUp(): void
    {
        parent::setUp();

        $this->responsableReparacion = User::factory()->create(['name' => 'Responsable Taller']);
        $role = Role::query()->create([
            'name' => 'reparacion',
            'display_name' => 'Responsable de Reparación',
        ]);
        $this->responsableReparacion->roles()->attach($role);

        $cliente = Cliente::query()->create([
            'dni' => '30111222',
            'nombre' => 'Ana',
            'apellido' => 'Martínez',
            'telefono' => '11 4123-7788',
            'activo' => true,
        ]);
        $vehiculo = Vehiculo::query()->create([
            'cliente_id' => $cliente->id,
            'patente' => 'AB123CD',
            'marca' => 'Toyota',
            'modelo' => 'Corolla',
            'anio' => 2019,
            'activo' => true,
        ]);
        $estadoTurnoConfirmado = EstadoTurno::query()->create(['codigo' => 'confirmado', 'nombre' => 'Confirmado']);
        $this->estadoTurnoAtendido = EstadoTurno::query()->create(['codigo' => 'atendido', 'nombre' => 'Atendido']);
        $this->turno = Turno::query()->create([
            'cliente_id' => $cliente->id,
            'vehiculo_id' => $vehiculo->id,
            'estado_turno_id' => $estadoTurnoConfirmado->id,
            'registrado_por' => $this->responsableReparacion->id,
            'fecha' => now()->toDateString(),
            'hora' => '09:30',
            'motivo' => 'Service y revisión de motor.',
        ]);

        $especialidad = Especialidad::query()->create(['codigo' => 'mecanica-general', 'nombre' => 'Mecánica general']);
        $estadoEmpleado = EstadoEmpleado::query()->create(['codigo' => 'activo', 'nombre' => 'Activo']);
        $empleado = Empleado::query()->create([
            'legajo' => 'EMP-001',
            'nombre' => 'Martín',
            'apellido' => 'Gómez',
            'especialidad_id' => $especialidad->id,
            'estado_empleado_id' => $estadoEmpleado->id,
        ]);
        $servicio = TipoServicio::query()->create([
            'codigo' => 'service-programado',
            'nombre' => 'Service programado',
            'especialidad_id' => $especialidad->id,
            'precio_base' => '85000.00',
            'activo' => true,
        ]);
        $this->repuesto = Repuesto::query()->create([
            'codigo' => 'REP-FIL-001',
            'nombre' => 'Filtro de aceite',
            'marca' => 'Toyota',
            'original' => true,
            'precio_venta' => '18500.00',
            'stock' => 10,
            'activo' => true,
        ]);

        $this->presupuesto = Presupuesto::query()->create([
            'numero' => 'PRE-000001',
            'turno_id' => $this->turno->id,
            'registrado_por' => $this->responsableReparacion->id,
            'fecha_emision' => now()->toDateString(),
            'valido_hasta' => now()->addDays(15)->toDateString(),
            'total' => '103500.00',
        ]);
        $this->aprobacion = AprobacionPresupuesto::query()->create([
            'presupuesto_id' => $this->presupuesto->id,
            'estado' => 'aprobado',
            'registrado_por' => $this->responsableReparacion->id,
            'fecha_decision' => now(),
            'medio_confirmacion' => 'whatsapp',
        ]);

        $estadoOrdenEmitida = EstadoOrdenTrabajo::query()->create(['codigo' => 'emitida', 'nombre' => 'Emitida']);
        $this->estadoOrdenEnProceso = EstadoOrdenTrabajo::query()->create(['codigo' => 'en-proceso', 'nombre' => 'En proceso']);
        $this->estadoOrdenFinalizada = EstadoOrdenTrabajo::query()->create(['codigo' => 'finalizada', 'nombre' => 'Finalizada']);
        $this->ordenTrabajo = OrdenTrabajo::query()->create([
            'numero' => 'OT-000001',
            'presupuesto_id' => $this->presupuesto->id,
            'turno_id' => $this->turno->id,
            'vehiculo_id' => $vehiculo->id,
            'empleado_id' => $empleado->id,
            'estado_orden_trabajo_id' => $estadoOrdenEmitida->id,
            'emitida_por' => $this->responsableReparacion->id,
            'fecha_emision' => now(),
        ]);
        $this->ordenTrabajo->detalles()->create([
            'tipo_servicio_id' => $servicio->id,
            'descripcion' => $servicio->nombre,
            'cantidad' => 1,
            'precio_referencia' => '85000.00',
        ]);
        $detalleRepuesto = $this->ordenTrabajo->detalles()->create([
            'repuesto_id' => $this->repuesto->id,
            'descripcion' => $this->repuesto->nombre,
            'cantidad' => 2,
            'precio_referencia' => '18500.00',
        ]);
        $this->detalleRepuestoId = $detalleRepuesto->id;

        $this->tipoReparacion = TipoReparacion::query()->create([
            'codigo' => 'preventiva',
            'nombre' => 'Mantenimiento preventivo',
            'activo' => true,
        ]);
        $this->estadoReparacionEnProceso = EstadoReparacion::query()->create(['codigo' => 'en-proceso', 'nombre' => 'En proceso']);
        $this->estadoReparacionFinalizada = EstadoReparacion::query()->create(['codigo' => 'finalizada', 'nombre' => 'Finalizada']);
    }

    public function test_guest_cannot_access_repair_registration(): void
    {
        $this->get('/reparaciones/crear')->assertRedirect('/login');
    }

    public function test_user_without_an_allowed_role_cannot_register_a_repair(): void
    {
        $this->actingAs(User::factory()->create())
            ->post('/reparaciones', $this->validPayload())
            ->assertForbidden();

        $this->assertDatabaseCount('reparaciones', 0);
    }

    public function test_responsible_user_can_select_an_eligible_work_order(): void
    {
        $this->actingAs($this->responsableReparacion)
            ->get('/reparaciones/crear')
            ->assertOk()
            ->assertSee('OT-000001')
            ->assertSee('AB123CD')
            ->assertSee('Martín Gómez');

        $this->actingAs($this->responsableReparacion)
            ->get("/reparaciones/crear/{$this->ordenTrabajo->id}")
            ->assertOk()
            ->assertSee('Service programado')
            ->assertSee('Filtro de aceite')
            ->assertSee('Mantenimiento preventivo');
    }

    public function test_incomplete_repair_is_rejected_without_partial_records(): void
    {
        $this->actingAs($this->responsableReparacion)
            ->from("/reparaciones/crear/{$this->ordenTrabajo->id}")
            ->post('/reparaciones', [
                'orden_trabajo_id' => $this->ordenTrabajo->id,
                'tipo_reparacion_id' => '',
                'tiempo_empleado_minutos' => 0,
                'trabajos_realizados' => '',
            ])
            ->assertRedirect("/reparaciones/crear/{$this->ordenTrabajo->id}")
            ->assertSessionHasErrors(['tipo_reparacion_id', 'tiempo_empleado_minutos', 'trabajos_realizados']);

        $this->assertDatabaseCount('reparaciones', 0);
        $this->assertSame(10, $this->repuesto->fresh()->stock);
    }

    public function test_repair_requires_an_approved_budget_and_emitted_work_order(): void
    {
        $this->aprobacion->update(['estado' => 'pendiente']);

        $this->actingAs($this->responsableReparacion)
            ->from("/reparaciones/crear/{$this->ordenTrabajo->id}")
            ->post('/reparaciones', $this->validPayload())
            ->assertSessionHasErrors('orden_trabajo_id');

        $this->assertDatabaseCount('reparaciones', 0);
        $this->assertSame(10, $this->repuesto->fresh()->stock);
    }

    public function test_repair_is_registered_with_traceability_and_real_part_usage(): void
    {
        $response = $this->actingAs($this->responsableReparacion)
            ->post('/reparaciones', $this->validPayload());

        $reparacion = Reparacion::query()->firstOrFail();

        $response->assertRedirect("/reparaciones/{$reparacion->id}");
        $this->assertSame('REP-000001', $reparacion->numero);
        $this->assertDatabaseHas('reparaciones', [
            'id' => $reparacion->id,
            'orden_trabajo_id' => $this->ordenTrabajo->id,
            'empleado_id' => $this->ordenTrabajo->empleado_id,
            'tipo_reparacion_id' => $this->tipoReparacion->id,
            'estado_reparacion_id' => $this->estadoReparacionEnProceso->id,
            'tiempo_empleado_minutos' => 120,
        ]);
        $this->assertDatabaseCount('detalle_reparaciones', 2);
        $this->assertSame(8, $this->repuesto->fresh()->stock);
        $this->assertDatabaseHas('ordenes_trabajo', [
            'id' => $this->ordenTrabajo->id,
            'estado_orden_trabajo_id' => $this->estadoOrdenEnProceso->id,
        ]);
        $this->assertDatabaseHas('turnos', [
            'id' => $this->turno->id,
            'estado_turno_id' => $this->estadoTurnoAtendido->id,
        ]);

        $this->actingAs($this->responsableReparacion)
            ->get("/reparaciones/{$reparacion->id}")
            ->assertOk()
            ->assertSee('REP-000001')
            ->assertSee('Service programado')
            ->assertSee('Filtro de aceite')
            ->assertSee('Sin incidencias registradas.');
    }

    public function test_a_part_outside_the_work_order_cannot_be_registered(): void
    {
        $payload = $this->validPayload();
        $payload['repuestos'][0]['detalle_id'] = 999999;

        $this->actingAs($this->responsableReparacion)
            ->from("/reparaciones/crear/{$this->ordenTrabajo->id}")
            ->post('/reparaciones', $payload)
            ->assertSessionHasErrors('repuestos.0.detalle_id');

        $this->assertDatabaseCount('reparaciones', 0);
        $this->assertSame(10, $this->repuesto->fresh()->stock);
    }

    public function test_a_work_order_cannot_create_duplicate_repairs_or_consume_stock_twice(): void
    {
        $this->actingAs($this->responsableReparacion)
            ->post('/reparaciones', $this->validPayload());

        $this->actingAs($this->responsableReparacion)
            ->from('/reparaciones/crear')
            ->post('/reparaciones', $this->validPayload())
            ->assertSessionHasErrors('orden_trabajo_id');

        $this->assertDatabaseCount('reparaciones', 1);
        $this->assertSame(8, $this->repuesto->fresh()->stock);
    }

    public function test_repair_can_be_finalized_once_and_closes_the_work_order(): void
    {
        $this->actingAs($this->responsableReparacion)
            ->post('/reparaciones', $this->validPayload());
        $reparacion = Reparacion::query()->firstOrFail();

        $this->actingAs($this->responsableReparacion)
            ->post("/reparaciones/{$reparacion->id}/finalizar")
            ->assertRedirect("/reparaciones/{$reparacion->id}")
            ->assertSessionHas('status');

        $this->assertDatabaseHas('reparaciones', [
            'id' => $reparacion->id,
            'estado_reparacion_id' => $this->estadoReparacionFinalizada->id,
        ]);
        $this->assertNotNull($reparacion->fresh()->fecha_fin);
        $this->assertDatabaseHas('ordenes_trabajo', [
            'id' => $this->ordenTrabajo->id,
            'estado_orden_trabajo_id' => $this->estadoOrdenFinalizada->id,
        ]);

        $this->actingAs($this->responsableReparacion)
            ->from("/reparaciones/{$reparacion->id}")
            ->post("/reparaciones/{$reparacion->id}/finalizar")
            ->assertSessionHasErrors('estado');
    }

    /** @return array<string, mixed> */
    private function validPayload(): array
    {
        return [
            'orden_trabajo_id' => $this->ordenTrabajo->id,
            'tipo_reparacion_id' => $this->tipoReparacion->id,
            'tiempo_empleado_minutos' => 120,
            'trabajos_realizados' => 'Se realizó el service programado y el control general de la unidad.',
            'incidencias' => null,
            'repuestos' => [
                ['detalle_id' => $this->detalleRepuestoId, 'cantidad' => 2],
            ],
        ];
    }
}
