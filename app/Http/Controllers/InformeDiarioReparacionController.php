<?php

namespace App\Http\Controllers;

use App\Http\Requests\DailyReparacionReportRequest;
use App\Models\Reparacion;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\View\View;

class InformeDiarioReparacionController extends Controller
{
    public function __invoke(DailyReparacionReportRequest $request): View
    {
        $filters = $request->validated();
        $fecha = $filters['fecha'] ?? null;

        $reparaciones = $fecha === null
            ? new Collection
            : Reparacion::query()
                ->with([
                    'vehiculo.cliente',
                    'empleado.especialidad',
                    'tipo',
                    'estado',
                    'detalles.tipoServicio',
                    'detalles.repuesto',
                ])
                ->whereDate('fecha_inicio', $fecha)
                ->orderBy('fecha_inicio')
                ->orderBy('id')
                ->get();

        return view('reparaciones.reports.daily', [
            'fecha' => $fecha,
            'reparaciones' => $reparaciones,
            'totalMinutos' => $reparaciones->sum('tiempo_empleado_minutos'),
        ]);
    }
}
