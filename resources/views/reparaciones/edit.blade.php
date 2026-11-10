@extends('layouts.app')

@section('title', "Modificar reparación · {$reparacion->numero}")
@section('context', 'Gestión de reparaciones')

@section('content')
@php($workOrder = $reparacion->ordenTrabajo)
@php($currentParts = $reparacion->detalles->where('tipo', 'repuesto')->keyBy('repuesto_id'))
@php($currentServices = $reparacion->detalles->where('tipo', 'servicio')->pluck('tipo_servicio_id')->all())
@php($selectedServices = collect(old('servicios', $workOrder->detalles->whereIn('tipo_servicio_id', $currentServices)->pluck('id')->all())))
@php($oldParts = collect(old('repuestos', []))->keyBy('repuesto_id'))
@php($authorizedPartQuantities = $workOrder->detalles->whereNotNull('repuesto_id')->pluck('cantidad', 'repuesto_id'))

<section class="mb-4">
    <div class="page-eyebrow">{{ $reparacion->numero }} · Modificación</div>
    <div class="d-flex flex-column flex-lg-row align-items-lg-end justify-content-between gap-3">
        <div><h1 class="page-title">Modificar reparación</h1><p class="page-lead mb-0">Corregí la información técnica registrada para {{ $reparacion->vehiculo->patente }}.</p></div>
        <a class="btn btn-outline-secondary" href="{{ route('reparaciones.show', $reparacion) }}"><i class="bi bi-arrow-left me-2" aria-hidden="true"></i>Volver al detalle</a>
    </div>
</section>

@if ($errors->any())
    <div class="alert alert-danger" role="alert"><strong>No pudimos modificar la reparación.</strong><ul class="mb-0 mt-2">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
@endif

<div class="content-card mb-4">
    <div class="row g-3">
        <div class="col-md-3"><span class="detail-label">Orden de Trabajo</span><strong>{{ $workOrder->numero }}</strong></div>
        <div class="col-md-3"><span class="detail-label">Vehículo</span><strong>{{ $workOrder->vehiculo->patente }}</strong><small class="d-block text-secondary">{{ $workOrder->vehiculo->marca }} {{ $workOrder->vehiculo->modelo }}</small></div>
        <div class="col-md-3"><span class="detail-label">Cliente</span><strong>{{ $workOrder->turno->cliente->nombre }} {{ $workOrder->turno->cliente->apellido }}</strong></div>
        <div class="col-md-3"><span class="detail-label">Estado</span><strong>{{ $reparacion->estado->nombre }}</strong></div>
    </div>
</div>

<form method="POST" action="{{ route('reparaciones.update', $reparacion) }}">
    @csrf
    @method('PUT')
    <div class="row g-4">
        <div class="col-xl-8">
            <section class="content-card mb-4" aria-labelledby="repair-edit-data-title">
                <div class="form-section-heading"><span class="form-section-icon"><i class="bi bi-wrench-adjustable" aria-hidden="true"></i></span><div><h2 id="repair-edit-data-title">Datos del trabajo</h2><p>Clasificación, responsable, duración y descripción técnica.</p></div></div>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label" for="tipo_reparacion_id">Tipo de reparación <span class="required-mark">*</span></label>
                        <select class="form-select @error('tipo_reparacion_id') is-invalid @enderror" id="tipo_reparacion_id" name="tipo_reparacion_id" required>
                            @foreach ($tiposReparacion as $tipo)
                                <option value="{{ $tipo->id }}" @selected((string) old('tipo_reparacion_id', $reparacion->tipo_reparacion_id) === (string) $tipo->id)>{{ $tipo->nombre }}</option>
                            @endforeach
                        </select>
                        @error('tipo_reparacion_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="empleado_id">Mecánico asignado <span class="required-mark">*</span></label>
                        <select class="form-select @error('empleado_id') is-invalid @enderror" id="empleado_id" name="empleado_id" required>
                            @foreach ($empleados as $empleado)
                                <option value="{{ $empleado->id }}" @selected((string) old('empleado_id', $reparacion->empleado_id) === (string) $empleado->id)>{{ $empleado->apellido }}, {{ $empleado->nombre }} · {{ $empleado->especialidad->nombre }}</option>
                            @endforeach
                        </select>
                        @error('empleado_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-5">
                        <label class="form-label" for="tiempo_empleado_horas">Tiempo empleado (horas) <span class="required-mark">*</span></label>
                        <input class="form-control @error('tiempo_empleado_horas') is-invalid @enderror" id="tiempo_empleado_horas" name="tiempo_empleado_horas" type="text" inputmode="decimal" value="{{ old('tiempo_empleado_horas', rtrim(rtrim(number_format($reparacion->tiempo_empleado_minutos / 60, 2, '.', ''), '0'), '.')) }}" placeholder="Ej.: 1,5" required>
                        @error('tiempo_empleado_horas')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        <div class="form-text">Podés usar coma o punto. Ejemplo: 1,5 horas.</div>
                    </div>
                    <div class="col-12">
                        <label class="form-label" for="trabajos_realizados">Trabajos realizados <span class="required-mark">*</span></label>
                        <textarea class="form-control @error('trabajos_realizados') is-invalid @enderror" id="trabajos_realizados" name="trabajos_realizados" rows="5" maxlength="2000" required>{{ old('trabajos_realizados', $reparacion->trabajos_realizados) }}</textarea>
                        @error('trabajos_realizados')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-12">
                        <label class="form-label" for="incidencias">Incidencias</label>
                        <textarea class="form-control @error('incidencias') is-invalid @enderror" id="incidencias" name="incidencias" rows="3" maxlength="2000">{{ old('incidencias', $reparacion->incidencias) }}</textarea>
                        @error('incidencias')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
            </section>

            <section class="content-card mb-4" aria-labelledby="performed-services-title">
                <div class="form-section-heading"><span class="form-section-icon"><i class="bi bi-clipboard2-check" aria-hidden="true"></i></span><div><h2 id="performed-services-title">Servicios realizados</h2><p>Seleccioná los servicios autorizados que efectivamente se realizaron.</p></div></div>
                <div class="catalog-list">
                    @foreach ($workOrder->detalles->whereNotNull('tipo_servicio_id') as $detail)
                        <article class="catalog-item"><div class="form-check catalog-check"><input class="form-check-input" id="service-{{ $detail->id }}" name="servicios[]" type="checkbox" value="{{ $detail->id }}" @checked($selectedServices->contains($detail->id))><label class="form-check-label" for="service-{{ $detail->id }}"><strong>{{ $detail->descripcion }}</strong><span>Cantidad autorizada: {{ $detail->cantidad }}</span></label></div></article>
                    @endforeach
                </div>
                @error('servicios')<div class="text-danger small mt-2">{{ $message }}</div>@enderror
            </section>

            <section class="content-card" aria-labelledby="modified-parts-title">
                <div class="form-section-heading"><span class="form-section-icon"><i class="bi bi-gear" aria-hidden="true"></i></span><div><h2 id="modified-parts-title">Repuestos utilizados</h2><p>Podés registrar repuestos adicionales. Las cantidades modificadas actualizan el stock de forma transaccional.</p></div></div>
                @if ($repuestosDisponibles->isEmpty())
                    <p class="text-secondary mb-0">No hay repuestos disponibles.</p>
                @else
                    <div class="catalog-list">
                        @foreach ($repuestosDisponibles as $repuesto)
                            @php($currentDetail = $currentParts->get($repuesto->id))
                            @php($selected = old('repuestos') !== null ? $oldParts->has($repuesto->id) : $currentDetail !== null)
                            @php($oldItem = $oldParts->get($repuesto->id, []))
                            @php($available = $repuesto->stock + ($currentDetail?->cantidad ?? 0))
                            @php($authorizedQuantity = $authorizedPartQuantities->get($repuesto->id))
                            <article class="catalog-item" data-repair-part>
                                <div class="form-check catalog-check"><input class="form-check-input" id="repair-part-{{ $repuesto->id }}" name="repuestos[{{ $repuesto->id }}][repuesto_id]" type="checkbox" value="{{ $repuesto->id }}" data-repair-part-toggle @checked($selected) @disabled($available === 0)><label class="form-check-label" for="repair-part-{{ $repuesto->id }}"><strong>{{ $repuesto->nombre }}</strong><span>{{ $repuesto->marca }} · {{ $authorizedQuantity ? "Incluido en presupuesto: {$authorizedQuantity}" : 'Adicional no presupuestado' }} · Disponible para esta reparación: {{ $available }}</span></label></div>
                                <div class="catalog-price"><label class="small text-secondary" for="repair-part-quantity-{{ $repuesto->id }}">Cantidad usada</label><input class="form-control form-control-sm" id="repair-part-quantity-{{ $repuesto->id }}" name="repuestos[{{ $repuesto->id }}][cantidad]" type="number" min="1" max="{{ $available }}" value="{{ $oldItem['cantidad'] ?? $currentDetail?->cantidad ?? 1 }}" data-repair-part-quantity @disabled(! $selected)></div>
                            </article>
                        @endforeach
                    </div>
                @endif
            </section>
        </div>

        <div class="col-xl-4">
            <aside class="content-card sticky-xl-top repair-summary">
                <div class="page-eyebrow">Actualización segura</div>
                <h2 class="h5 fw-bold mt-2 mb-3">Confirmar cambios</h2>
                <p class="text-secondary">Si alguna validación falla, los datos y el stock conservan sus valores anteriores.</p>
                <button class="btn btn-autotech w-100 mt-3" type="submit"><i class="bi bi-save me-2" aria-hidden="true"></i>Guardar modificación</button>
            </aside>
        </div>
    </div>
</form>
@endsection
