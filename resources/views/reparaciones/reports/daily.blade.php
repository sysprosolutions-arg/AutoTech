@extends('layouts.app')

@section('title', 'Informe diario de reparaciones')
@section('context', 'Informes de reparaciones')

@section('content')
<section class="mb-4 report-screen-actions">
    <div class="page-eyebrow">ATSA-13 · Informe diario</div>
    <div class="d-flex flex-column flex-lg-row align-items-lg-end justify-content-between gap-3">
        <div>
            <h1 class="page-title">Informe diario de reparaciones</h1>
            <p class="page-lead mb-0">Seleccioná una jornada para consolidar los trabajos registrados en el taller.</p>
        </div>
        <a class="btn btn-outline-secondary" href="{{ route('reparaciones.index') }}"><i class="bi bi-arrow-left me-2" aria-hidden="true"></i>Volver a reparaciones</a>
    </div>
</section>

<form class="content-card mb-4 report-screen-actions" method="GET" action="{{ route('reparaciones.reports.daily') }}">
    <div class="row g-3 align-items-end">
        <div class="col-md-8 col-lg-4">
            <label class="form-label" for="fecha">Fecha del informe <span class="text-danger" aria-hidden="true">*</span></label>
            <input class="form-control @error('fecha') is-invalid @enderror" id="fecha" name="fecha" type="date" value="{{ $fecha ?? '' }}" required>
            @error('fecha')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-4 col-lg-3 d-grid">
            <button class="btn btn-autotech" type="submit"><i class="bi bi-file-earmark-text me-2" aria-hidden="true"></i>Generar informe</button>
        </div>
    </div>
</form>

@if ($fecha === null)
    <section class="content-card empty-state" aria-live="polite">
        <i class="bi bi-calendar2-check" aria-hidden="true"></i>
        <h2>Seleccioná una fecha</h2>
        <p>El informe se generará con las reparaciones registradas durante esa jornada.</p>
    </section>
@elseif ($reparaciones->isEmpty())
    <section class="content-card empty-state" aria-live="polite">
        <i class="bi bi-calendar-x" aria-hidden="true"></i>
        <h2>No existen reparaciones para la fecha seleccionada</h2>
        <p>No se generó información para el {{ \Illuminate\Support\Carbon::parse($fecha)->format('d/m/Y') }}. Seleccioná otra fecha para volver a consultar.</p>
    </section>
@else
    <article class="daily-report" aria-labelledby="daily-report-title">
        <header class="content-card daily-report-header mb-4">
            <div>
                <div class="page-eyebrow">AutoTech S.A.</div>
                <h2 class="h3 fw-bold mb-1" id="daily-report-title">Reparaciones del {{ \Illuminate\Support\Carbon::parse($fecha)->format('d/m/Y') }}</h2>
                <p class="text-secondary mb-0">Informe operativo generado el {{ now()->format('d/m/Y H:i') }}.</p>
            </div>
            <div class="d-flex align-items-center gap-4 flex-wrap">
                <div class="report-metric"><span>Reparaciones</span><strong>{{ $reparaciones->count() }}</strong></div>
                <div class="report-metric"><span>Tiempo total</span><strong>{{ \App\Models\Reparacion::formatearMinutosComoHoras($totalMinutos) }}</strong></div>
                <button class="btn btn-outline-secondary report-screen-actions" type="button" data-print-report><i class="bi bi-printer me-2" aria-hidden="true"></i>Imprimir</button>
            </div>
        </header>

        <div class="daily-report-list">
            @foreach ($reparaciones as $reparacion)
                <section class="content-card daily-report-item" aria-labelledby="repair-{{ $reparacion->id }}-title">
                    <div class="daily-report-item-heading">
                        <div>
                            <span class="state-badge state-{{ $reparacion->estado->codigo }}">{{ $reparacion->estado->nombre }}</span>
                            <h3 class="h5 fw-bold mt-2 mb-1" id="repair-{{ $reparacion->id }}-title">{{ $reparacion->numero }} · {{ $reparacion->vehiculo->patente }}</h3>
                            <p class="text-secondary mb-0">{{ $reparacion->vehiculo->marca }} {{ $reparacion->vehiculo->modelo }} · {{ $reparacion->vehiculo->cliente->nombre }} {{ $reparacion->vehiculo->cliente->apellido }}</p>
                        </div>
                        <div class="text-lg-end"><strong>{{ $reparacion->fecha_inicio->format('H:i') }} h</strong><small class="d-block text-secondary">{{ $reparacion->tiempoEmpleadoFormateado() }}</small></div>
                    </div>

                    <dl class="daily-report-data">
                        <div><dt>Tipo de reparación</dt><dd>{{ $reparacion->tipo->nombre }}</dd></div>
                        <div><dt>Mecánico asignado</dt><dd>{{ $reparacion->empleado->nombre }} {{ $reparacion->empleado->apellido }}<small class="d-block text-secondary">{{ $reparacion->empleado->especialidad->nombre }}</small></dd></div>
                        <div><dt>Servicios</dt><dd>{{ $reparacion->detalles->where('tipo', 'servicio')->pluck('descripcion')->join(', ') ?: 'Sin servicios registrados' }}</dd></div>
                        <div><dt>Repuestos utilizados</dt><dd>{{ $reparacion->detalles->where('tipo', 'repuesto')->map(fn ($detalle) => $detalle->descripcion.' ('.$detalle->cantidad.')')->join(', ') ?: 'Sin repuestos utilizados' }}</dd></div>
                    </dl>

                    <a class="small report-screen-actions" href="{{ route('reparaciones.show', $reparacion) }}">Ver detalle completo</a>
                </section>
            @endforeach
        </div>
    </article>
@endif
@endsection
