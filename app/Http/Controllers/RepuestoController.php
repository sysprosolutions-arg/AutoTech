<?php

namespace App\Http\Controllers;

use App\Http\Requests\SearchRepuestoRequest;
use App\Http\Requests\StoreRepuestoRequest;
use App\Models\Proveedor;
use App\Models\Repuesto;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class RepuestoController extends Controller
{
    public function index(SearchRepuestoRequest $request): View
    {
        $filters = $request->validated();
        $buscar = trim((string) ($filters['buscar'] ?? ''));

        $repuestos = Repuesto::query()
            ->with('proveedor')
            ->when($buscar !== '', function ($query) use ($buscar): void {
                $query->where(function ($query) use ($buscar): void {
                    if (ctype_digit($buscar)) {
                        $query->orWhere('id', (int) $buscar);
                    }

                    $query
                        ->orWhere('codigo', 'like', "%{$buscar}%")
                        ->orWhere('nombre', 'like', "%{$buscar}%")
                        ->orWhere('marca', 'like', "%{$buscar}%")
                        ->orWhereHas('proveedor', fn ($query) => $query
                            ->where('razon_social', 'like', "%{$buscar}%")
                            ->orWhere('nombre_fantasia', 'like', "%{$buscar}%"));
                });
            })
            ->when(
                $filters['tipo'] ?? null,
                fn ($query, string $tipo) => $query->where('original', $tipo === 'original'),
            )
            ->when(
                $filters['estado'] ?? null,
                fn ($query, string $estado) => $query->where('activo', $estado === 'activo'),
            )
            ->orderBy('codigo')
            ->paginate(10)
            ->withQueryString();

        return view('repuestos.index', [
            'filters' => $filters,
            'repuestos' => $repuestos,
        ]);
    }

    public function create(): View
    {
        return view('repuestos.create', [
            'proveedores' => Proveedor::query()
                ->where('activo', true)
                ->orderBy('razon_social')
                ->get(),
        ]);
    }

    public function store(StoreRepuestoRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $repuesto = DB::transaction(fn (): Repuesto => Repuesto::query()->create([
            'proveedor_id' => $data['proveedor_id'],
            'codigo' => $data['codigo'],
            'nombre' => $data['nombre'],
            'descripcion' => $data['descripcion'],
            'marca' => $data['marca'],
            'original' => $data['tipo'] === 'original',
            'precio_venta' => $data['precio_venta'],
            'stock' => $data['stock'],
            'activo' => true,
        ]));

        return redirect()
            ->route('repuestos.create')
            ->with('status', "Repuesto {$repuesto->codigo} registrado correctamente.");
    }

    public function show(Repuesto $repuesto): View
    {
        $repuesto->load('proveedor');

        return view('repuestos.show', [
            'repuesto' => $repuesto,
        ]);
    }
}
