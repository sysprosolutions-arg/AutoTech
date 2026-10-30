@extends('layouts.app')

@section('title', 'Repuestos por marca')
@section('context', 'Informes de repuestos')

@section('content')
<section class="mb-4">
    <div class="page-eyebrow">ATSA-39 · Reporte</div>
    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-end gap-3">
        <div>
            <h1 class="page-title">Repuestos por marca</h1>
            <p class="page-lead mb-0">Analizá las piezas activas según su fabricante.</p>
        </div>
        <a class="btn btn-outline-secondary" href="{{ route('repuestos.index') }}">Volver al catálogo</a>
    </div>
</section>

<form class="content-card mb-4 report-screen-actions" method="GET" action="{{ route('repuestos.reports.brands') }}">
    <div class="row g-3 align-items-end">
        <div class="col-md-8">
            <label class="form-label" for="marca">Marca</label>
            <select class="form-select" id="marca" name="marca">
                <option value="">Todas las marcas</option>
                @foreach ($marcas as $option)
                    <option value="{{ $option }}" @selected($marca === $option)>{{ $option }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-4 d-grid">
            <button class="btn btn-autotech" type="submit">Generar reporte</button>
        </div>
    </div>
</form>

@if ($repuestosPorMarca->isEmpty())
    <section class="content-card empty-state">
        <i class="bi bi-tags" aria-hidden="true"></i>
        <h2>No hay repuestos para la marca seleccionada</h2>
        <p>El reporte no incluye piezas de otras marcas.</p>
    </section>
@else
    <div class="d-flex justify-content-end mb-3 report-screen-actions">
        <button class="btn btn-outline-secondary" type="button" data-print-report>
            <i class="bi bi-printer me-2" aria-hidden="true"></i>Imprimir
        </button>
    </div>

    @foreach ($repuestosPorMarca as $nombreMarca => $repuestos)
        <section class="content-card mb-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h2 class="h4 fw-bold mb-0">{{ $nombreMarca }}</h2>
                <span class="badge text-bg-secondary">{{ $repuestos->count() }} repuestos</span>
            </div>
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Código</th>
                            <th>Repuesto</th>
                            <th>Tipo</th>
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
                                <td>{{ $repuesto->original ? 'Original' : 'Alternativo' }}</td>
                                <td>{{ $repuesto->proveedor?->razon_social ?? 'Sin proveedor' }}</td>
                                <td class="text-end">${{ number_format((float) $repuesto->precio_venta, 2, ',', '.') }}</td>
                                <td class="text-center">{{ $repuesto->stock }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </section>
    @endforeach
@endif
@endsection
