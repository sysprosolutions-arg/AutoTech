<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRepuestoRequest;
use App\Models\Proveedor;
use App\Models\Repuesto;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class RepuestoController extends Controller
{
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
}
