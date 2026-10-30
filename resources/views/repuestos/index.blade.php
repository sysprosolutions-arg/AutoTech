@extends('layouts.app')

@section('title', 'Consultar repuestos')
@section('context', 'Gestión de repuestos')

@section('content')
<section class="mb-4">
    <div class="page-eyebrow">ATSA-37 · Consulta</div>
    <div class="d-flex flex-column flex-lg-row align-items-lg-end justify-content-between gap-3">
        <div>
            <h1 class="page-title">Repuestos</h1>
            <p class="page-lead mb-0">Consultá las piezas del catálogo, sus proveedores y la disponibilidad actual.</p>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <a class="btn btn-outline-secondary" href="{{ route('repuestos.lists.alternatives') }}">
                <i class="bi bi-tools me-2" aria-hidden="true"></i>Repuestos alternativos
            </a>
            <a class="btn btn-outline-secondary" href="{{ route('repuestos.lists.originals') }}">
                <i class="bi bi-patch-check me-2" aria-hidden="true"></i>Repuestos originales
            </a>
            <a class="btn btn-outline-secondary" href="{{ route('repuestos.reports.brands') }}">
                <i class="bi bi-bar-chart me-2" aria-hidden="true"></i>Reporte por marca
            </a>
            <a class="btn btn-outline-secondary" href="{{ route('repuestos.reports.suppliers') }}">
                <i class="bi bi-truck me-2" aria-hidden="true"></i>Reporte por proveedor
            </a>
            <a class="btn btn-autotech" href="{{ route('repuestos.create') }}">
                <i class="bi bi-plus-lg me-2" aria-hidden="true"></i>Registrar repuesto
            </a>
        </div>
    </div>
</section>

@if (session('status'))
    <div class="alert alert-success" role="status"><i class="bi bi-check-circle me-2" aria-hidden="true"></i>{{ session('status') }}</div>
@endif

<form class="content-card mb-4" method="GET" action="{{ route('repuestos.index') }}">
    <div class="row g-3 align-items-end">
        <div class="col-lg-6">
            <label class="form-label" for="buscar">Código, nombre, marca o proveedor</label>
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-search" aria-hidden="true"></i></span>
                <input class="form-control @error('buscar') is-invalid @enderror" id="buscar" name="buscar" type="search" maxlength="100" value="{{ $filters['buscar'] ?? '' }}" placeholder="Ej.: REP-FIL-001, Bosch...">
                @error('buscar')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        </div>
        <div class="col-md-4 col-lg-2">
            <label class="form-label" for="tipo">Tipo</label>
            <select class="form-select" id="tipo" name="tipo">
                <option value="">Todos</option>
                <option value="original" @selected(($filters['tipo'] ?? '') === 'original')>Original</option>
                <option value="alternativo" @selected(($filters['tipo'] ?? '') === 'alternativo')>Alternativo</option>
            </select>
        </div>
        <div class="col-md-4 col-lg-2">
            <label class="form-label" for="estado">Estado</label>
            <select class="form-select" id="estado" name="estado">
                <option value="">Todos</option>
                <option value="activo" @selected(($filters['estado'] ?? '') === 'activo')>Activo</option>
                <option value="inactivo" @selected(($filters['estado'] ?? '') === 'inactivo')>Inactivo</option>
            </select>
        </div>
        <div class="col-md-4 col-lg-2 d-grid"><button class="btn btn-outline-secondary" type="submit">Buscar</button></div>
    </div>
</form>

@if ($repuestos->isEmpty())
    <section class="content-card empty-state" aria-live="polite">
        <i class="bi bi-search" aria-hidden="true"></i>
        <h2>No se encontraron repuestos</h2>
        <p>No existen coincidencias para los criterios ingresados.</p>
        <a class="btn btn-outline-secondary" href="{{ route('repuestos.index') }}">Nueva búsqueda</a>
    </section>
@else
    <section class="content-card p-0 overflow-hidden" aria-label="Resultados de repuestos">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead><tr><th>Código y repuesto</th><th>Tipo</th><th>Proveedor</th><th class="text-end">Precio</th><th class="text-center">Stock</th><th>Estado</th><th><span class="visually-hidden">Acciones</span></th></tr></thead>
                <tbody>
                    @foreach ($repuestos as $repuesto)
                        <tr>
                            <td><strong>{{ $repuesto->codigo }}</strong><small class="d-block text-secondary">{{ $repuesto->nombre }} · {{ $repuesto->marca }}</small></td>
                            <td>{{ $repuesto->original ? 'Original' : 'Alternativo' }}</td>
                            <td>{{ $repuesto->proveedor?->razon_social ?? 'Sin proveedor asignado' }}</td>
                            <td class="text-end">${{ number_format((float) $repuesto->precio_venta, 2, ',', '.') }}</td>
                            <td class="text-center"><span class="badge text-bg-{{ $repuesto->stock > 0 ? 'success' : 'danger' }}">{{ $repuesto->stock }}</span></td>
                            <td><span class="state-badge state-{{ $repuesto->activo ? 'confirmado' : 'cancelado' }}">{{ $repuesto->activo ? 'Activo' : 'Inactivo' }}</span></td>
                            <td class="text-end"><a class="btn btn-sm btn-outline-secondary" href="{{ route('repuestos.show', $repuesto) }}">Ver detalle</a></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </section>

    <div class="mt-4">{{ $repuestos->links() }}</div>
@endif
@endsection
