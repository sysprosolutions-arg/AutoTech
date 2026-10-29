<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePresupuestoDecisionRequest;
use App\Models\AprobacionPresupuesto;
use App\Models\Empleado;
use App\Models\EstadoEmpleado;
use App\Models\EstadoOrdenTrabajo;
use App\Models\EstadoTurno;
use App\Models\OrdenTrabajo;
use App\Models\Presupuesto;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PresupuestoDecisionController extends Controller
{
    public function __invoke(StorePresupuestoDecisionRequest $request, Presupuesto $presupuesto): RedirectResponse
    {
        $user = $request->user();
        abort_unless($user instanceof User, 401);
        $data = $request->validated();

        $ordenTrabajo = DB::transaction(function () use ($data, $presupuesto, $user): ?OrdenTrabajo {
            $presupuesto = Presupuesto::query()
                ->with(['turno', 'detalles.tipoServicio'])
                ->lockForUpdate()
                ->findOrFail($presupuesto->id);
            $aprobacion = AprobacionPresupuesto::query()
                ->where('presupuesto_id', $presupuesto->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($aprobacion->estado !== 'pendiente') {
                throw ValidationException::withMessages([
                    'decision' => 'La decisión del cliente ya fue registrada y no puede modificarse.',
                ]);
            }

            if ($data['decision'] === 'rechazado') {
                $this->recordDecision($aprobacion, $data, $user);

                return null;
            }

            $especialidadIds = $presupuesto->detalles
                ->where('tipo', 'servicio')
                ->pluck('tipoServicio.especialidad_id')
                ->filter()
                ->unique();

            if ($especialidadIds->count() !== 1) {
                throw ValidationException::withMessages([
                    'decision' => 'No se pudo determinar una especialidad única para asignar la Orden de Trabajo.',
                ]);
            }

            $estadoActivo = EstadoEmpleado::query()->where('codigo', 'activo')->firstOrFail();
            $empleado = Empleado::query()
                ->where('especialidad_id', $especialidadIds->first())
                ->where('estado_empleado_id', $estadoActivo->id)
                ->orderBy('id')
                ->lockForUpdate()
                ->first();

            if (! $empleado instanceof Empleado) {
                throw ValidationException::withMessages([
                    'decision' => 'No hay un empleado activo con la especialidad requerida para emitir la Orden de Trabajo.',
                ]);
            }

            $estadoEmitida = EstadoOrdenTrabajo::query()->where('codigo', 'emitida')->firstOrFail();
            $ordenTrabajo = OrdenTrabajo::query()->create([
                'presupuesto_id' => $presupuesto->id,
                'turno_id' => $presupuesto->turno_id,
                'vehiculo_id' => $presupuesto->turno->vehiculo_id,
                'empleado_id' => $empleado->id,
                'estado_orden_trabajo_id' => $estadoEmitida->id,
                'emitida_por' => $user->id,
                'fecha_emision' => now(),
                'instrucciones' => $presupuesto->observaciones,
            ]);
            $ordenTrabajo->update(['numero' => sprintf('OT-%06d', $ordenTrabajo->id)]);
            $ordenTrabajo->detalles()->createMany($presupuesto->detalles->map(fn ($detalle): array => [
                'tipo_servicio_id' => $detalle->tipo_servicio_id,
                'repuesto_id' => $detalle->repuesto_id,
                'descripcion' => $detalle->descripcion,
                'cantidad' => $detalle->cantidad,
                'precio_referencia' => $detalle->precio_unitario,
            ])->all());

            $this->recordDecision($aprobacion, $data, $user);

            $estadoConfirmado = EstadoTurno::query()->where('codigo', 'confirmado')->first();

            if ($estadoConfirmado instanceof EstadoTurno) {
                $presupuesto->turno->update(['estado_turno_id' => $estadoConfirmado->id]);
            }

            return $ordenTrabajo;
        }, 3);

        $message = $ordenTrabajo instanceof OrdenTrabajo
            ? "Aprobación registrada. Se emitió automáticamente la {$ordenTrabajo->numero}."
            : 'Rechazo del cliente registrado. No se emitió una Orden de Trabajo.';

        return redirect()
            ->route('presupuestos.show', $presupuesto)
            ->with('status', $message);
    }

    /** @param array<string, mixed> $data */
    private function recordDecision(AprobacionPresupuesto $aprobacion, array $data, User $user): void
    {
        $aprobacion->update([
            'estado' => $data['decision'],
            'registrado_por' => $user->id,
            'fecha_decision' => $data['fecha_decision'],
            'medio_confirmacion' => $data['medio_confirmacion'],
            'observaciones' => $data['observaciones_decision'] ?? null,
        ]);
    }
}
