<?php

namespace App\Http\Controllers;

use App\Http\Requests\SearchRepuestoRequest;
use App\Http\Requests\StoreRepuestoRequest;
use App\Http\Requests\UpdateRepuestoRequest;
use App\Http\Requests\UpdateRepuestoStockRequest;
use App\Models\MovimientoStock;
use App\Models\Proveedor;
use App\Models\Repuesto;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
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

    public function edit(Repuesto $repuesto): View
    {
        abort_unless($repuesto->activo, 404);

        return view('repuestos.edit', [
            'proveedores' => Proveedor::query()
                ->where(function ($query) use ($repuesto): void {
                    $query->where('activo', true);

                    if ($repuesto->proveedor_id !== null) {
                        $query->orWhereKey($repuesto->proveedor_id);
                    }
                })
                ->orderBy('razon_social')
                ->get(),
            'repuesto' => $repuesto,
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

    public function stock(Repuesto $repuesto): View
    {
        abort_unless($repuesto->activo, 404);

        $repuesto->load(['movimientosStock' => fn ($query) => $query->with('registradoPor')->latest()->limit(20)]);

        return view('repuestos.stock', ['repuesto' => $repuesto]);
    }

    public function show(Repuesto $repuesto): View
    {
        $repuesto->load('proveedor');

        return view('repuestos.show', [
            'repuesto' => $repuesto,
        ]);
    }

    public function update(UpdateRepuestoRequest $request, Repuesto $repuesto): RedirectResponse
    {
        abort_unless($repuesto->activo, 404);

        $data = $request->validated();

        DB::transaction(function () use ($data, $repuesto): void {
            $repuesto->update([
                'proveedor_id' => $data['proveedor_id'],
                'codigo' => $data['codigo'],
                'nombre' => $data['nombre'],
                'descripcion' => $data['descripcion'],
                'marca' => $data['marca'],
                'original' => $data['tipo'] === 'original',
                'precio_venta' => $data['precio_venta'],
            ]);
        });

        return redirect()
            ->route('repuestos.show', $repuesto)
            ->with('status', "Repuesto {$repuesto->codigo} actualizado correctamente.");
    }

    public function updateStock(UpdateRepuestoStockRequest $request, Repuesto $repuesto): RedirectResponse
    {
        abort_unless($repuesto->activo, 404);

        $data = $request->validated();
        $user = $request->user();
        abort_unless($user instanceof User, 401);

        DB::transaction(function () use ($data, $repuesto, $user): void {
            $lockedRepuesto = Repuesto::query()->lockForUpdate()->findOrFail($repuesto->id);
            $cantidad = (int) $data['cantidad'];
            $stockResultante = $data['operacion'] === 'entrada'
                ? $lockedRepuesto->stock + $cantidad
                : $lockedRepuesto->stock - $cantidad;

            if ($stockResultante < 0) {
                throw ValidationException::withMessages(['cantidad' => 'La salida supera el stock disponible.']);
            }

            MovimientoStock::query()->create([
                'repuesto_id' => $lockedRepuesto->id,
                'registrado_por' => $user->id,
                'operacion' => $data['operacion'],
                'cantidad' => $cantidad,
                'stock_anterior' => $lockedRepuesto->stock,
                'stock_resultante' => $stockResultante,
                'motivo' => $data['motivo'],
            ]);
            $lockedRepuesto->update(['stock' => $stockResultante]);
        }, 3);

        return redirect()->route('repuestos.stock', $repuesto)->with('status', 'Stock actualizado correctamente.');
    }

    public function destroy(Request $request, Repuesto $repuesto): RedirectResponse
    {
        $request->validate(
            ['confirmar' => ['accepted']],
            ['confirmar.accepted' => 'Confirmá la baja del repuesto para continuar.'],
        );

        DB::transaction(function () use ($repuesto): void {
            $lockedRepuesto = Repuesto::query()->lockForUpdate()->findOrFail($repuesto->id);

            if (! $lockedRepuesto->activo) {
                throw ValidationException::withMessages(['repuesto' => 'El repuesto ya se encuentra inactivo.']);
            }

            if ($lockedRepuesto->stock > 0) {
                throw ValidationException::withMessages(['repuesto' => 'No se puede dar de baja un repuesto con existencias disponibles.']);
            }

            if ($lockedRepuesto->detallesPresupuesto()->exists()
                || $lockedRepuesto->detallesOrdenTrabajo()->exists()
                || $lockedRepuesto->detallesReparacion()->exists()) {
                throw ValidationException::withMessages(['repuesto' => 'El repuesto posee operaciones relacionadas y debe conservarse activo.']);
            }

            $lockedRepuesto->update(['activo' => false]);
        }, 3);

        return redirect()->route('repuestos.index')->with('status', "Repuesto {$repuesto->codigo} dado de baja correctamente.");
    }
}
