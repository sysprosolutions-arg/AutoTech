<?php

namespace App\Http\Controllers;

use App\Http\Requests\TurnoClientReportRequest;
use App\Models\Cliente;
use App\Models\Turno;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as SupportCollection;
use Illuminate\View\View;

class TurnoClientReportController extends Controller
{
    public function __invoke(TurnoClientReportRequest $request): View
    {
        $filters = $request->validated();
        $clientId = isset($filters['cliente_id']) ? (int) $filters['cliente_id'] : null;
        $today = CarbonImmutable::today();
        $from = $today->subYear();
        $client = $clientId === null ? null : Cliente::query()->findOrFail($clientId);
        $appointments = $clientId === null
            ? new Collection
            : Turno::query()
                ->with(['vehiculo', 'estado'])
                ->where('cliente_id', $clientId)
                ->whereDate('fecha', '>=', $from->toDateString())
                ->whereDate('fecha', '<=', $today->toDateString())
                ->orderByDesc('fecha')
                ->orderByDesc('hora')
                ->get();
        $appointmentsByMonth = $appointments->groupBy(fn (Turno $appointment): string => $appointment->fecha->format('Y-m'));
        $monthlyEvolution = collect(range(11, 0))->map(function (int $monthsAgo) use ($appointmentsByMonth, $today): array {
            $month = $today->startOfMonth()->subMonths($monthsAgo);

            return [
                'key' => $month->format('Y-m'),
                'label' => ucfirst($month->locale('es')->isoFormat('MMM YY')),
                'quantity' => $appointmentsByMonth->get($month->format('Y-m'), new SupportCollection)->count(),
            ];
        });
        $appointmentsByState = $appointments
            ->groupBy(fn (Turno $appointment): string => $appointment->estado->nombre)
            ->map->count()
            ->sortDesc();

        return view('turnos.reports.client', [
            'cliente' => $client,
            'clientes' => Cliente::query()->orderBy('apellido')->orderBy('nombre')->get(),
            'desde' => $from,
            'evolucionMensual' => $monthlyEvolution,
            'hasta' => $today,
            'maximoMensual' => (int) $monthlyEvolution->max('quantity'),
            'turnos' => $appointments,
            'turnosPorEstado' => $appointmentsByState,
        ]);
    }
}
