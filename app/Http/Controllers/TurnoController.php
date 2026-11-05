<?php

namespace App\Http\Controllers;

use App\Http\Requests\SearchTurnoRequest;
use App\Http\Requests\StoreTurnoRequest;
use App\Http\Requests\UpdateTurnoRequest;
use App\Models\Cliente;
use App\Models\EstadoTurno;
use App\Models\Turno;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class TurnoController extends Controller
{
    public function index(SearchTurnoRequest $request): View
    {
        $filters = $request->validated();
        $turnos = $this->filteredAppointments($filters)
            ->orderByDesc('fecha')
            ->orderByDesc('hora')
            ->paginate(10)
            ->withQueryString();

        return view('turnos.index', [
            'estados' => EstadoTurno::query()->orderBy('nombre')->get(),
            'filters' => $filters,
            'turnos' => $turnos,
        ]);
    }

    public function listing(SearchTurnoRequest $request): View
    {
        $filters = $request->validated();
        $turnos = $this->filteredAppointments($filters)
            ->orderBy('fecha')
            ->orderBy('hora')
            ->get();

        return view('turnos.list', [
            'filters' => $filters,
            'turnos' => $turnos,
        ]);
    }

    public function create(): View
    {
        $clientes = Cliente::query()
            ->where('activo', true)
            ->whereHas('vehiculos', fn ($query) => $query->where('activo', true))
            ->with(['vehiculos' => fn ($query) => $query
                ->where('activo', true)
                ->orderBy('patente')])
            ->orderBy('apellido')
            ->orderBy('nombre')
            ->get();

        return view('turnos.create', [
            'clientes' => $clientes,
        ]);
    }

    public function store(StoreTurnoRequest $request): RedirectResponse
    {
        $user = $request->user();
        abort_unless($user instanceof User, 401);

        $turno = DB::transaction(function () use ($request, $user): Turno {
            $estadoPendiente = EstadoTurno::query()
                ->where('codigo', 'pendiente')
                ->firstOrFail();

            return Turno::query()->create([
                ...$request->safe()->only([
                    'cliente_id',
                    'vehiculo_id',
                    'fecha',
                    'hora',
                    'motivo',
                    'observaciones',
                ]),
                'estado_turno_id' => $estadoPendiente->id,
                'registrado_por' => $user->id,
            ]);
        });

        return redirect()
            ->route('turnos.create')
            ->with('status', "Turno #{$turno->id} registrado correctamente.");
    }

    public function show(Turno $turno): View
    {
        $turno->load(['cliente', 'vehiculo', 'estado', 'registradoPor', 'presupuesto.aprobacion']);

        return view('turnos.show', [
            'turno' => $turno,
        ]);
    }

    public function edit(Turno $turno): View|RedirectResponse
    {
        $turno->load('estado');

        if (! $turno->canBeModified()) {
            return redirect()
                ->route('turnos.show', $turno)
                ->with('error', 'El turno no puede modificarse porque se encuentra finalizado o cancelado.');
        }

        $clientes = Cliente::query()
            ->where('activo', true)
            ->whereHas('vehiculos', fn ($query) => $query->where('activo', true))
            ->with(['vehiculos' => fn ($query) => $query
                ->where('activo', true)
                ->orderBy('patente')])
            ->orderBy('apellido')
            ->orderBy('nombre')
            ->get();

        return view('turnos.edit', [
            'clientes' => $clientes,
            'estados' => EstadoTurno::query()
                ->whereIn('codigo', $turno->allowedDestinationStateCodes())
                ->orderBy('nombre')
                ->get(),
            'turno' => $turno,
        ]);
    }

    public function update(UpdateTurnoRequest $request, Turno $turno): RedirectResponse
    {
        DB::transaction(function () use ($request, $turno): void {
            $turno->update($request->safe()->only([
                'cliente_id',
                'vehiculo_id',
                'estado_turno_id',
                'fecha',
                'hora',
                'motivo',
                'observaciones',
            ]));
        });

        return redirect()
            ->route('turnos.show', $turno)
            ->with('status', "Turno #{$turno->id} modificado correctamente.");
    }

    /** @param array<string, mixed> $filters */
    private function filteredAppointments(array $filters): Builder
    {
        $search = trim((string) ($filters['buscar'] ?? ''));

        return Turno::query()
            ->with(['cliente', 'vehiculo', 'estado'])
            ->when($search !== '', function (Builder $query) use ($search): void {
                $query->where(function (Builder $query) use ($search): void {
                    if (ctype_digit($search)) {
                        $query->orWhere('id', (int) $search);
                    }

                    $query
                        ->orWhereHas('cliente', fn (Builder $query): Builder => $query
                            ->where('nombre', 'like', "%{$search}%")
                            ->orWhere('apellido', 'like', "%{$search}%")
                            ->orWhere('dni', 'like', "%{$search}%"))
                        ->orWhereHas('vehiculo', fn (Builder $query): Builder => $query
                            ->where('patente', 'like', "%{$search}%")
                            ->orWhere('marca', 'like', "%{$search}%")
                            ->orWhere('modelo', 'like', "%{$search}%"));
                });
            })
            ->when(
                $filters['estado'] ?? null,
                fn (Builder $query, string $state): Builder => $query->whereHas(
                    'estado',
                    fn (Builder $query): Builder => $query->where('codigo', $state),
                ),
            )
            ->when(
                $filters['fecha'] ?? null,
                fn (Builder $query, string $date): Builder => $query->whereDate('fecha', $date),
            );
    }
}
