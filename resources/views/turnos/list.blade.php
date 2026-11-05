@extends('layouts.app')

@section('title', 'Listado de turnos')
@section('context', 'Gestión de turnos')

@section('content')
<section class="mb-4 report-screen-actions">
    <div class="page-eyebrow">Agenda del taller · Listado operativo</div>
    <div class="d-flex flex-column flex-lg-row align-items-lg-end justify-content-between gap-3">
        <div>
            <h1 class="page-title">Listado de turnos</h1>
            <p class="page-lead mb-0">Información consolidada de los turnos que cumplen los criterios seleccionados.</p>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <button class="btn btn-autotech" type="button" data-print-report><i class="bi bi-printer me-2" aria-hidden="true"></i>Imprimir listado</button>
            <a class="btn btn-outline-secondary" href="{{ route('turnos.index', $filters) }}"><i class="bi bi-arrow-left me-2" aria-hidden="true"></i>Volver a turnos</a>
        </div>
    </div>
</section>

<article class="content-card" aria-labelledby="appointment-list-title">
    <header class="daily-report-header mb-4">
        <div>
            <div class="page-eyebrow">AutoTech S.A.</div>
            <h2 class="h4 fw-bold mb-1" id="appointment-list-title">Agenda de turnos</h2>
            <p class="text-secondary mb-0">Emitido el {{ now()->format('d/m/Y H:i') }}.</p>
        </div>
        <div class="report-metric"><span>Total</span><strong>{{ $turnos->count() }}</strong></div>
    </header>

    @if (array_filter($filters))
        <div class="alert alert-light border mb-4">
            <strong>Filtros aplicados:</strong>
            @if ($filters['buscar'] ?? null)<span class="ms-2">Búsqueda “{{ $filters['buscar'] }}”</span>@endif
            @if ($filters['estado'] ?? null)<span class="ms-2">Estado {{ ucfirst($filters['estado']) }}</span>@endif
            @if ($filters['fecha'] ?? null)<span class="ms-2">Fecha {{ \Illuminate\Support\Carbon::parse($filters['fecha'])->format('d/m/Y') }}</span>@endif
        </div>
    @endif

    @if ($turnos->isEmpty())
        <div class="empty-state" aria-live="polite">
            <i class="bi bi-calendar-x" aria-hidden="true"></i>
            <h3>No existen turnos para emitir</h3>
            <p>No se encontraron registros que coincidan con los criterios seleccionados.</p>
        </div>
    @else
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead><tr><th>N.º</th><th>Fecha</th><th>Hora</th><th>Cliente</th><th>Vehículo</th><th>Motivo</th><th>Estado</th></tr></thead>
                <tbody>
                    @foreach ($turnos as $turno)
                        <tr>
                            <td class="fw-semibold">#{{ $turno->id }}</td>
                            <td>{{ $turno->fecha->format('d/m/Y') }}</td>
                            <td>{{ substr((string) $turno->hora, 0, 5) }} h</td>
                            <td>{{ $turno->cliente->apellido }}, {{ $turno->cliente->nombre }}<small class="d-block text-secondary">DNI {{ $turno->cliente->dni }}</small></td>
                            <td><strong>{{ $turno->vehiculo->patente }}</strong><small class="d-block text-secondary">{{ $turno->vehiculo->marca }} {{ $turno->vehiculo->modelo }}</small></td>
                            <td>{{ $turno->motivo }}</td>
                            <td><span class="state-badge state-{{ $turno->estado->codigo }}">{{ $turno->estado->nombre }}</span></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</article>
@endsection
