<?php

namespace App\Http\Controllers;

use App\Http\Requests\SearchReparacionRequest;
use App\Http\Requests\StoreReparacionRequest;
use App\Models\DetalleOrdenTrabajo;
use App\Models\EstadoOrdenTrabajo;
use App\Models\EstadoReparacion;
use App\Models\EstadoTurno;
use App\Models\OrdenTrabajo;
use App\Models\Reparacion;
use App\Models\Repuesto;
use App\Models\TipoReparacion;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class ReparacionController extends Controller
{
    public function index(SearchReparacionRequest $request): View
    {
        $filters = $request->validated();
        $buscar = trim((string) ($filters['buscar'] ?? ''));

        $reparaciones = Reparacion::query()
            ->with(['vehiculo.cliente', 'empleado.especialidad', 'tipo', 'estado'])
            ->when($buscar !== '', function ($query) use ($buscar): void {
                $query->where(function ($query) use ($buscar): void {
                    if (ctype_digit($buscar)) {
                        $query->orWhere('id', (int) $buscar);
                    }

                    $query
                        ->orWhere('numero', 'like', "%{$buscar}%")
                        ->orWhereHas('vehiculo', fn ($query) => $query
                            ->where('patente', 'like', "%{$buscar}%")
                            ->orWhere('marca', 'like', "%{$buscar}%")
                            ->orWhere('modelo', 'like', "%{$buscar}%"));
                });
            })
            ->when(
                $filters['estado'] ?? null,
                fn ($query, string $estado) => $query->whereHas(
                    'estado',
                    fn ($query) => $query->where('codigo', $estado),
                ),
            )
            ->orderByDesc('fecha_inicio')
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        return view('reparaciones.index', [
            'estados' => EstadoReparacion::query()->orderBy('nombre')->get(),
            'filters' => $filters,
            'reparaciones' => $reparaciones,
        ]);
    }

    public function create(): View
    {
        $ordenesTrabajo = OrdenTrabajo::query()
            ->whereHas('estado', fn ($query) => $query->where('codigo', 'emitida'))
            ->whereHas('presupuesto.aprobacion', fn ($query) => $query->where('estado', 'aprobado'))
            ->whereDoesntHave('reparacion')
            ->with(['turno.cliente', 'vehiculo', 'empleado.especialidad', 'estado'])
            ->orderBy('fecha_emision')
            ->get();

        return view('reparaciones.create', [
            'ordenesTrabajo' => $ordenesTrabajo,
        ]);
    }

    public function createFromOrder(OrdenTrabajo $ordenTrabajo): View|RedirectResponse
    {
        $ordenTrabajo->load([
            'presupuesto.aprobacion',
            'turno.cliente',
            'vehiculo',
            'empleado.especialidad',
            'estado',
            'detalles.tipoServicio',
            'detalles.repuesto',
            'reparacion',
        ]);

        if ($ordenTrabajo->reparacion instanceof Reparacion) {
            return redirect()->route('reparaciones.show', $ordenTrabajo->reparacion);
        }

        if ($ordenTrabajo->estado->codigo !== 'emitida' || $ordenTrabajo->presupuesto->aprobacion->estado !== 'aprobado') {
            return redirect()
                ->route('reparaciones.create')
                ->with('error', 'La reparación requiere una OT emitida desde un presupuesto aprobado.');
        }

        return view('reparaciones.form', [
            'ordenTrabajo' => $ordenTrabajo,
            'tiposReparacion' => TipoReparacion::query()->where('activo', true)->orderBy('nombre')->get(),
        ]);
    }

    public function store(StoreReparacionRequest $request): RedirectResponse
    {
        $user = $request->user();
        abort_unless($user instanceof User, 401);
        $data = $request->validated();

        $reparacion = DB::transaction(function () use ($data, $user): Reparacion {
            $ordenTrabajo = OrdenTrabajo::query()
                ->with(['presupuesto.aprobacion', 'turno', 'estado', 'detalles.tipoServicio', 'detalles.repuesto'])
                ->lockForUpdate()
                ->findOrFail((int) $data['orden_trabajo_id']);

            if ($ordenTrabajo->estado->codigo !== 'emitida' || $ordenTrabajo->presupuesto->aprobacion->estado !== 'aprobado') {
                throw ValidationException::withMessages([
                    'orden_trabajo_id' => 'Se requiere una OT emitida desde un presupuesto aprobado.',
                ]);
            }

            if (Reparacion::query()->where('orden_trabajo_id', $ordenTrabajo->id)->exists()) {
                throw ValidationException::withMessages([
                    'orden_trabajo_id' => 'La Orden de Trabajo ya tiene una reparación registrada.',
                ]);
            }

            $estadoEnProceso = EstadoReparacion::query()->where('codigo', 'en-proceso')->firstOrFail();
            $reparacion = Reparacion::query()->create([
                'orden_trabajo_id' => $ordenTrabajo->id,
                'vehiculo_id' => $ordenTrabajo->vehiculo_id,
                'empleado_id' => $ordenTrabajo->empleado_id,
                'tipo_reparacion_id' => $data['tipo_reparacion_id'],
                'estado_reparacion_id' => $estadoEnProceso->id,
                'registrado_por' => $user->id,
                'fecha_inicio' => now(),
                'tiempo_empleado_minutos' => $data['tiempo_empleado_minutos'],
                'trabajos_realizados' => $data['trabajos_realizados'],
                'incidencias' => $data['incidencias'] ?? null,
            ]);
            $reparacion->update(['numero' => sprintf('REP-%06d', $reparacion->id)]);

            $detalles = $ordenTrabajo->detalles
                ->whereNotNull('tipo_servicio_id')
                ->map(fn (DetalleOrdenTrabajo $detalle): array => [
                    'tipo' => 'servicio',
                    'tipo_servicio_id' => $detalle->tipo_servicio_id,
                    'repuesto_id' => null,
                    'descripcion' => $detalle->descripcion,
                    'cantidad' => $detalle->cantidad,
                ]);
            $repuestosOrdenados = $ordenTrabajo->detalles
                ->whereNotNull('repuesto_id')
                ->keyBy('id');

            foreach ($data['repuestos'] ?? [] as $item) {
                $detalleOrden = $repuestosOrdenados->get((int) $item['detalle_id']);

                if (! $detalleOrden instanceof DetalleOrdenTrabajo) {
                    throw ValidationException::withMessages([
                        'repuestos' => 'Uno de los repuestos no pertenece a la Orden de Trabajo.',
                    ]);
                }

                $repuesto = Repuesto::query()->lockForUpdate()->findOrFail($detalleOrden->repuesto_id);
                $cantidad = (int) $item['cantidad'];

                if ($cantidad > $detalleOrden->cantidad || $cantidad > $repuesto->stock) {
                    throw ValidationException::withMessages([
                        'repuestos' => "No hay disponibilidad suficiente de {$repuesto->nombre}.",
                    ]);
                }

                $repuesto->decrement('stock', $cantidad);
                $detalles->push([
                    'tipo' => 'repuesto',
                    'tipo_servicio_id' => null,
                    'repuesto_id' => $repuesto->id,
                    'descripcion' => $detalleOrden->descripcion,
                    'cantidad' => $cantidad,
                ]);
            }

            $reparacion->detalles()->createMany($detalles->all());

            $estadoOrdenEnProceso = EstadoOrdenTrabajo::query()->where('codigo', 'en-proceso')->firstOrFail();
            $ordenTrabajo->update(['estado_orden_trabajo_id' => $estadoOrdenEnProceso->id]);
            $estadoTurnoAtendido = EstadoTurno::query()->where('codigo', 'atendido')->first();

            if ($estadoTurnoAtendido instanceof EstadoTurno) {
                $ordenTrabajo->turno->update(['estado_turno_id' => $estadoTurnoAtendido->id]);
            }

            return $reparacion;
        }, 3);

        return redirect()
            ->route('reparaciones.show', $reparacion)
            ->with('status', "Reparación {$reparacion->numero} registrada correctamente.");
    }

    public function show(Reparacion $reparacion): View
    {
        $reparacion->load([
            'ordenTrabajo.presupuesto',
            'ordenTrabajo.estado',
            'vehiculo.cliente',
            'empleado.especialidad',
            'tipo',
            'estado',
            'registradoPor',
            'detalles.tipoServicio',
            'detalles.repuesto',
        ]);

        return view('reparaciones.show', ['reparacion' => $reparacion]);
    }

    public function finish(Reparacion $reparacion): RedirectResponse
    {
        DB::transaction(function () use ($reparacion): void {
            $reparacion = Reparacion::query()
                ->with(['estado', 'ordenTrabajo'])
                ->lockForUpdate()
                ->findOrFail($reparacion->id);

            if ($reparacion->estado->codigo !== 'en-proceso') {
                throw ValidationException::withMessages([
                    'estado' => 'La reparación ya fue finalizada o no se encuentra en proceso.',
                ]);
            }

            $estadoFinalizada = EstadoReparacion::query()->where('codigo', 'finalizada')->firstOrFail();
            $estadoOrdenFinalizada = EstadoOrdenTrabajo::query()->where('codigo', 'finalizada')->firstOrFail();

            $reparacion->update([
                'estado_reparacion_id' => $estadoFinalizada->id,
                'fecha_fin' => now(),
            ]);
            $reparacion->ordenTrabajo->update([
                'estado_orden_trabajo_id' => $estadoOrdenFinalizada->id,
            ]);
        }, 3);

        return redirect()
            ->route('reparaciones.show', $reparacion)
            ->with('status', "Reparación {$reparacion->numero} finalizada correctamente.");
    }
}
