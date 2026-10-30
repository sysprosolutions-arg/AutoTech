<?php

namespace App\Http\Controllers;

use App\Http\Requests\RepuestoMarcaReportRequest;
use App\Models\Repuesto;
use Illuminate\View\View;

class RepuestoReportController extends Controller
{
    public function byBrand(RepuestoMarcaReportRequest $request): View
    {
        $filters = $request->validated();
        $marca = $filters['marca'] ?? null;
        $marcas = Repuesto::query()->where('activo', true)->distinct()->orderBy('marca')->pluck('marca');
        $repuestos = Repuesto::query()
            ->with('proveedor')
            ->where('activo', true)
            ->when($marca, fn ($query, string $marca) => $query->where('marca', $marca))
            ->orderBy('marca')
            ->orderBy('codigo')
            ->get();

        return view('repuestos.reports.brands', [
            'marca' => $marca,
            'marcas' => $marcas,
            'repuestosPorMarca' => $repuestos->groupBy('marca'),
        ]);
    }
}
