@extends('layouts.app')

@section('title', 'Repuestos originales')
@section('context', 'Listados de repuestos')

@section('content')
<section class="mb-4">
    <div class="page-eyebrow">ATSA-41 · Listado</div>
    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-end gap-3">
        <div>
            <h1 class="page-title">Repuestos originales</h1>
            <p class="page-lead mb-0">Piezas activas provistas o avaladas por el fabricante del vehículo.</p>
        </div>
        <div class="d-flex gap-2 report-screen-actions">
            <button class="btn btn-outline-secondary" type="button" data-print-report>
                <i class="bi bi-printer me-2" aria-hidden="true"></i>Imprimir
            </button>
            <a class="btn btn-outline-secondary" href="{{ route('repuestos.index') }}">Volver al catálogo</a>
        </div>
    </div>
</section>

@if ($repuestos->isEmpty())
    <section class="content-card empty-state">
        <i class="bi bi-patch-check" aria-hidden="true"></i>
        <h2>No hay repuestos originales activos</h2>
        <p>El listado se actualizará cuando se registre una pieza original.</p>
    </section>
@else
    <section class="content-card p-0 overflow-hidden" aria-label="Listado de repuestos originales">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead>
                    <tr>
                        <th>Código</th>
                        <th>Repuesto</th>
                        <th>Marca</th>
                        <th>Proveedor</th>
                        <th class="text-end">Precio</th>
                        <th class="text-center">Stock</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($repuestos as $repuesto)
                        <tr>
                            <td><strong>{{ $repuesto->codigo }}</strong></td>
                            <td>{{ $repuesto->nombre }}</td>
                            <td>{{ $repuesto->marca }}</td>
                            <td>{{ $repuesto->proveedor?->razon_social ?? 'Sin proveedor asignado' }}</td>
                            <td class="text-end">${{ number_format((float) $repuesto->precio_venta, 2, ',', '.') }}</td>
                            <td class="text-center">{{ $repuesto->stock }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </section>
@endif
@endsection
