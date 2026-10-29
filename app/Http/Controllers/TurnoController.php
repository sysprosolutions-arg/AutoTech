<?php

namespace App\Http\Controllers;

use App\Http\Requests\SearchTurnoRequest;
use App\Http\Requests\StoreTurnoRequest;
use App\Models\Cliente;
use App\Models\EstadoTurno;
use App\Models\Turno;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class TurnoController extends Controller
{
    public function index(SearchTurnoRequest $request): View
    {
        $filters = $request->validated();
        $buscar = trim((string) ($filters['buscar'] ?? ''));

        $turnos = Turno::query()
            ->with(['cliente', 'vehiculo', 'estado'])
            ->when($buscar !== '', function ($query) use ($buscar): void {
                $query->where(function ($query) use ($buscar): void {
                    if (ctype_digit($buscar)) {
                        $query->orWhere('id', (int) $buscar);
                    }

                    $query
                        ->orWhereHas('cliente', fn ($query) => $query
                            ->where('nombre', 'like', "%{$buscar}%")
                            ->orWhere('apellido', 'like', "%{$buscar}%")
                            ->orWhere('dni', 'like', "%{$buscar}%"))
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
            ->when(
                $filters['fecha'] ?? null,
                fn ($query, string $fecha) => $query->whereDate('fecha', $fecha),
            )
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
        $turno->load(['cliente', 'vehiculo', 'estado', 'registradoPor']);

        return view('turnos.show', [
            'turno' => $turno,
        ]);
    }
}
