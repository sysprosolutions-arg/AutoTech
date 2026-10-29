@extends('layouts.app')

@section('title', $presupuesto->numero)
@section('context', 'Turnos · Presupuesto')

@section('content')
<section class="mb-4">
    <div class="page-eyebrow">ATSA-52 · Presupuesto</div>
    <div class="d-flex flex-column flex-lg-row align-items-lg-end justify-content-between gap-3">
        <div>
            <div class="d-flex align-items-center gap-3 flex-wrap">
                <h1 class="page-title mb-0">{{ $presupuesto->numero }}</h1>
                <span class="state-badge state-{{ $presupuesto->aprobacion->estado }}">{{ ucfirst($presupuesto->aprobacion->estado) }}</span>
            </div>
            <p class="page-lead mb-0 mt-2">Turno #{{ $presupuesto->turno->id }} · {{ $presupuesto->turno->cliente->nombre }} {{ $presupuesto->turno->cliente->apellido }} · {{ $presupuesto->turno->vehiculo->patente }}</p>
        </div>
        <a class="btn btn-outline-secondary" href="{{ route('turnos.show', $presupuesto->turno) }}">
            <i class="bi bi-arrow-left me-2" aria-hidden="true"></i>Volver al turno
        </a>
    </div>
</section>

@if (session('status'))
    <div class="alert alert-success" role="status"><i class="bi bi-check-circle me-2" aria-hidden="true"></i>{{ session('status') }}</div>
@endif

@if ($errors->any())
    <div class="alert alert-danger" role="alert">
        <strong>No pudimos registrar la decisión.</strong>
        <ul class="mb-0 mt-2">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
    </div>
@endif

<div class="row g-4 align-items-start">
    <div class="col-xl-8">
        <section class="content-card mb-4" aria-labelledby="budget-lines-title">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <div><div class="page-eyebrow">Detalle</div><h2 class="h5 fw-bold mt-1 mb-0" id="budget-lines-title">Conceptos presupuestados</h2></div>
                <span class="small text-secondary">Emitido {{ $presupuesto->fecha_emision->format('d/m/Y') }}</span>
            </div>
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead><tr><th>Concepto</th><th>Tipo</th><th class="text-center">Cantidad</th><th class="text-end">Unitario</th><th class="text-end">Subtotal</th></tr></thead>
                    <tbody>
                        @foreach ($presupuesto->detalles as $detalle)
                            <tr>
                                <td><strong>{{ $detalle->descripcion }}</strong>@if($detalle->tipoServicio)<small class="d-block text-secondary">{{ $detalle->tipoServicio->especialidad->nombre }}</small>@endif</td>
                                <td>{{ $detalle->tipo === 'servicio' ? 'Servicio' : 'Repuesto' }}</td>
                                <td class="text-center">{{ $detalle->cantidad }}</td>
                                <td class="text-end">${{ number_format((float) $detalle->precio_unitario, 2, ',', '.') }}</td>
                                <td class="text-end fw-semibold">${{ number_format((float) $detalle->subtotal, 2, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot><tr><th colspan="4">Total</th><th class="text-end budget-table-total">${{ number_format((float) $presupuesto->total, 2, ',', '.') }}</th></tr></tfoot>
                </table>
            </div>
            @if ($presupuesto->observaciones)
                <div class="budget-note mt-4"><strong>Observaciones</strong><p class="mb-0 mt-1">{{ $presupuesto->observaciones }}</p></div>
            @endif
        </section>

        @if ($presupuesto->ordenTrabajo)
            <section class="content-card order-issued" aria-labelledby="order-title">
                <div class="form-section-heading">
                    <span class="form-section-icon"><i class="bi bi-clipboard-check" aria-hidden="true"></i></span>
                    <div><h2 id="order-title">Orden de Trabajo emitida</h2><p>Generada automáticamente a partir del presupuesto aprobado.</p></div>
                </div>
                <dl class="detail-grid mb-0">
                    <div><dt>Número</dt><dd><strong>{{ $presupuesto->ordenTrabajo->numero }}</strong></dd></div>
                    <div><dt>Estado</dt><dd>{{ $presupuesto->ordenTrabajo->estado->nombre }}</dd></div>
                    <div><dt>Empleado asignado</dt><dd>{{ $presupuesto->ordenTrabajo->empleado->nombre }} {{ $presupuesto->ordenTrabajo->empleado->apellido }}</dd></div>
                    <div><dt>Especialidad</dt><dd>{{ $presupuesto->ordenTrabajo->empleado->especialidad->nombre }}</dd></div>
                </dl>
            </section>
        @endif
    </div>

    <div class="col-xl-4">
        @if ($presupuesto->aprobacion->estado === 'pendiente')
            <section class="content-card" aria-labelledby="decision-title">
                <div class="form-section-heading">
                    <span class="form-section-icon"><i class="bi bi-chat-square-check" aria-hidden="true"></i></span>
                    <div><h2 id="decision-title">Decisión del cliente</h2><p>El empleado registra la respuesta comunicada por el cliente.</p></div>
                </div>
                <form method="POST" action="{{ route('presupuestos.decision.store', $presupuesto) }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label" for="fecha_decision">Fecha de decisión <span class="required-mark">*</span></label>
                        <input class="form-control" id="fecha_decision" name="fecha_decision" type="date" max="{{ now()->toDateString() }}" value="{{ old('fecha_decision', now()->toDateString()) }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="medio_confirmacion">Medio de confirmación <span class="required-mark">*</span></label>
                        <select class="form-select" id="medio_confirmacion" name="medio_confirmacion" required>
                            <option value="">Seleccionar</option>
                            @foreach (['presencial' => 'Presencial', 'telefono' => 'Teléfono', 'whatsapp' => 'WhatsApp', 'email' => 'Correo electrónico'] as $value => $label)
                                <option value="{{ $value }}" @selected(old('medio_confirmacion') === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-4">
                        <label class="form-label" for="observaciones_decision">Observaciones</label>
                        <textarea class="form-control" id="observaciones_decision" name="observaciones_decision" rows="3" maxlength="1000">{{ old('observaciones_decision') }}</textarea>
                    </div>
                    <div class="d-grid gap-2">
                        <button class="btn btn-success" name="decision" type="submit" value="aprobado"><i class="bi bi-check-lg me-2" aria-hidden="true"></i>Registrar aprobación</button>
                        <button class="btn btn-outline-danger" name="decision" type="submit" value="rechazado"><i class="bi bi-x-lg me-2" aria-hidden="true"></i>Registrar rechazo</button>
                    </div>
                </form>
            </section>
        @else
            <section class="content-card" aria-labelledby="registered-decision-title">
                <div class="form-section-heading">
                    <span class="form-section-icon"><i class="bi bi-check2-square" aria-hidden="true"></i></span>
                    <div><h2 id="registered-decision-title">Decisión registrada</h2><p>La respuesta queda cerrada y auditada.</p></div>
                </div>
                <dl class="decision-detail mb-0">
                    <div><dt>Resultado</dt><dd><span class="state-badge state-{{ $presupuesto->aprobacion->estado }}">{{ ucfirst($presupuesto->aprobacion->estado) }}</span></dd></div>
                    <div><dt>Fecha</dt><dd>{{ $presupuesto->aprobacion->fecha_decision->format('d/m/Y') }}</dd></div>
                    <div><dt>Medio</dt><dd>{{ ucfirst($presupuesto->aprobacion->medio_confirmacion) }}</dd></div>
                    <div><dt>Registró</dt><dd>{{ $presupuesto->aprobacion->registradoPor->name }}</dd></div>
                </dl>
            </section>
        @endif

        <div class="small text-secondary mt-3">Válido hasta el {{ $presupuesto->valido_hasta->format('d/m/Y') }}. Registrado por {{ $presupuesto->registradoPor->name }}.</div>
    </div>
</div>
@endsection
