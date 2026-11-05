@extends('layouts.app')

@section('title', 'Reparaciones por tipo')
@section('context', 'Reportes de reparaciones')

@section('content')
<section class="mb-4 report-screen-actions">
    <div class="page-eyebrow">Análisis del taller · Reporte táctico</div>
    <div class="d-flex flex-column flex-lg-row align-items-lg-end justify-content-between gap-3">
        <div>
            <h1 class="page-title">Reparaciones por tipo</h1>
            <p class="page-lead mb-0">Compará la cantidad de trabajos realizados por categoría durante un período.</p>
        </div>
        <a class="btn btn-outline-secondary" href="{{ route('reparaciones.index') }}"><i class="bi bi-arrow-left me-2" aria-hidden="true"></i>Volver a reparaciones</a>
    </div>
</section>

<form class="content-card mb-4 report-screen-actions" method="GET" action="{{ route('reparaciones.reports.types') }}">
    <div class="row g-3 align-items-end">
        <div class="col-md-4">
            <label class="form-label" for="desde">Desde <span class="required-mark">*</span></label>
            <input class="form-control @error('desde') is-invalid @enderror" id="desde" name="desde" type="date" value="{{ $desde ?? '' }}" required>
            @error('desde')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-4">
            <label class="form-label" for="hasta">Hasta <span class="required-mark">*</span></label>
            <input class="form-control @error('hasta') is-invalid @enderror" id="hasta" name="hasta" type="date" value="{{ $hasta ?? '' }}" required>
            @error('hasta')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-4 d-grid"><button class="btn btn-autotech" type="submit"><i class="bi bi-bar-chart me-2" aria-hidden="true"></i>Generar reporte</button></div>
    </div>
</form>

@if ($desde === null || $hasta === null)
    <section class="content-card empty-state" aria-live="polite">
        <i class="bi bi-calendar-range" aria-hidden="true"></i>
        <h2>Seleccioná un período</h2>
        <p>El reporte agrupará las reparaciones registradas según su tipo.</p>
    </section>
@elseif ($tiposReparacion->isEmpty())
    <section class="content-card empty-state" aria-live="polite">
        <i class="bi bi-bar-chart" aria-hidden="true"></i>
        <h2>No existen reparaciones en el período seleccionado</h2>
        <p>Probá con otro rango de fechas para generar el reporte.</p>
    </section>
@else
    <article class="type-report" aria-labelledby="repair-type-report-title">
        <header class="content-card daily-report-header mb-4">
            <div>
                <div class="page-eyebrow">AutoTech S.A. · Distribución de trabajos</div>
                <h2 class="h3 fw-bold mb-1" id="repair-type-report-title">Del {{ \Illuminate\Support\Carbon::parse($desde)->format('d/m/Y') }} al {{ \Illuminate\Support\Carbon::parse($hasta)->format('d/m/Y') }}</h2>
                <p class="text-secondary mb-0">Reporte generado el {{ now()->format('d/m/Y H:i') }}.</p>
            </div>
            <div class="d-flex align-items-center gap-4 flex-wrap">
                <div class="report-metric"><span>Total reparaciones</span><strong>{{ $totalReparaciones }}</strong></div>
                <div class="report-metric"><span>Tipos registrados</span><strong>{{ $tiposReparacion->count() }}</strong></div>
                <button class="btn btn-outline-secondary report-screen-actions" type="button" data-print-report><i class="bi bi-printer me-2" aria-hidden="true"></i>Imprimir</button>
            </div>
        </header>

        <section class="content-card mb-4" aria-labelledby="repair-type-chart-title">
            <div class="page-eyebrow">Comparación</div>
            <h3 class="h5 fw-bold mt-1 mb-4" id="repair-type-chart-title">Cantidad por tipo de reparación</h3>
            <div class="report-bars" role="img" aria-label="Gráfico de barras con la cantidad de reparaciones por tipo">
                @foreach ($tiposReparacion as $tipo)
                    @php($percentage = $totalReparaciones > 0 ? round(($tipo->reparaciones_count / $totalReparaciones) * 100, 1) : 0)
                    @php($barWidth = $maximaCantidad > 0 ? round(($tipo->reparaciones_count / $maximaCantidad) * 100, 1) : 0)
                    <div class="report-bar-row">
                        <div class="report-bar-heading"><strong>{{ $tipo->nombre }}</strong><span>{{ $tipo->reparaciones_count }} · {{ number_format($percentage, 1, ',', '.') }}%</span></div>
                        <div class="report-bar-track" aria-hidden="true"><span style="width: {{ $barWidth }}%"></span></div>
                    </div>
                @endforeach
            </div>
        </section>

        <section class="content-card" aria-labelledby="repair-type-table-title">
            <div class="page-eyebrow">Valores exactos</div>
            <h3 class="h5 fw-bold mt-1 mb-3" id="repair-type-table-title">Detalle del reporte</h3>
            <div class="table-responsive"><table class="table align-middle mb-0"><thead><tr><th>Tipo de reparación</th><th class="text-end">Cantidad</th><th class="text-end">Participación</th></tr></thead><tbody>@foreach ($tiposReparacion as $tipo)@php($percentage = ($tipo->reparaciones_count / $totalReparaciones) * 100)<tr><td><strong>{{ $tipo->nombre }}</strong></td><td class="text-end">{{ $tipo->reparaciones_count }}</td><td class="text-end">{{ number_format($percentage, 1, ',', '.') }}%</td></tr>@endforeach</tbody><tfoot><tr><th>Total</th><th class="text-end">{{ $totalReparaciones }}</th><th class="text-end">100%</th></tr></tfoot></table></div>
        </section>
    </article>
@endif
@endsection
