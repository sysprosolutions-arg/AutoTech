@extends('layouts.app')

@section('title', 'Repuestos por proveedor')
@section('context', 'Informes de repuestos')

@section('content')
<section class="mb-4">
    <div class="page-eyebrow">ATSA-40 · Reporte</div>
    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-end gap-3">
        <div>
            <h1 class="page-title">Repuestos por proveedor</h1>
            <p class="page-lead mb-0">Consultá las piezas activas según la empresa que las suministra.</p>
        </div>
        <a class="btn btn-outline-secondary" href="{{ route('repuestos.index') }}">Volver al catálogo</a>
    </div>
</section>

<form class="content-card mb-4 report-screen-actions" method="GET" action="{{ route('repuestos.reports.suppliers') }}">
    <div class="row g-3 align-items-end">
        <div class="col-md-8">
            <label class="form-label" for="proveedor_id">Proveedor</label>
            <select class="form-select @error('proveedor_id') is-invalid @enderror" id="proveedor_id" name="proveedor_id">
                <option value="">Todos los proveedores</option>
                @foreach ($proveedores as $proveedor)
                    <option value="{{ $proveedor->id }}" @selected((int) $proveedorId === $proveedor->id)>
                        {{ $proveedor->razon_social }}
                    </option>
                @endforeach
            </select>
            @error('proveedor_id')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <div class="col-md-4 d-grid">
            <button class="btn btn-autotech" type="submit">Generar reporte</button>
        </div>
    </div>
</form>

@if ($repuestosPorProveedor->isEmpty())
    <section class="content-card empty-state">
        <i class="bi bi-truck" aria-hidden="true"></i>
        <h2>No hay repuestos para el proveedor seleccionado</h2>
        <p>El reporte no incluye piezas de otros proveedores.</p>
    </section>
@else
    <div class="d-flex justify-content-end mb-3 report-screen-actions">
        <button class="btn btn-outline-secondary" type="button" data-print-report>
            <i class="bi bi-printer me-2" aria-hidden="true"></i>Imprimir
        </button>
    </div>

    @foreach ($repuestosPorProveedor as $nombreProveedor => $repuestos)
        <section class="content-card mb-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h2 class="h4 fw-bold mb-0">{{ $nombreProveedor }}</h2>
                <span class="badge text-bg-secondary">{{ $repuestos->count() }} repuestos</span>
            </div>
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Código</th>
                            <th>Repuesto</th>
                            <th>Marca</th>
                            <th>Tipo</th>
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
                                <td>{{ $repuesto->original ? 'Original' : 'Alternativo' }}</td>
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
