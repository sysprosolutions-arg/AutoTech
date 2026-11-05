@extends('layouts.app')

@section('title', 'Turnos por cliente')
@section('context', 'Reportes de turnos')

@section('content')
<section class="mb-4 report-screen-actions">
    <div class="page-eyebrow">Historial de atención · Reporte táctico</div>
    <div class="d-flex flex-column flex-lg-row align-items-lg-end justify-content-between gap-3">
        <div>
            <h1 class="page-title">Turnos por cliente</h1>
            <p class="page-lead mb-0">Analizá exclusivamente los turnos del cliente seleccionado durante el último año.</p>
        </div>
        <a class="btn btn-outline-secondary" href="{{ route('turnos.index') }}"><i class="bi bi-arrow-left me-2" aria-hidden="true"></i>Volver a turnos</a>
    </div>
</section>

<form class="content-card mb-4 report-screen-actions" method="GET" action="{{ route('turnos.reports.client') }}">
    <div class="row g-3 align-items-end">
        <div class="col-lg-8">
            <label class="form-label" for="cliente_id">Cliente <span class="required-mark">*</span></label>
            <select class="form-select @error('cliente_id') is-invalid @enderror" id="cliente_id" name="cliente_id" required>
                <option value="">Seleccionar cliente</option>
                @foreach ($clientes as $option)
                    <option value="{{ $option->id }}" @selected($cliente?->id === $option->id)>{{ $option->apellido }}, {{ $option->nombre }} · DNI {{ $option->dni }}</option>
                @endforeach
            </select>
            @error('cliente_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-lg-4 d-grid"><button class="btn btn-autotech" type="submit"><i class="bi bi-graph-up me-2" aria-hidden="true"></i>Generar reporte</button></div>
    </div>
</form>

@if ($cliente === null)
    <section class="content-card empty-state" aria-live="polite">
        <i class="bi bi-person-check" aria-hidden="true"></i>
        <h2>Seleccioná un cliente</h2>
        <p>El reporte mostrará únicamente sus turnos comprendidos entre {{ $desde->format('d/m/Y') }} y {{ $hasta->format('d/m/Y') }}.</p>
    </section>
@elseif ($turnos->isEmpty())
    <section class="content-card empty-state" aria-live="polite">
        <i class="bi bi-calendar-x" aria-hidden="true"></i>
        <h2>El cliente no posee turnos en el último año</h2>
        <p>No existen registros para {{ $cliente->nombre }} {{ $cliente->apellido }} dentro del período consultado.</p>
    </section>
@else
    <article aria-labelledby="client-appointments-report-title">
        <header class="content-card daily-report-header mb-4">
            <div>
                <div class="page-eyebrow">AutoTech S.A. · Historial del cliente</div>
                <h2 class="h3 fw-bold mb-1" id="client-appointments-report-title">{{ $cliente->apellido }}, {{ $cliente->nombre }}</h2>
                <p class="text-secondary mb-0">DNI {{ $cliente->dni }} · Del {{ $desde->format('d/m/Y') }} al {{ $hasta->format('d/m/Y') }}</p>
            </div>
            <div class="d-flex align-items-center gap-4 flex-wrap">
                <div class="report-metric"><span>Total turnos</span><strong>{{ $turnos->count() }}</strong></div>
                <div class="report-metric"><span>Vehículos atendidos</span><strong>{{ $turnos->pluck('vehiculo_id')->unique()->count() }}</strong></div>
                <button class="btn btn-outline-secondary report-screen-actions" type="button" data-print-report><i class="bi bi-printer me-2" aria-hidden="true"></i>Imprimir</button>
            </div>
        </header>

        <div class="row g-4 mb-4">
            <div class="col-xl-8">
                <section class="content-card h-100" aria-labelledby="monthly-evolution-title">
                    <div class="page-eyebrow">Evolución mensual</div>
                    <h3 class="h5 fw-bold mt-1 mb-4" id="monthly-evolution-title">Turnos registrados en los últimos 12 meses</h3>
                    <div class="monthly-chart" role="img" aria-label="Gráfico mensual de turnos del cliente">
                        @foreach ($evolucionMensual as $month)
                            @php($height = $maximoMensual > 0 ? max(4, round(($month['quantity'] / $maximoMensual) * 100, 1)) : 0)
                            <div class="monthly-column">
                                <strong>{{ $month['quantity'] }}</strong>
                                <div class="monthly-bar-track" aria-hidden="true"><span style="height: {{ $height }}%"></span></div>
                                <small>{{ $month['label'] }}</small>
                            </div>
                        @endforeach
                    </div>
                </section>
            </div>
            <div class="col-xl-4">
                <section class="content-card h-100" aria-labelledby="state-distribution-title">
                    <div class="page-eyebrow">Situación</div>
                    <h3 class="h5 fw-bold mt-1 mb-3" id="state-distribution-title">Distribución por estado</h3>
                    <div class="report-state-list">
                        @foreach ($turnosPorEstado as $state => $quantity)
                            <div><span>{{ $state }}</span><strong>{{ $quantity }}</strong></div>
                        @endforeach
                    </div>
                </section>
            </div>
        </div>

        <section class="content-card" aria-labelledby="client-appointments-table-title">
            <div class="page-eyebrow">Detalle operativo</div>
            <h3 class="h5 fw-bold mt-1 mb-3" id="client-appointments-table-title">Turnos comprendidos en el período</h3>
            <div class="table-responsive"><table class="table align-middle mb-0"><thead><tr><th>Fecha y hora</th><th>Vehículo</th><th>Motivo</th><th>Estado</th></tr></thead><tbody>@foreach ($turnos as $turno)<tr><td><strong>{{ $turno->fecha->format('d/m/Y') }}</strong><small class="d-block text-secondary">{{ substr((string) $turno->hora, 0, 5) }} h</small></td><td><strong>{{ $turno->vehiculo->patente }}</strong><small class="d-block text-secondary">{{ $turno->vehiculo->marca }} {{ $turno->vehiculo->modelo }}</small></td><td>{{ $turno->motivo }}</td><td><span class="state-badge state-{{ $turno->estado->codigo }}">{{ $turno->estado->nombre }}</span></td></tr>@endforeach</tbody></table></div>
        </section>
    </article>
@endif
@endsection
