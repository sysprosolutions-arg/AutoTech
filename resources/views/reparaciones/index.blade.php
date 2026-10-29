@extends('layouts.app')

@section('title', 'Consultar reparaciones')
@section('context', 'Gestión de reparaciones')

@section('content')
<section class="mb-4">
    <div class="page-eyebrow">ATSA-12 · Consulta</div>
    <div class="d-flex flex-column flex-lg-row align-items-lg-end justify-content-between gap-3">
        <div>
            <h1 class="page-title">Reparaciones</h1>
            <p class="page-lead mb-0">Consultá los trabajos registrados y su trazabilidad operativa.</p>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <a class="btn btn-outline-secondary" href="{{ route('reparaciones.reports.daily') }}"><i class="bi bi-file-earmark-bar-graph me-2" aria-hidden="true"></i>Informe diario</a>
            <a class="btn btn-autotech px-4" href="{{ route('reparaciones.create') }}"><i class="bi bi-plus-lg me-2" aria-hidden="true"></i>Nueva reparación</a>
        </div>
    </div>
</section>

<form class="content-card mb-4" method="GET" action="{{ route('reparaciones.index') }}">
    <div class="row g-3 align-items-end">
        <div class="col-lg-8">
            <label class="form-label" for="buscar">Buscar</label>
            <div class="input-group">
                <span class="input-group-text bg-white border-end-0"><i class="bi bi-search" aria-hidden="true"></i></span>
                <input class="form-control border-start-0 @error('buscar') is-invalid @enderror" id="buscar" name="buscar" type="search" value="{{ $filters['buscar'] ?? '' }}" placeholder="N.º de reparación, patente, marca o modelo">
            </div>
            @error('buscar')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-7 col-lg-2">
            <label class="form-label" for="estado">Estado</label>
            <select class="form-select @error('estado') is-invalid @enderror" id="estado" name="estado">
                <option value="">Todos</option>
                @foreach ($estados as $estado)
                    <option value="{{ $estado->codigo }}" @selected(($filters['estado'] ?? '') === $estado->codigo)>{{ $estado->nombre }}</option>
                @endforeach
            </select>
            @error('estado')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-5 col-lg-2 d-grid">
            <button class="btn btn-outline-secondary" type="submit">Buscar</button>
        </div>
    </div>
    @if (array_filter($filters))
        <div class="mt-3"><a class="small" href="{{ route('reparaciones.index') }}">Limpiar filtros</a></div>
    @endif
</form>

<section class="content-card" aria-labelledby="repairs-list-title">
    <div class="d-flex align-items-center justify-content-between mb-3">
        <div>
            <div class="page-eyebrow">Seguimiento</div>
            <h2 class="h5 fw-bold mt-1 mb-0" id="repairs-list-title">Resultados</h2>
        </div>
        <span class="text-secondary small">{{ $reparaciones->total() }} {{ $reparaciones->total() === 1 ? 'reparación' : 'reparaciones' }}</span>
    </div>

    @if ($reparaciones->isEmpty())
        <div class="empty-state">
            <i class="bi bi-tools" aria-hidden="true"></i>
            <h3>No se encontraron reparaciones</h3>
            <p>Probá con otro identificador o dato del vehículo.</p>
            <a class="btn btn-outline-secondary" href="{{ route('reparaciones.index') }}">Nueva búsqueda</a>
        </div>
    @else
        <div class="table-responsive">
            <table class="table align-middle table-hover mb-0">
                <thead>
                    <tr>
                        <th scope="col">Reparación</th>
                        <th scope="col">Inicio</th>
                        <th scope="col">Cliente y vehículo</th>
                        <th scope="col">Tipo</th>
                        <th scope="col">Mecánico</th>
                        <th scope="col">Estado</th>
                        <th class="text-end" scope="col">Acción</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($reparaciones as $reparacion)
                        <tr>
                            <td class="fw-semibold">{{ $reparacion->numero }}</td>
                            <td>{{ $reparacion->fecha_inicio->format('d/m/Y') }}<small class="d-block text-secondary">{{ $reparacion->fecha_inicio->format('H:i') }} h</small></td>
                            <td>
                                {{ $reparacion->vehiculo->cliente->apellido }}, {{ $reparacion->vehiculo->cliente->nombre }}
                                <small class="d-block text-secondary"><strong>{{ $reparacion->vehiculo->patente }}</strong> · {{ $reparacion->vehiculo->marca }} {{ $reparacion->vehiculo->modelo }}</small>
                            </td>
                            <td>{{ $reparacion->tipo->nombre }}</td>
                            <td>{{ $reparacion->empleado->apellido }}, {{ $reparacion->empleado->nombre }}</td>
                            <td><span class="state-badge state-{{ $reparacion->estado->codigo }}">{{ $reparacion->estado->nombre }}</span></td>
                            <td class="text-end"><a class="btn btn-sm btn-outline-secondary" href="{{ route('reparaciones.show', $reparacion) }}">Ver detalle</a></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if ($reparaciones->hasPages())
            <div class="mt-4">{{ $reparaciones->links('pagination::bootstrap-5') }}</div>
        @endif
    @endif
</section>
@endsection
