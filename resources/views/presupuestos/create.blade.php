@extends('layouts.app')

@section('title', 'Generar presupuesto')
@section('context', 'Turnos · Presupuesto')

@section('content')
@php($oldServices = collect(old('servicios', []))->keyBy('id'))
@php($oldParts = collect(old('repuestos', []))->keyBy('id'))

<section class="mb-4">
    <div class="page-eyebrow">ATSA-52 · Presupuesto</div>
    <div class="d-flex flex-column flex-lg-row align-items-lg-end justify-content-between gap-3">
        <div>
            <h1 class="page-title">Generar presupuesto</h1>
            <p class="page-lead mb-0">Seleccioná servicios y repuestos. Los importes vigentes quedarán registrados como referencia histórica.</p>
        </div>
        <a class="btn btn-outline-secondary" href="{{ route('turnos.show', $turno) }}">
            <i class="bi bi-arrow-left me-2" aria-hidden="true"></i>Volver al turno
        </a>
    </div>
</section>

@if ($errors->any())
    <div class="alert alert-danger" role="alert">
        <strong>No pudimos generar el presupuesto.</strong>
        <ul class="mb-0 mt-2">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="content-card mb-4">
    <div class="row g-3">
        <div class="col-md-3"><span class="detail-label">Turno</span><strong class="d-block">#{{ $turno->id }}</strong></div>
        <div class="col-md-3"><span class="detail-label">Cliente</span><strong class="d-block">{{ $turno->cliente->nombre }} {{ $turno->cliente->apellido }}</strong></div>
        <div class="col-md-3"><span class="detail-label">Vehículo</span><strong class="d-block">{{ $turno->vehiculo->patente }}</strong><small class="text-secondary">{{ $turno->vehiculo->marca }} {{ $turno->vehiculo->modelo }}</small></div>
        <div class="col-md-3"><span class="detail-label">Atención</span><strong class="d-block">{{ $turno->fecha->format('d/m/Y') }} · {{ substr((string) $turno->hora, 0, 5) }} h</strong></div>
    </div>
</div>

<form method="POST" action="{{ route('presupuestos.store') }}" data-budget-form>
    @csrf
    <input name="turno_id" type="hidden" value="{{ $turno->id }}">

    <div class="row g-4 align-items-start">
        <div class="col-xl-8">
            <section class="content-card mb-4" aria-labelledby="services-title">
                <div class="form-section-heading">
                    <span class="form-section-icon"><i class="bi bi-tools" aria-hidden="true"></i></span>
                    <div><h2 id="services-title">Servicios</h2><p>Elegí uno o más servicios de una misma especialidad.</p></div>
                </div>

                <div class="catalog-list">
                    @foreach ($servicios as $servicio)
                        @php($selected = $oldServices->has($servicio->id))
                        @php($oldItem = $oldServices->get($servicio->id, []))
                        <article class="catalog-item" data-budget-item data-unit-price="{{ $servicio->precio_base }}">
                            <div class="form-check catalog-check">
                                <input class="form-check-input" id="service-{{ $servicio->id }}" name="servicios[{{ $servicio->id }}][id]" type="checkbox" value="{{ $servicio->id }}" data-budget-toggle @checked($selected)>
                                <label class="form-check-label" for="service-{{ $servicio->id }}">
                                    <strong>{{ $servicio->nombre }}</strong>
                                    <span>{{ $servicio->especialidad->nombre }} · {{ $servicio->descripcion }}</span>
                                </label>
                            </div>
                            <div class="catalog-price">
                                <strong>${{ number_format((float) $servicio->precio_base, 2, ',', '.') }}</strong>
                                <label class="visually-hidden" for="service-quantity-{{ $servicio->id }}">Cantidad</label>
                                <input class="form-control form-control-sm" id="service-quantity-{{ $servicio->id }}" name="servicios[{{ $servicio->id }}][cantidad]" type="number" min="1" max="99" value="{{ $oldItem['cantidad'] ?? 1 }}" data-budget-quantity @disabled(! $selected)>
                            </div>
                        </article>
                    @endforeach
                </div>
            </section>

            <section class="content-card mb-4" aria-labelledby="parts-title">
                <div class="form-section-heading">
                    <span class="form-section-icon"><i class="bi bi-gear" aria-hidden="true"></i></span>
                    <div><h2 id="parts-title">Repuestos</h2><p>Opcional. El precio y la disponibilidad provienen del catálogo precargado.</p></div>
                </div>

                <div class="catalog-list">
                    @foreach ($repuestos as $repuesto)
                        @php($selected = $oldParts->has($repuesto->id))
                        @php($oldItem = $oldParts->get($repuesto->id, []))
                        <article class="catalog-item" data-budget-item data-unit-price="{{ $repuesto->precio_venta }}">
                            <div class="form-check catalog-check">
                                <input class="form-check-input" id="part-{{ $repuesto->id }}" name="repuestos[{{ $repuesto->id }}][id]" type="checkbox" value="{{ $repuesto->id }}" data-budget-toggle @checked($selected) @disabled($repuesto->stock === 0)>
                                <label class="form-check-label" for="part-{{ $repuesto->id }}">
                                    <strong>{{ $repuesto->nombre }}</strong>
                                    <span>{{ $repuesto->marca }} · {{ $repuesto->original ? 'Original' : 'Alternativo' }} · Stock {{ $repuesto->stock }}</span>
                                </label>
                            </div>
                            <div class="catalog-price">
                                <strong>${{ number_format((float) $repuesto->precio_venta, 2, ',', '.') }}</strong>
                                <label class="visually-hidden" for="part-quantity-{{ $repuesto->id }}">Cantidad</label>
                                <input class="form-control form-control-sm" id="part-quantity-{{ $repuesto->id }}" name="repuestos[{{ $repuesto->id }}][cantidad]" type="number" min="1" max="{{ max($repuesto->stock, 1) }}" value="{{ $oldItem['cantidad'] ?? 1 }}" data-budget-quantity @disabled(! $selected)>
                            </div>
                        </article>
                    @endforeach
                </div>
            </section>

            <section class="content-card" aria-labelledby="notes-title">
                <div class="form-section-heading">
                    <span class="form-section-icon"><i class="bi bi-card-text" aria-hidden="true"></i></span>
                    <div><h2 id="notes-title">Observaciones</h2><p>Condiciones o aclaraciones que deben acompañar la estimación.</p></div>
                </div>
                <textarea class="form-control" id="observaciones" name="observaciones" rows="4" maxlength="1000" placeholder="Ej.: presupuesto sujeto a revisión al desarmar la unidad.">{{ old('observaciones') }}</textarea>
            </section>
        </div>

        <div class="col-xl-4">
            <aside class="content-card budget-summary sticky-xl-top" aria-labelledby="budget-summary-title">
                <div class="page-eyebrow">Resumen</div>
                <h2 class="h5 fw-bold mt-2" id="budget-summary-title">Total estimado</h2>
                <div class="budget-total" data-budget-total>$ 0,00</div>
                <p class="small text-secondary">El total definitivo se calcula nuevamente en el servidor con los precios registrados.</p>
                <div class="budget-validity"><i class="bi bi-clock-history" aria-hidden="true"></i><span>Validez: 15 días desde la emisión</span></div>
                <button class="btn btn-autotech w-100 mt-4" type="submit">
                    <i class="bi bi-file-earmark-check me-2" aria-hidden="true"></i>Generar presupuesto
                </button>
            </aside>
        </div>
    </div>
</form>
@endsection
