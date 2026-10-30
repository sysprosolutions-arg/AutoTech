<?php

namespace App\Http\Controllers;

use App\Http\Requests\RepuestoMarcaReportRequest;
use App\Http\Requests\RepuestoProveedorReportRequest;
use App\Models\Proveedor;
use App\Models\Repuesto;
use Illuminate\View\View;

class RepuestoReportController extends Controller
{
    public function originals(): View
    {
        $repuestos = Repuesto::query()
            ->with('proveedor')
            ->where('activo', true)
            ->where('original', true)
            ->orderBy('marca')
            ->orderBy('codigo')
            ->get();

        return view('repuestos.lists.originals', ['repuestos' => $repuestos]);
    }

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

    public function bySupplier(RepuestoProveedorReportRequest $request): View
    {
        $filters = $request->validated();
        $proveedorId = $filters['proveedor_id'] ?? null;
        $proveedores = Proveedor::query()
            ->where('activo', true)
            ->orderBy('razon_social')
            ->get();
        $repuestos = Repuesto::query()
            ->with('proveedor')
            ->where('activo', true)
            ->when($proveedorId, fn ($query, int $proveedorId) => $query->where('proveedor_id', $proveedorId))
            ->orderBy('codigo')
            ->get();

        return view('repuestos.reports.suppliers', [
            'proveedorId' => $proveedorId,
            'proveedores' => $proveedores,
            'repuestosPorProveedor' => $repuestos->groupBy(
                fn (Repuesto $repuesto): string => $repuesto->proveedor?->razon_social ?? 'Sin proveedor asignado',
            ),
        ]);
    }
}
