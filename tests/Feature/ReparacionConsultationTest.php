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

class ReparacionConsultationTest extends TestCase
{
    use RefreshDatabase;

    private User $responsableReparacion;

    private Reparacion $reparacion;

    private EstadoReparacion $estadoFinalizada;

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
        $especialidad = Especialidad::query()->create([
            'codigo' => 'mecanica-general',
            'nombre' => 'Mecánica general',
        ]);
        $estadoEmpleado = EstadoEmpleado::query()->create(['codigo' => 'activo', 'nombre' => 'Activo']);
        $empleado = Empleado::query()->create([
            'legajo' => 'EMP-001',
            'nombre' => 'Martín',
            'apellido' => 'Gómez',
            'especialidad_id' => $especialidad->id,
            'estado_empleado_id' => $estadoEmpleado->id,
        ]);
        $estadoTurno = EstadoTurno::query()->create(['codigo' => 'atendido', 'nombre' => 'Atendido']);
        $turno = Turno::query()->create([
            'cliente_id' => $cliente->id,
            'vehiculo_id' => $vehiculo->id,
            'estado_turno_id' => $estadoTurno->id,
            'registrado_por' => $this->responsableReparacion->id,
            'fecha' => '2026-10-29',
            'hora' => '09:30',
            'motivo' => 'Service programado.',
        ]);
        $presupuesto = Presupuesto::query()->create([
            'numero' => 'PRE-000001',
            'turno_id' => $turno->id,
            'registrado_por' => $this->responsableReparacion->id,
            'fecha_emision' => '2026-10-29',
            'valido_hasta' => '2026-11-13',
            'total' => '103500.00',
        ]);
        AprobacionPresupuesto::query()->create([
            'presupuesto_id' => $presupuesto->id,
            'estado' => 'aprobado',
            'registrado_por' => $this->responsableReparacion->id,
            'fecha_decision' => '2026-10-29 09:00:00',
        ]);
        $estadoOrden = EstadoOrdenTrabajo::query()->create(['codigo' => 'en-proceso', 'nombre' => 'En proceso']);
        $ordenTrabajo = OrdenTrabajo::query()->create([
            'numero' => 'OT-000001',
            'presupuesto_id' => $presupuesto->id,
            'turno_id' => $turno->id,
            'vehiculo_id' => $vehiculo->id,
            'empleado_id' => $empleado->id,
            'estado_orden_trabajo_id' => $estadoOrden->id,
            'emitida_por' => $this->responsableReparacion->id,
            'fecha_emision' => '2026-10-29 09:05:00',
        ]);
        $tipoReparacion = TipoReparacion::query()->create([
            'codigo' => 'preventiva',
            'nombre' => 'Mantenimiento preventivo',
            'activo' => true,
        ]);
        $estadoEnProceso = EstadoReparacion::query()->create(['codigo' => 'en-proceso', 'nombre' => 'En proceso']);
        $this->estadoFinalizada = EstadoReparacion::query()->create(['codigo' => 'finalizada', 'nombre' => 'Finalizada']);
        $this->reparacion = Reparacion::query()->create([
            'numero' => 'REP-000001',
            'orden_trabajo_id' => $ordenTrabajo->id,
            'vehiculo_id' => $vehiculo->id,
            'empleado_id' => $empleado->id,
            'tipo_reparacion_id' => $tipoReparacion->id,
            'estado_reparacion_id' => $estadoEnProceso->id,
            'registrado_por' => $this->responsableReparacion->id,
            'fecha_inicio' => '2026-10-29 10:15:00',
            'tiempo_empleado_minutos' => 120,
            'trabajos_realizados' => 'Se realizó el service y el control general.',
            'incidencias' => 'Se detectó desgaste en una correa auxiliar.',
        ]);
        $servicio = TipoServicio::query()->create([
            'codigo' => 'service-programado',
            'nombre' => 'Service programado',
            'especialidad_id' => $especialidad->id,
            'precio_base' => '85000.00',
            'activo' => true,
        ]);
        $repuesto = Repuesto::query()->create([
            'codigo' => 'REP-FIL-001',
            'nombre' => 'Filtro de aceite',
            'marca' => 'Toyota',
            'original' => true,
            'precio_venta' => '18500.00',
            'stock' => 8,
            'activo' => true,
        ]);
        $this->reparacion->detalles()->createMany([
            [
                'tipo' => 'servicio',
                'tipo_servicio_id' => $servicio->id,
                'descripcion' => $servicio->nombre,
                'cantidad' => 1,
            ],
            [
                'tipo' => 'repuesto',
                'repuesto_id' => $repuesto->id,
                'descripcion' => $repuesto->nombre,
                'cantidad' => 1,
            ],
        ]);
    }

    public function test_guest_cannot_access_repair_consultation(): void
    {
        $this->get('/reparaciones')->assertRedirect('/login');
    }

    public function test_user_without_an_allowed_role_cannot_access_repair_consultation(): void
    {
        $this->actingAs(User::factory()->create())
            ->get('/reparaciones')
            ->assertForbidden();
    }

    public function test_responsible_user_can_list_registered_repairs(): void
    {
        $this->actingAs($this->responsableReparacion)
            ->get('/reparaciones')
            ->assertOk()
            ->assertSee('REP-000001')
            ->assertSee('Martínez, Ana')
            ->assertSee('AB123CD')
            ->assertSee('Mantenimiento preventivo')
            ->assertSee('Gómez, Martín')
            ->assertSee('En proceso');
    }

    public function test_repairs_can_be_searched_by_identifier_and_vehicle_data(): void
    {
        foreach (["{$this->reparacion->id}", 'REP-000001', 'AB123CD', 'Toyota', 'Corolla'] as $search) {
            $this->actingAs($this->responsableReparacion)
                ->get('/reparaciones?'.http_build_query(['buscar' => $search]))
                ->assertOk()
                ->assertSee('REP-000001')
                ->assertSee('AB123CD');
        }
    }

    public function test_repairs_can_be_filtered_by_state(): void
    {
        $this->actingAs($this->responsableReparacion)
            ->get('/reparaciones?estado=finalizada')
            ->assertOk()
            ->assertSee('No se encontraron reparaciones');

        $this->reparacion->update(['estado_reparacion_id' => $this->estadoFinalizada->id]);

        $this->actingAs($this->responsableReparacion)
            ->get('/reparaciones?estado=finalizada')
            ->assertOk()
            ->assertSee('REP-000001')
            ->assertSee('Finalizada');
    }

    public function test_empty_search_result_is_reported_without_changing_data(): void
    {
        $this->actingAs($this->responsableReparacion)
            ->get('/reparaciones?buscar=REP-999999')
            ->assertOk()
            ->assertSee('No se encontraron reparaciones')
            ->assertSee('Nueva búsqueda');

        $this->assertDatabaseHas('reparaciones', [
            'id' => $this->reparacion->id,
            'numero' => 'REP-000001',
        ]);
    }

    public function test_responsible_user_can_view_the_complete_repair_detail(): void
    {
        $this->actingAs($this->responsableReparacion)
            ->get("/reparaciones/{$this->reparacion->id}")
            ->assertOk()
            ->assertSee('REP-000001')
            ->assertSee('Ana Martínez')
            ->assertSee('AB123CD')
            ->assertSee('Toyota Corolla')
            ->assertSee('Mantenimiento preventivo')
            ->assertSee('Martín Gómez')
            ->assertSee('Service programado')
            ->assertSee('Filtro de aceite')
            ->assertSee('2 horas')
            ->assertSee('Se detectó desgaste en una correa auxiliar.');
    }
}
