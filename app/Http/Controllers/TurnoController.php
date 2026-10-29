<?php

namespace App\Http\Controllers;

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
}
