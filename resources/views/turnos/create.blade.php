@extends('layouts.app')

@section('title', 'Registrar turno')
@section('context', 'Gestión de turnos')

@section('content')
<section class="mb-4">
    <div class="page-eyebrow">ATSA-49 · Registro</div>
    <div class="d-flex flex-column flex-lg-row align-items-lg-end justify-content-between gap-3">
        <div>
            <h1 class="page-title">Registrar turno</h1>
            <p class="page-lead mb-0">Seleccioná un cliente y uno de sus vehículos para programar la evaluación inicial en el taller.</p>
        </div>
        <a class="btn btn-outline-secondary disabled" href="#" aria-disabled="true">
            <i class="bi bi-search me-2" aria-hidden="true"></i>Consultar turnos
        </a>
    </div>
</section>

@if (session('status'))
    <div class="alert alert-success d-flex align-items-center gap-2" role="status">
        <i class="bi bi-check-circle-fill" aria-hidden="true"></i>
        <span>{{ session('status') }}</span>
    </div>
@endif

@if ($errors->any())
    <div class="alert alert-danger" role="alert">
        <div class="fw-semibold mb-1">Revisá los datos señalados.</div>
        <div class="small">El turno no fue registrado porque hay información incompleta o inválida.</div>
    </div>
@endif

<form method="POST" action="{{ route('turnos.store') }}" novalidate>
    @csrf

    <div class="content-card mb-4">
        <div class="form-section-heading">
            <span class="form-section-icon"><i class="bi bi-person-vcard" aria-hidden="true"></i></span>
            <div>
                <h2>Cliente y vehículo</h2>
                <p>Los datos provienen del catálogo precargado para este incremento.</p>
            </div>
        </div>

        <div class="row g-3">
            <div class="col-lg-6">
                <label class="form-label" for="cliente_id">Cliente <span class="required-mark">*</span></label>
                <select class="form-select @error('cliente_id') is-invalid @enderror" id="cliente_id" name="cliente_id" required data-client-select>
                    <option value="">Seleccionar cliente</option>
                    @foreach ($clientes as $cliente)
                        <option value="{{ $cliente->id }}" @selected((string) old('cliente_id') === (string) $cliente->id)>
                            {{ $cliente->apellido }}, {{ $cliente->nombre }} · DNI {{ $cliente->dni }}
                        </option>
                    @endforeach
                </select>
                @error('cliente_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="col-lg-6">
                <label class="form-label" for="vehiculo_id">Vehículo <span class="required-mark">*</span></label>
                <select class="form-select @error('vehiculo_id') is-invalid @enderror" id="vehiculo_id" name="vehiculo_id" required data-vehicle-select>
                    <option value="">Seleccionar vehículo</option>
                    @foreach ($clientes as $cliente)
                        @foreach ($cliente->vehiculos as $vehiculo)
                            <option value="{{ $vehiculo->id }}" data-client-id="{{ $cliente->id }}" @selected((string) old('vehiculo_id') === (string) $vehiculo->id)>
                                {{ $vehiculo->patente }} · {{ $vehiculo->marca }} {{ $vehiculo->modelo }} ({{ $vehiculo->anio }})
                            </option>
                        @endforeach
                    @endforeach
                </select>
                @error('vehiculo_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                <div class="form-text">Solo se muestran vehículos vinculados al cliente elegido.</div>
            </div>
        </div>
    </div>

    <div class="content-card mb-4">
        <div class="form-section-heading">
            <span class="form-section-icon"><i class="bi bi-calendar-event" aria-hidden="true"></i></span>
            <div>
                <h2>Datos del turno</h2>
                <p>Definí la fecha, la hora y el motivo de la atención.</p>
            </div>
        </div>

        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label" for="fecha">Fecha <span class="required-mark">*</span></label>
                <input class="form-control @error('fecha') is-invalid @enderror" id="fecha" name="fecha" type="date" value="{{ old('fecha') }}" min="{{ now()->toDateString() }}" required>
                @error('fecha')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="col-md-6">
                <label class="form-label" for="hora">Hora <span class="required-mark">*</span></label>
                <input class="form-control @error('hora') is-invalid @enderror" id="hora" name="hora" type="time" value="{{ old('hora') }}" required>
                @error('hora')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="col-12">
                <label class="form-label" for="motivo">Motivo <span class="required-mark">*</span></label>
                <textarea class="form-control @error('motivo') is-invalid @enderror" id="motivo" name="motivo" rows="3" maxlength="500" placeholder="Ej.: ruido en el tren delantero, service programado..." required>{{ old('motivo') }}</textarea>
                @error('motivo')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="col-12">
                <label class="form-label" for="observaciones">Observaciones</label>
                <textarea class="form-control @error('observaciones') is-invalid @enderror" id="observaciones" name="observaciones" rows="3" maxlength="1000" placeholder="Información adicional comunicada por el cliente">{{ old('observaciones') }}</textarea>
                @error('observaciones')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        </div>
    </div>

    <div class="d-flex flex-column flex-sm-row justify-content-end gap-2">
        <a class="btn btn-light border" href="{{ route('dashboard') }}">Cancelar</a>
        <button class="btn btn-autotech px-4" type="submit">
            <i class="bi bi-calendar-check me-2" aria-hidden="true"></i>Registrar turno
        </button>
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

            vehicleSelect.disabled = clientId === '';
        };

        clientSelect.addEventListener('change', updateVehicles);
        updateVehicles();
    });
</script>
@endpush
