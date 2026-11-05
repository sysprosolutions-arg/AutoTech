<?php

namespace App\Http\Controllers;

use App\Http\Requests\ReparacionTypeReportRequest;
use App\Models\TipoReparacion;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class ReparacionTypeReportController extends Controller
{
    public function __invoke(ReparacionTypeReportRequest $request): View
    {
        $filters = $request->validated();
        $from = $filters['desde'] ?? null;
        $to = $filters['hasta'] ?? null;

        $repairTypes = $from === null || $to === null
            ? new Collection
            : TipoReparacion::query()
                ->withCount(['reparaciones' => function (Builder $query) use ($from, $to): void {
                    $query->whereBetween('fecha_inicio', [
                        CarbonImmutable::parse($from)->startOfDay(),
                        CarbonImmutable::parse($to)->endOfDay(),
                    ]);
                }])
                ->get()
                ->filter(fn (TipoReparacion $type): bool => $type->reparaciones_count > 0)
                ->sortByDesc('reparaciones_count')
                ->values();
        $totalRepairs = $repairTypes->sum('reparaciones_count');
        $maximumQuantity = (int) $repairTypes->max('reparaciones_count');

        return view('reparaciones.reports.types', [
            'desde' => $from,
            'hasta' => $to,
            'maximaCantidad' => $maximumQuantity,
            'tiposReparacion' => $repairTypes,
            'totalReparaciones' => $totalRepairs,
        ]);
    }
}
