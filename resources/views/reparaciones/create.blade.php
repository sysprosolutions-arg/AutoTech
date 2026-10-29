@extends('layouts.app')

@section('title', 'Registrar reparación')
@section('context', 'Gestión de reparaciones')

@section('content')
<section class="mb-4">
    <div class="page-eyebrow">ATSA-10 · Registro</div>
    <h1 class="page-title">Registrar reparación</h1>
    <p class="page-lead mb-0">Seleccioná una Orden de Trabajo emitida desde un presupuesto aprobado para comenzar el registro técnico.</p>
</section>

@if (session('error'))
    <div class="alert alert-danger" role="alert">{{ session('error') }}</div>
@endif

<section class="content-card" aria-labelledby="eligible-orders-title">
    <div class="d-flex align-items-center justify-content-between mb-3">
        <div><div class="page-eyebrow">Órdenes disponibles</div><h2 class="h5 fw-bold mt-1 mb-0" id="eligible-orders-title">Pendientes de reparación</h2></div>
        <span class="text-secondary small">{{ $ordenesTrabajo->count() }} {{ $ordenesTrabajo->count() === 1 ? 'orden' : 'órdenes' }}</span>
    </div>

    @if ($ordenesTrabajo->isEmpty())
        <div class="empty-state">
            <i class="bi bi-clipboard2-check" aria-hidden="true"></i>
            <h3>No hay órdenes pendientes</h3>
            <p>Las OT aparecerán aquí cuando el cliente apruebe un presupuesto.</p>
        </div>
    @else
        <div class="repair-order-list">
            @foreach ($ordenesTrabajo as $orden)
                <article class="repair-order-card">
                    <div class="repair-order-number"><span>Orden de Trabajo</span><strong>{{ $orden->numero }}</strong></div>
                    <div><span class="detail-label">Cliente y vehículo</span><strong>{{ $orden->turno->cliente->nombre }} {{ $orden->turno->cliente->apellido }}</strong><small>{{ $orden->vehiculo->patente }} · {{ $orden->vehiculo->marca }} {{ $orden->vehiculo->modelo }}</small></div>
                    <div><span class="detail-label">Mecánico asignado</span><strong>{{ $orden->empleado->nombre }} {{ $orden->empleado->apellido }}</strong><small>{{ $orden->empleado->especialidad->nombre }}</small></div>
                    <a class="btn btn-autotech px-4" href="{{ route('reparaciones.create.from-order', $orden) }}">Registrar trabajo</a>
                </article>
            @endforeach
        </div>
    @endif
</section>
@endsection
