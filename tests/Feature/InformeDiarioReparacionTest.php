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
use App\Models\Repuesto;
use App\Models\Role;
use App\Models\TipoReparacion;
use App\Models\TipoServicio;
use App\Models\Turno;
use App\Models\User;
use App\Models\Vehiculo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InformeDiarioReparacionTest extends TestCase
{
    use RefreshDatabase;

    private User $responsableReparacion;

    protected function setUp(): void
    {
        parent::setUp();

        $this->responsableReparacion = User::factory()->create(['name' => 'Responsable Taller']);
        $role = Role::query()->create([
            'name' => 'reparacion',
            'display_name' => 'Responsable de Reparación',
        ]);
        $this->responsableReparacion->roles()->attach($role);
    }

    public function test_guest_cannot_access_the_daily_repair_report(): void
    {
        $this->get('/reparaciones/informes/diario')->assertRedirect('/login');
    }

    public function test_user_without_an_allowed_role_cannot_access_the_daily_repair_report(): void
    {
        $this->actingAs(User::factory()->create())
            ->get('/reparaciones/informes/diario')
            ->assertForbidden();
    }

    public function test_report_screen_requests_a_date_before_generating_information(): void
    {
        $this->actingAs($this->responsableReparacion)
            ->get('/reparaciones/informes/diario')
            ->assertOk()
            ->assertSee('Informe diario de reparaciones')
            ->assertSee('Seleccioná una fecha')
            ->assertDontSee('Reparaciones del');
    }

    public function test_daily_report_contains_the_required_operational_information(): void
    {
        $this->createRepair('2026-10-29 10:15:00');

        $this->actingAs($this->responsableReparacion)
            ->get('/reparaciones/informes/diario?fecha=2026-10-29')
            ->assertOk()
            ->assertSee('Reparaciones del 29/10/2026')
            ->assertSee('REP-000001')
            ->assertSee('AB123CD')
            ->assertSee('Toyota Corolla')
            ->assertSee('Mantenimiento preventivo')
            ->assertSee('Service programado')
            ->assertSee('Martín Gómez')
            ->assertSee('Filtro de aceite (1)')
            ->assertSee('2 h 0 min')
            ->assertSee('Imprimir');
    }

    public function test_report_only_includes_repairs_from_the_selected_date(): void
    {
        $this->createRepair('2026-10-29 10:15:00');

        $this->actingAs($this->responsableReparacion)
            ->get('/reparaciones/informes/diario?fecha=2026-10-30')
            ->assertOk()
            ->assertSee('No existen reparaciones para la fecha seleccionada')
            ->assertDontSee('REP-000001');
    }

    public function test_invalid_report_date_is_rejected(): void
    {
        $this->actingAs($this->responsableReparacion)
            ->from('/reparaciones/informes/diario')
            ->get('/reparaciones/informes/diario?fecha=29-10-2026')
            ->assertRedirect('/reparaciones/informes/diario')
            ->assertSessionHasErrors('fecha');
    }

    private function createRepair(string $fechaInicio): Reparacion
    {
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
        $estadoReparacion = EstadoReparacion::query()->create(['codigo' => 'en-proceso', 'nombre' => 'En proceso']);
        $reparacion = Reparacion::query()->create([
            'numero' => 'REP-000001',
            'orden_trabajo_id' => $ordenTrabajo->id,
            'vehiculo_id' => $vehiculo->id,
            'empleado_id' => $empleado->id,
            'tipo_reparacion_id' => $tipoReparacion->id,
            'estado_reparacion_id' => $estadoReparacion->id,
            'registrado_por' => $this->responsableReparacion->id,
            'fecha_inicio' => $fechaInicio,
            'tiempo_empleado_minutos' => 120,
            'trabajos_realizados' => 'Se realizó el service y el control general.',
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
        $reparacion->detalles()->createMany([
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

        return $reparacion;
    }
}
