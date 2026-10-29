@extends('layouts.app')

@section('title', 'Consultar turnos')
@section('context', 'Gestión de turnos')

@section('content')
<section class="mb-4">
    <div class="page-eyebrow">ATSA-50 · Consulta</div>
    <div class="d-flex flex-column flex-lg-row align-items-lg-end justify-content-between gap-3">
        <div>
            <h1 class="page-title">Turnos</h1>
            <p class="page-lead mb-0">Consultá la agenda y accedé al detalle registrado de cada atención.</p>
        </div>
        <a class="btn btn-autotech px-4" href="{{ route('turnos.create') }}">
            <i class="bi bi-plus-lg me-2" aria-hidden="true"></i>Nuevo turno
        </a>
    </div>
</section>

<form class="content-card mb-4" method="GET" action="{{ route('turnos.index') }}">
    <div class="row g-3 align-items-end">
        <div class="col-lg-5">
            <label class="form-label" for="buscar">Buscar</label>
            <div class="input-group">
                <span class="input-group-text bg-white border-end-0"><i class="bi bi-search" aria-hidden="true"></i></span>
                <input class="form-control border-start-0 @error('buscar') is-invalid @enderror" id="buscar" name="buscar" type="search" value="{{ $filters['buscar'] ?? '' }}" placeholder="N.º, cliente, DNI o patente">
            </div>
            @error('buscar')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-5 col-lg-3">
            <label class="form-label" for="estado">Estado</label>
            <select class="form-select @error('estado') is-invalid @enderror" id="estado" name="estado">
                <option value="">Todos</option>
                @foreach ($estados as $estado)
                    <option value="{{ $estado->codigo }}" @selected(($filters['estado'] ?? '') === $estado->codigo)>{{ $estado->nombre }}</option>
                @endforeach
            </select>
            @error('estado')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-4 col-lg-2">
            <label class="form-label" for="fecha">Fecha</label>
            <input class="form-control @error('fecha') is-invalid @enderror" id="fecha" name="fecha" type="date" value="{{ $filters['fecha'] ?? '' }}">
            @error('fecha')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-3 col-lg-2 d-grid">
            <button class="btn btn-outline-secondary" type="submit">Aplicar</button>
        </div>
    </div>
    @if (array_filter($filters))
        <div class="mt-3">
            <a class="small" href="{{ route('turnos.index') }}">Limpiar filtros</a>
        </div>
    @endif
</form>

<section class="content-card" aria-labelledby="turnos-list-title">
    <div class="d-flex align-items-center justify-content-between mb-3">
        <div>
            <div class="page-eyebrow">Agenda</div>
            <h2 class="h5 fw-bold mt-1 mb-0" id="turnos-list-title">Resultados</h2>
        </div>
        <span class="text-secondary small">{{ $turnos->total() }} {{ $turnos->total() === 1 ? 'turno' : 'turnos' }}</span>
    </div>

    @if ($turnos->isEmpty())
        <div class="empty-state">
            <i class="bi bi-calendar-x" aria-hidden="true"></i>
            <h3>No se encontraron turnos</h3>
            <p>Probá con otros criterios o registrá una nueva atención.</p>
            <a class="btn btn-autotech" href="{{ route('turnos.create') }}">Registrar turno</a>
        </div>
    @else
        <div class="table-responsive">
            <table class="table align-middle table-hover mb-0">
                <thead>
                    <tr>
                        <th scope="col">N.º</th>
                        <th scope="col">Fecha y hora</th>
                        <th scope="col">Cliente</th>
                        <th scope="col">Vehículo</th>
                        <th scope="col">Estado</th>
                        <th class="text-end" scope="col">Acción</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($turnos as $turno)
                        <tr>
                            <td class="fw-semibold">#{{ $turno->id }}</td>
                            <td>
                                <strong class="d-block">{{ $turno->fecha->format('d/m/Y') }}</strong>
                                <small class="text-secondary">{{ substr((string) $turno->hora, 0, 5) }} h</small>
                            </td>
                            <td>
                                {{ $turno->cliente->apellido }}, {{ $turno->cliente->nombre }}
                                <small class="d-block text-secondary">DNI {{ $turno->cliente->dni }}</small>
                            </td>
                            <td>
                                <strong>{{ $turno->vehiculo->patente }}</strong>
                                <small class="d-block text-secondary">{{ $turno->vehiculo->marca }} {{ $turno->vehiculo->modelo }}</small>
                            </td>
                            <td><span class="state-badge state-{{ $turno->estado->codigo }}">{{ $turno->estado->nombre }}</span></td>
                            <td class="text-end">
                                <a class="btn btn-sm btn-outline-secondary" href="{{ route('turnos.show', $turno) }}">Ver detalle</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if ($turnos->hasPages())
            <div class="mt-4">{{ $turnos->links('pagination::bootstrap-5') }}</div>
        @endif
    @endif
</section>
@endsection
