@extends('layouts.app')

@section('title', "Modificar turno #{$turno->id}")
@section('context', 'Gestión de turnos')

@section('content')
<section class="mb-4">
    <div class="page-eyebrow">Agenda del taller · Modificación</div>
    <div class="d-flex flex-column flex-lg-row align-items-lg-end justify-content-between gap-3">
        <div>
            <h1 class="page-title">Modificar turno #{{ $turno->id }}</h1>
            <p class="page-lead mb-0">Actualizá los datos permitidos y el estado de la atención.</p>
        </div>
        <a class="btn btn-outline-secondary" href="{{ route('turnos.show', $turno) }}">
            <i class="bi bi-arrow-left me-2" aria-hidden="true"></i>Volver al detalle
        </a>
    </div>
</section>

@if ($errors->any())
    <div class="alert alert-danger" role="alert">
        <div class="fw-semibold mb-1">Revisá los datos señalados.</div>
        <div class="small">El turno conserva su información anterior porque la modificación no pudo guardarse.</div>
    </div>
@endif

<form method="POST" action="{{ route('turnos.update', $turno) }}" novalidate>
    @csrf
    @method('PUT')

    <div class="content-card mb-4">
        <div class="form-section-heading">
            <span class="form-section-icon"><i class="bi bi-person-vcard" aria-hidden="true"></i></span>
            <div><h2>Cliente y vehículo</h2><p>Solo se ofrecen clientes y vehículos activos.</p></div>
        </div>

        <div class="row g-3">
            <div class="col-lg-6">
                <label class="form-label" for="cliente_id">Cliente <span class="required-mark">*</span></label>
                <select class="form-select @error('cliente_id') is-invalid @enderror" id="cliente_id" name="cliente_id" required data-client-select>
                    @foreach ($clientes as $cliente)
                        <option value="{{ $cliente->id }}" @selected((string) old('cliente_id', $turno->cliente_id) === (string) $cliente->id)>
                            {{ $cliente->apellido }}, {{ $cliente->nombre }} · DNI {{ $cliente->dni }}
                        </option>
                    @endforeach
                </select>
                @error('cliente_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="col-lg-6">
                <label class="form-label" for="vehiculo_id">Vehículo <span class="required-mark">*</span></label>
                <select class="form-select @error('vehiculo_id') is-invalid @enderror" id="vehiculo_id" name="vehiculo_id" required data-vehicle-select>
                    @foreach ($clientes as $cliente)
                        @foreach ($cliente->vehiculos as $vehiculo)
                            <option value="{{ $vehiculo->id }}" data-client-id="{{ $cliente->id }}" @selected((string) old('vehiculo_id', $turno->vehiculo_id) === (string) $vehiculo->id)>
                                {{ $vehiculo->patente }} · {{ $vehiculo->marca }} {{ $vehiculo->modelo }} ({{ $vehiculo->anio }})
                            </option>
                        @endforeach
                    @endforeach
                </select>
                @error('vehiculo_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        </div>
    </div>

    <div class="content-card mb-4">
        <div class="form-section-heading">
            <span class="form-section-icon"><i class="bi bi-calendar-event" aria-hidden="true"></i></span>
            <div><h2>Datos del turno</h2><p>Los cambios de estado respetan el ciclo operativo permitido.</p></div>
        </div>

        <div class="row g-3">
            <div class="col-md-4">
                <label class="form-label" for="fecha">Fecha <span class="required-mark">*</span></label>
                <input class="form-control @error('fecha') is-invalid @enderror" id="fecha" name="fecha" type="date" value="{{ old('fecha', $turno->fecha->toDateString()) }}" min="{{ now()->toDateString() }}" required>
                @error('fecha')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="col-md-4">
                <label class="form-label" for="hora">Hora <span class="required-mark">*</span></label>
                <select class="form-select @error('hora') is-invalid @enderror" id="hora" name="hora" required>
                    @for ($minutos = 8 * 60; $minutos <= 17 * 60; $minutos += 30)
                        @php($horario = sprintf('%02d:%02d', intdiv($minutos, 60), $minutos % 60))
                        <option value="{{ $horario }}" @selected(old('hora', substr((string) $turno->hora, 0, 5)) === $horario)>{{ $horario }} h</option>
                    @endfor
                </select>
                @error('hora')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="col-md-4">
                <label class="form-label" for="estado_turno_id">Estado <span class="required-mark">*</span></label>
                <select class="form-select @error('estado_turno_id') is-invalid @enderror" id="estado_turno_id" name="estado_turno_id" required>
                    @foreach ($estados as $estado)
                        <option value="{{ $estado->id }}" @selected((string) old('estado_turno_id', $turno->estado_turno_id) === (string) $estado->id)>{{ $estado->nombre }}</option>
                    @endforeach
                </select>
                @error('estado_turno_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="col-12">
                <label class="form-label" for="motivo">Motivo <span class="required-mark">*</span></label>
                <textarea class="form-control @error('motivo') is-invalid @enderror" id="motivo" name="motivo" rows="3" maxlength="500" required>{{ old('motivo', $turno->motivo) }}</textarea>
                @error('motivo')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="col-12">
                <label class="form-label" for="observaciones">Observaciones</label>
                <textarea class="form-control @error('observaciones') is-invalid @enderror" id="observaciones" name="observaciones" rows="3" maxlength="1000">{{ old('observaciones', $turno->observaciones) }}</textarea>
                @error('observaciones')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        </div>
    </div>

    <div class="d-flex flex-column flex-sm-row justify-content-end gap-2">
        <a class="btn btn-light border" href="{{ route('turnos.show', $turno) }}">Cancelar</a>
        <button class="btn btn-autotech px-4" type="submit"><i class="bi bi-check2-circle me-2" aria-hidden="true"></i>Guardar cambios</button>
    </div>
</form>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const clientSelect = document.querySelector('[data-client-select]');
        const vehicleSelect = document.querySelector('[data-vehicle-select]');

        if (!clientSelect || !vehicleSelect) {
            return;
        }

        const updateVehicles = () => {
            const clientId = clientSelect.value;

            vehicleSelect.querySelectorAll('option[data-client-id]').forEach((option) => {
                option.hidden = option.dataset.clientId !== clientId;
            });

            const selectedOption = vehicleSelect.options[vehicleSelect.selectedIndex];
            if (selectedOption?.dataset.clientId !== clientId) {
                vehicleSelect.value = '';
            }
        };

        clientSelect.addEventListener('change', updateVehicles);
        updateVehicles();
    });
</script>
@endpush
