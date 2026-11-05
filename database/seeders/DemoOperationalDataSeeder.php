<?php

namespace Database\Seeders;

use App\Models\AprobacionPresupuesto;
use App\Models\Empleado;
use App\Models\EstadoOrdenTrabajo;
use App\Models\EstadoReparacion;
use App\Models\EstadoTurno;
use App\Models\OrdenTrabajo;
use App\Models\Presupuesto;
use App\Models\Reparacion;
use App\Models\Repuesto;
use App\Models\TipoReparacion;
use App\Models\TipoServicio;
use App\Models\Turno;
use App\Models\User;
use App\Models\Vehiculo;
use Carbon\CarbonImmutable;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DemoOperationalDataSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function (): void {
            $administrator = User::query()->where('username', 'admin')->firstOrFail();
            $now = CarbonImmutable::now();

            foreach ($this->scenarios() as $scenario) {
                $vehicle = Vehiculo::query()
                    ->with('cliente')
                    ->where('patente', $scenario['patente'])
                    ->firstOrFail();
                $appointmentState = EstadoTurno::query()
                    ->where('codigo', $scenario['estado_turno'])
                    ->firstOrFail();
                $marker = "Datos ficticios de demostración [AUTOTECH-DEMO:{$scenario['key']}]";

                $appointment = Turno::query()->firstOrCreate(
                    ['observaciones' => $marker],
                    [
                        'cliente_id' => $vehicle->cliente_id,
                        'vehiculo_id' => $vehicle->id,
                        'estado_turno_id' => $appointmentState->id,
                        'registrado_por' => $administrator->id,
                        'fecha' => $now->startOfDay()->addDays($scenario['day_offset'])->toDateString(),
                        'hora' => $scenario['hora'],
                        'motivo' => $scenario['motivo'],
                    ],
                );

                if (! isset($scenario['presupuesto'])) {
                    continue;
                }

                $budget = $this->seedBudget($appointment, $administrator, $scenario['presupuesto'], $now);

                if (! isset($scenario['decision'])) {
                    continue;
                }

                $approval = $this->seedApproval($budget, $administrator, $scenario['decision'], $now);

                if ($approval->estado !== 'aprobado' || ! isset($scenario['orden'])) {
                    continue;
                }

                $workOrder = $this->seedWorkOrder($budget, $administrator, $scenario['orden'], $now);

                if (isset($scenario['reparacion'])) {
                    $this->seedRepair($workOrder, $administrator, $scenario['reparacion'], $now);
                }
            }
        }, 3);
    }

    /**
     * @param  array{servicio: string, repuesto: string, cantidad_repuesto: int, observaciones: string}  $definition
     */
    private function seedBudget(Turno $appointment, User $administrator, array $definition, CarbonImmutable $now): Presupuesto
    {
        $service = TipoServicio::query()->where('codigo', $definition['servicio'])->firstOrFail();
        $part = Repuesto::query()->where('codigo', $definition['repuesto'])->firstOrFail();
        $servicePrice = $this->priceToCents($service->precio_base);
        $partPrice = $this->priceToCents($part->precio_venta);
        $partSubtotal = $partPrice * $definition['cantidad_repuesto'];

        $budget = Presupuesto::query()->firstOrCreate(
            ['turno_id' => $appointment->id],
            [
                'registrado_por' => $administrator->id,
                'fecha_emision' => $now->toDateString(),
                'valido_hasta' => $now->addDays(15)->toDateString(),
                'total' => $this->centsToDecimal($servicePrice + $partSubtotal),
                'observaciones' => $definition['observaciones'],
            ],
        );

        if ($budget->numero === null) {
            $budget->update(['numero' => sprintf('PRE-%06d', $budget->id)]);
        }

        if ($budget->detalles()->doesntExist()) {
            $budget->detalles()->createMany([
                [
                    'tipo' => 'servicio',
                    'tipo_servicio_id' => $service->id,
                    'repuesto_id' => null,
                    'descripcion' => $service->nombre,
                    'cantidad' => 1,
                    'precio_unitario' => $this->centsToDecimal($servicePrice),
                    'subtotal' => $this->centsToDecimal($servicePrice),
                ],
                [
                    'tipo' => 'repuesto',
                    'tipo_servicio_id' => null,
                    'repuesto_id' => $part->id,
                    'descripcion' => $part->nombre,
                    'cantidad' => $definition['cantidad_repuesto'],
                    'precio_unitario' => $this->centsToDecimal($partPrice),
                    'subtotal' => $this->centsToDecimal($partSubtotal),
                ],
            ]);
        }

        return $budget;
    }

    private function seedApproval(Presupuesto $budget, User $administrator, string $decision, CarbonImmutable $now): AprobacionPresupuesto
    {
        return AprobacionPresupuesto::query()->firstOrCreate(
            ['presupuesto_id' => $budget->id],
            [
                'estado' => $decision,
                'registrado_por' => $decision === 'pendiente' ? null : $administrator->id,
                'fecha_decision' => $decision === 'pendiente' ? null : $now,
                'medio_confirmacion' => $decision === 'pendiente' ? null : 'whatsapp',
                'observaciones' => $decision === 'pendiente'
                    ? null
                    : 'Decisión ficticia precargada para demostrar el flujo operativo.',
            ],
        );
    }

    /** @param array{empleado: string, estado: string} $definition */
    private function seedWorkOrder(Presupuesto $budget, User $administrator, array $definition, CarbonImmutable $now): OrdenTrabajo
    {
        $employee = Empleado::query()->where('legajo', $definition['empleado'])->firstOrFail();
        $workOrderState = EstadoOrdenTrabajo::query()->where('codigo', $definition['estado'])->firstOrFail();

        $workOrder = OrdenTrabajo::query()->firstOrCreate(
            ['presupuesto_id' => $budget->id],
            [
                'turno_id' => $budget->turno_id,
                'vehiculo_id' => $budget->turno->vehiculo_id,
                'empleado_id' => $employee->id,
                'estado_orden_trabajo_id' => $workOrderState->id,
                'emitida_por' => $administrator->id,
                'fecha_emision' => $now,
                'instrucciones' => $budget->observaciones,
            ],
        );

        if ($workOrder->numero === null) {
            $workOrder->update(['numero' => sprintf('OT-%06d', $workOrder->id)]);
        }

        if ($workOrder->detalles()->doesntExist()) {
            $workOrder->detalles()->createMany($budget->detalles->map(fn ($detail): array => [
                'tipo_servicio_id' => $detail->tipo_servicio_id,
                'repuesto_id' => $detail->repuesto_id,
                'descripcion' => $detail->descripcion,
                'cantidad' => $detail->cantidad,
                'precio_referencia' => $detail->precio_unitario,
            ])->all());
        }

        return $workOrder;
    }

    /**
     * @param  array{tipo: string, estado: string, minutos: int, trabajos: string, incidencias: ?string}  $definition
     */
    private function seedRepair(OrdenTrabajo $workOrder, User $administrator, array $definition, CarbonImmutable $now): Reparacion
    {
        $repairType = TipoReparacion::query()->where('codigo', $definition['tipo'])->firstOrFail();
        $repairState = EstadoReparacion::query()->where('codigo', $definition['estado'])->firstOrFail();
        $startedAt = $now->subMinutes($definition['minutos'] + 45);
        $finishedAt = $definition['estado'] === 'finalizada' ? $now : null;

        $repair = Reparacion::query()->firstOrCreate(
            ['orden_trabajo_id' => $workOrder->id],
            [
                'vehiculo_id' => $workOrder->vehiculo_id,
                'empleado_id' => $workOrder->empleado_id,
                'tipo_reparacion_id' => $repairType->id,
                'estado_reparacion_id' => $repairState->id,
                'registrado_por' => $administrator->id,
                'fecha_inicio' => $startedAt,
                'fecha_fin' => $finishedAt,
                'tiempo_empleado_minutos' => $definition['minutos'],
                'trabajos_realizados' => $definition['trabajos'],
                'incidencias' => $definition['incidencias'],
            ],
        );

        if ($repair->numero === null) {
            $repair->update(['numero' => sprintf('REP-%06d', $repair->id)]);
        }

        if ($repair->detalles()->doesntExist()) {
            $repair->detalles()->createMany($workOrder->detalles->map(fn ($detail): array => [
                'tipo' => $detail->tipo_servicio_id === null ? 'repuesto' : 'servicio',
                'tipo_servicio_id' => $detail->tipo_servicio_id,
                'repuesto_id' => $detail->repuesto_id,
                'descripcion' => $detail->descripcion,
                'cantidad' => $detail->cantidad,
            ])->all());
        }

        return $repair;
    }

    /** @return array<int, array<string, mixed>> */
    private function scenarios(): array
    {
        return [
            $this->repairScenario('active-service', 'AB123CD', '08:00', 'Service programado y control general', 'service-programado', 'REP-FIL-001', 'EMP-001', 'preventiva', 'en-proceso', 150, 'Se reemplazó el filtro y se realiza el control general de la unidad.'),
            $this->repairScenario('active-electric', 'AE321JK', '09:15', 'Diagnóstico del sistema eléctrico', 'diagnostico-electrico', 'REP-BAT-001', 'EMP-002', 'diagnostico', 'en-proceso', 105, 'Se verifican batería, alternador y circuito de arranque.'),
            $this->repairScenario('active-brakes', 'AH246QR', '10:30', 'Revisión de frenos delanteros', 'revision-frenos', 'REP-PAS-001', 'EMP-003', 'correctiva', 'en-proceso', 75, 'Se reemplazan las pastillas y se controla el sistema de frenado.'),
            $this->repairScenario('completed-engine', 'AD789GH', '11:30', 'Mantenimiento preventivo del motor', 'service-programado', 'REP-ACE-001', 'EMP-001', 'preventiva', 'finalizada', 120, 'Se realizó cambio de aceite, filtro y control de niveles.'),
            $this->repairScenario('completed-suspension', 'AF654LM', '13:00', 'Control de suspensión y tren delantero', 'revision-suspension', 'REP-AMO-001', 'EMP-003', 'inspeccion', 'finalizada', 180, 'Se reemplazó el amortiguador delantero y se verificó el tren delantero.'),
            $this->budgetScenario('pending-service', 'AG987NP', 1, '09:00', 'Service de mantenimiento periódico', 'service-programado', 'REP-FIL-002', 'pendiente'),
            $this->budgetScenario('pending-engine', 'AI135ST', 1, '11:00', 'Diagnóstico por falla de encendido', 'diagnostico-motor', 'REP-BUJ-001', 'pendiente'),
            $this->budgetScenario('rejected-electrical', 'AJ864UV', -1, '15:30', 'Diagnóstico de alternador', 'diagnostico-electrico', 'REP-ALT-001', 'rechazado'),
            $this->appointmentScenario('future-brakes', 'AK579WX', 1, '14:30', 'Control preventivo de frenos', 'confirmado'),
            $this->appointmentScenario('future-service', 'AL318YZ', 2, '08:30', 'Service de los 60.000 km', 'pendiente'),
            $this->appointmentScenario('future-suspension', 'AM742BC', 2, '10:30', 'Revisión de suspensión por vibraciones', 'confirmado'),
            $this->appointmentScenario('future-electric', 'AC456EF', 3, '16:00', 'Control de luces y batería', 'pendiente'),
        ];
    }

    /** @return array<string, mixed> */
    private function appointmentScenario(string $key, string $patent, int $dayOffset, string $time, string $reason, string $state): array
    {
        return [
            'key' => $key,
            'patente' => $patent,
            'day_offset' => $dayOffset,
            'hora' => $time,
            'motivo' => $reason,
            'estado_turno' => $state,
        ];
    }

    /** @return array<string, mixed> */
    private function budgetScenario(string $key, string $patent, int $dayOffset, string $time, string $reason, string $service, string $part, string $decision): array
    {
        return [
            ...$this->appointmentScenario($key, $patent, $dayOffset, $time, $reason, $decision === 'aprobado' ? 'confirmado' : 'pendiente'),
            'presupuesto' => [
                'servicio' => $service,
                'repuesto' => $part,
                'cantidad_repuesto' => 1,
                'observaciones' => 'Presupuesto ficticio y editable incluido para la demostración de AutoTech.',
            ],
            'decision' => $decision,
        ];
    }

    /** @return array<string, mixed> */
    private function repairScenario(string $key, string $patent, string $time, string $reason, string $service, string $part, string $employee, string $repairType, string $repairState, int $minutes, string $work): array
    {
        return [
            ...$this->budgetScenario($key, $patent, 0, $time, $reason, $service, $part, 'aprobado'),
            'estado_turno' => 'atendido',
            'orden' => [
                'empleado' => $employee,
                'estado' => $repairState === 'finalizada' ? 'finalizada' : 'en-proceso',
            ],
            'reparacion' => [
                'tipo' => $repairType,
                'estado' => $repairState,
                'minutos' => $minutes,
                'trabajos' => $work,
                'incidencias' => null,
            ],
        ];
    }

    private function priceToCents(string $amount): int
    {
        [$whole, $fraction] = array_pad(explode('.', $amount, 2), 2, '0');

        return ((int) $whole * 100) + (int) substr(str_pad($fraction, 2, '0'), 0, 2);
    }

    private function centsToDecimal(int $amount): string
    {
        return sprintf('%d.%02d', intdiv($amount, 100), $amount % 100);
    }
}
