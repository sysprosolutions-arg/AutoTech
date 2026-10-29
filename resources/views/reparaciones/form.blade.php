@extends('layouts.app')

@section('title', "Registrar reparación · {$ordenTrabajo->numero}")
@section('context', 'Gestión de reparaciones')

@section('content')
@php($oldParts = collect(old('repuestos', []))->keyBy('detalle_id'))

<section class="mb-4">
    <div class="page-eyebrow">ATSA-10 · {{ $ordenTrabajo->numero }}</div>
    <div class="d-flex flex-column flex-lg-row align-items-lg-end justify-content-between gap-3">
        <div><h1 class="page-title">Registrar reparación</h1><p class="page-lead mb-0">Documentá el trabajo efectivamente realizado sobre {{ $ordenTrabajo->vehiculo->patente }}.</p></div>
        <a class="btn btn-outline-secondary" href="{{ route('reparaciones.create') }}"><i class="bi bi-arrow-left me-2" aria-hidden="true"></i>Volver a órdenes</a>
    </div>
</section>

@if ($errors->any())
    <div class="alert alert-danger" role="alert"><strong>No pudimos registrar la reparación.</strong><ul class="mb-0 mt-2">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
@endif

<div class="content-card mb-4">
    <div class="row g-3">
        <div class="col-md-3"><span class="detail-label">Orden de Trabajo</span><strong>{{ $ordenTrabajo->numero }}</strong></div>
        <div class="col-md-3"><span class="detail-label">Vehículo</span><strong>{{ $ordenTrabajo->vehiculo->patente }}</strong><small class="d-block text-secondary">{{ $ordenTrabajo->vehiculo->marca }} {{ $ordenTrabajo->vehiculo->modelo }}</small></div>
        <div class="col-md-3"><span class="detail-label">Cliente</span><strong>{{ $ordenTrabajo->turno->cliente->nombre }} {{ $ordenTrabajo->turno->cliente->apellido }}</strong></div>
        <div class="col-md-3"><span class="detail-label">Mecánico</span><strong>{{ $ordenTrabajo->empleado->nombre }} {{ $ordenTrabajo->empleado->apellido }}</strong><small class="d-block text-secondary">{{ $ordenTrabajo->empleado->especialidad->nombre }}</small></div>
    </div>
</div>

<form method="POST" action="{{ route('reparaciones.store') }}">
    @csrf
    <input name="orden_trabajo_id" type="hidden" value="{{ $ordenTrabajo->id }}">
    <div class="row g-4">
        <div class="col-xl-8">
            <section class="content-card mb-4" aria-labelledby="repair-data-title">
                <div class="form-section-heading"><span class="form-section-icon"><i class="bi bi-wrench-adjustable" aria-hidden="true"></i></span><div><h2 id="repair-data-title">Datos del trabajo</h2><p>Clasificación, duración y descripción técnica.</p></div></div>
                <div class="row g-3">
                    <div class="col-md-7">
                        <label class="form-label" for="tipo_reparacion_id">Tipo de reparación <span class="required-mark">*</span></label>
                        <select class="form-select" id="tipo_reparacion_id" name="tipo_reparacion_id" required>
                            <option value="">Seleccionar</option>
                            @foreach ($tiposReparacion as $tipo)
                                <option value="{{ $tipo->id }}" @selected((string) old('tipo_reparacion_id') === (string) $tipo->id)>{{ $tipo->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-5">
                        <label class="form-label" for="tiempo_empleado_minutos">Tiempo empleado (minutos) <span class="required-mark">*</span></label>
                        <input class="form-control" id="tiempo_empleado_minutos" name="tiempo_empleado_minutos" type="number" min="1" max="10000" value="{{ old('tiempo_empleado_minutos') }}" placeholder="Ej.: 120" required>
                    </div>
                    <div class="col-12">
                        <label class="form-label" for="trabajos_realizados">Trabajos realizados <span class="required-mark">*</span></label>
                        <textarea class="form-control" id="trabajos_realizados" name="trabajos_realizados" rows="5" maxlength="2000" placeholder="Describí las tareas, controles y resultados obtenidos." required>{{ old('trabajos_realizados') }}</textarea>
                    </div>
                    <div class="col-12">
                        <label class="form-label" for="incidencias">Incidencias</label>
                        <textarea class="form-control" id="incidencias" name="incidencias" rows="3" maxlength="2000" placeholder="Anomalías, desvíos o aclaraciones encontradas durante el trabajo.">{{ old('incidencias') }}</textarea>
                    </div>
                </div>
            </section>

            <section class="content-card" aria-labelledby="used-parts-title">
                <div class="form-section-heading"><span class="form-section-icon"><i class="bi bi-gear" aria-hidden="true"></i></span><div><h2 id="used-parts-title">Repuestos utilizados</h2><p>Marcá únicamente los repuestos efectivamente consumidos.</p></div></div>
                @php($partDetails = $ordenTrabajo->detalles->whereNotNull('repuesto_id'))
                @if ($partDetails->isEmpty())
                    <p class="text-secondary mb-0">La OT no incluye repuestos.</p>
                @else
                    <div class="catalog-list">
                        @foreach ($partDetails as $detalle)
                            @php($selected = $oldParts->has($detalle->id))
                            @php($oldItem = $oldParts->get($detalle->id, []))
                            <article class="catalog-item" data-repair-part>
                                <div class="form-check catalog-check">
                                    <input class="form-check-input" id="repair-part-{{ $detalle->id }}" name="repuestos[{{ $detalle->id }}][detalle_id]" type="checkbox" value="{{ $detalle->id }}" data-repair-part-toggle @checked($selected) @disabled($detalle->repuesto->stock === 0)>
                                    <label class="form-check-label" for="repair-part-{{ $detalle->id }}"><strong>{{ $detalle->descripcion }}</strong><span>Autorizado: {{ $detalle->cantidad }} · Stock actual: {{ $detalle->repuesto->stock }}</span></label>
                                </div>
                                <div class="catalog-price">
                                    <label class="small text-secondary" for="repair-part-quantity-{{ $detalle->id }}">Cantidad usada</label>
                                    <input class="form-control form-control-sm" id="repair-part-quantity-{{ $detalle->id }}" name="repuestos[{{ $detalle->id }}][cantidad]" type="number" min="1" max="{{ min($detalle->cantidad, $detalle->repuesto->stock) }}" value="{{ $oldItem['cantidad'] ?? 1 }}" data-repair-part-quantity @disabled(! $selected)>
                                </div>
                            </article>
                        @endforeach
                    </div>
                @endif
            </section>
        </div>

        <div class="col-xl-4">
            <aside class="content-card sticky-xl-top repair-summary" aria-labelledby="authorized-work-title">
                <div class="page-eyebrow">Trabajo autorizado</div>
                <h2 class="h5 fw-bold mt-2 mb-3" id="authorized-work-title">Servicios de la OT</h2>
                <ul class="authorized-work-list">
                    @foreach ($ordenTrabajo->detalles->whereNotNull('tipo_servicio_id') as $detalle)
                        <li><i class="bi bi-check-circle" aria-hidden="true"></i><span><strong>{{ $detalle->descripcion }}</strong><small>Cantidad {{ $detalle->cantidad }}</small></span></li>
                    @endforeach
                </ul>
                <div class="budget-validity mt-3"><i class="bi bi-shield-check" aria-hidden="true"></i><span>Presupuesto {{ $ordenTrabajo->presupuesto->numero }} aprobado</span></div>
                <button class="btn btn-autotech w-100 mt-4" type="submit"><i class="bi bi-save me-2" aria-hidden="true"></i>Registrar reparación</button>
            </aside>
        </div>
    </div>
</form>
@endsection
