@extends('layouts.app')

@section('title', "Turno #{$turno->id}")
@section('context', 'Gestión de turnos')

@section('content')
<section class="mb-4">
    <div class="page-eyebrow">ATSA-50 · Detalle</div>
    <div class="d-flex flex-column flex-lg-row align-items-lg-end justify-content-between gap-3">
        <div>
            <div class="d-flex align-items-center gap-3 flex-wrap">
                <h1 class="page-title mb-0">Turno #{{ $turno->id }}</h1>
                <span class="state-badge state-{{ $turno->estado->codigo }}">{{ $turno->estado->nombre }}</span>
            </div>
            <p class="page-lead mb-0 mt-2">Información registrada para la atención del vehículo.</p>
        </div>
        <a class="btn btn-outline-secondary" href="{{ route('turnos.index') }}">
            <i class="bi bi-arrow-left me-2" aria-hidden="true"></i>Volver a turnos
        </a>
    </div>
</section>

<div class="row g-4">
    <div class="col-xl-8">
        <section class="content-card h-100" aria-labelledby="appointment-detail-title">
            <div class="form-section-heading">
                <span class="form-section-icon"><i class="bi bi-calendar-event" aria-hidden="true"></i></span>
                <div>
                    <h2 id="appointment-detail-title">Datos de la atención</h2>
                    <p>Fecha, hora y motivo informados al registrar el turno.</p>
                </div>
            </div>
            <dl class="detail-grid mb-0">
                <div><dt>Fecha</dt><dd>{{ $turno->fecha->format('d/m/Y') }}</dd></div>
                <div><dt>Hora</dt><dd>{{ substr((string) $turno->hora, 0, 5) }} h</dd></div>
                <div class="detail-grid-wide"><dt>Motivo</dt><dd>{{ $turno->motivo }}</dd></div>
                <div class="detail-grid-wide"><dt>Observaciones</dt><dd>{{ $turno->observaciones ?: 'Sin observaciones.' }}</dd></div>
            </dl>
        </section>
    </div>

    <div class="col-xl-4">
        <section class="content-card mb-4" aria-labelledby="client-detail-title">
            <div class="form-section-heading">
                <span class="form-section-icon"><i class="bi bi-person" aria-hidden="true"></i></span>
                <div><h2 id="client-detail-title">Cliente</h2><p>Titular del vehículo.</p></div>
            </div>
            <strong>{{ $turno->cliente->nombre }} {{ $turno->cliente->apellido }}</strong>
            <div class="small text-secondary mt-2">DNI {{ $turno->cliente->dni }}</div>
            <div class="small text-secondary">{{ $turno->cliente->telefono }}</div>
        </section>

        <section class="content-card" aria-labelledby="vehicle-detail-title">
            <div class="form-section-heading">
                <span class="form-section-icon"><i class="bi bi-car-front" aria-hidden="true"></i></span>
                <div><h2 id="vehicle-detail-title">Vehículo</h2><p>Unidad vinculada al turno.</p></div>
            </div>
            <strong>{{ $turno->vehiculo->patente }}</strong>
            <div class="small text-secondary mt-2">{{ $turno->vehiculo->marca }} {{ $turno->vehiculo->modelo }} · {{ $turno->vehiculo->anio }}</div>
            <div class="small text-secondary">{{ $turno->vehiculo->color ?: 'Color no informado' }}</div>
        </section>
    </div>
</div>

<div class="small text-secondary mt-4">
    Registrado por {{ $turno->registradoPor->name }} el {{ $turno->created_at->format('d/m/Y H:i') }}.
</div>
@endsection
