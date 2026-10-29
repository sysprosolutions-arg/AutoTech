@extends('layouts.app')

@section('title', $reparacion->numero)
@section('context', 'Gestión de reparaciones')

@section('content')
<section class="mb-4">
    <div class="page-eyebrow">ATSA-10/12 · Reparación</div>
    <div class="d-flex flex-column flex-lg-row align-items-lg-end justify-content-between gap-3">
        <div><div class="d-flex align-items-center gap-3 flex-wrap"><h1 class="page-title mb-0">{{ $reparacion->numero }}</h1><span class="state-badge state-{{ $reparacion->estado->codigo }}">{{ $reparacion->estado->nombre }}</span></div><p class="page-lead mb-0 mt-2">{{ $reparacion->vehiculo->patente }} · {{ $reparacion->vehiculo->cliente->nombre }} {{ $reparacion->vehiculo->cliente->apellido }}</p></div>
        <div class="d-flex gap-2 flex-wrap">
            @if ($reparacion->estado->codigo === 'en-proceso')
                <form method="POST" action="{{ route('reparaciones.finish', $reparacion) }}">@csrf<button class="btn btn-success px-4" type="submit"><i class="bi bi-check2-circle me-2" aria-hidden="true"></i>Finalizar reparación</button></form>
            @endif
            <a class="btn btn-outline-secondary" href="{{ route('reparaciones.index') }}"><i class="bi bi-arrow-left me-2" aria-hidden="true"></i>Volver</a>
        </div>
    </div>
</section>

@if (session('status'))<div class="alert alert-success" role="status"><i class="bi bi-check-circle me-2" aria-hidden="true"></i>{{ session('status') }}</div>@endif
@if ($errors->any())<div class="alert alert-danger" role="alert">{{ $errors->first() }}</div>@endif

<div class="row g-4">
    <div class="col-xl-8">
        <section class="content-card mb-4" aria-labelledby="repair-detail-title">
            <div class="form-section-heading"><span class="form-section-icon"><i class="bi bi-wrench-adjustable" aria-hidden="true"></i></span><div><h2 id="repair-detail-title">Trabajo realizado</h2><p>Información técnica registrada sobre la intervención.</p></div></div>
            <dl class="detail-grid mb-0">
                <div><dt>Tipo</dt><dd>{{ $reparacion->tipo->nombre }}</dd></div>
                <div><dt>Tiempo empleado</dt><dd>{{ $reparacion->tiempo_empleado_minutos }} minutos</dd></div>
                <div class="detail-grid-wide"><dt>Descripción</dt><dd>{{ $reparacion->trabajos_realizados }}</dd></div>
                <div class="detail-grid-wide"><dt>Incidencias</dt><dd>{{ $reparacion->incidencias ?: 'Sin incidencias registradas.' }}</dd></div>
            </dl>
        </section>

        <section class="content-card" aria-labelledby="repair-items-title">
            <div class="page-eyebrow">Detalle</div><h2 class="h5 fw-bold mt-1 mb-3" id="repair-items-title">Servicios y repuestos registrados</h2>
            <div class="table-responsive"><table class="table align-middle mb-0"><thead><tr><th>Concepto</th><th>Tipo</th><th class="text-center">Cantidad</th></tr></thead><tbody>@foreach($reparacion->detalles as $detalle)<tr><td><strong>{{ $detalle->descripcion }}</strong></td><td>{{ $detalle->tipo === 'servicio' ? 'Servicio' : 'Repuesto utilizado' }}</td><td class="text-center">{{ $detalle->cantidad }}</td></tr>@endforeach</tbody></table></div>
        </section>
    </div>

    <div class="col-xl-4">
        <section class="content-card mb-4" aria-labelledby="traceability-title">
            <div class="form-section-heading"><span class="form-section-icon"><i class="bi bi-diagram-3" aria-hidden="true"></i></span><div><h2 id="traceability-title">Trazabilidad</h2><p>Circuito que habilitó la reparación.</p></div></div>
            <dl class="decision-detail mb-0">
                <div><dt>Cliente</dt><dd>{{ $reparacion->vehiculo->cliente->nombre }} {{ $reparacion->vehiculo->cliente->apellido }}</dd></div>
                <div><dt>Vehículo</dt><dd>{{ $reparacion->vehiculo->patente }}<small class="d-block text-secondary">{{ $reparacion->vehiculo->marca }} {{ $reparacion->vehiculo->modelo }} · {{ $reparacion->vehiculo->anio }}</small></dd></div>
                <div><dt>Orden de Trabajo</dt><dd>{{ $reparacion->ordenTrabajo->numero }}</dd></div>
                <div><dt>Presupuesto</dt><dd>{{ $reparacion->ordenTrabajo->presupuesto->numero }}</dd></div>
                <div><dt>Mecánico</dt><dd>{{ $reparacion->empleado->nombre }} {{ $reparacion->empleado->apellido }}<small class="d-block text-secondary">{{ $reparacion->empleado->especialidad->nombre }}</small></dd></div>
                <div><dt>Registró</dt><dd>{{ $reparacion->registradoPor->name }}</dd></div>
            </dl>
        </section>
        <section class="content-card" aria-labelledby="repair-dates-title">
            <div class="form-section-heading"><span class="form-section-icon"><i class="bi bi-clock-history" aria-hidden="true"></i></span><div><h2 id="repair-dates-title">Fechas</h2><p>Seguimiento del ciclo operativo.</p></div></div>
            <dl class="decision-detail mb-0"><div><dt>Inicio</dt><dd>{{ $reparacion->fecha_inicio->format('d/m/Y H:i') }}</dd></div><div><dt>Finalización</dt><dd>{{ $reparacion->fecha_fin?->format('d/m/Y H:i') ?? 'En proceso' }}</dd></div></dl>
        </section>
    </div>
</div>
@endsection
