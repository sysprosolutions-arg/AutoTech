@extends('layouts.app')

@section('title', "Modificar {$repuesto->codigo}")
@section('context', 'Gestión de repuestos')

@section('content')
<section class="mb-4"><div class="page-eyebrow">ATSA-35 · Modificación</div><div class="d-flex flex-column flex-lg-row align-items-lg-end justify-content-between gap-3"><div><h1 class="page-title">Modificar repuesto</h1><p class="page-lead mb-0">Actualizá los datos admitidos de {{ $repuesto->codigo }} sin perder su trazabilidad.</p></div><a class="btn btn-outline-secondary" href="{{ route('repuestos.show', $repuesto) }}"><i class="bi bi-arrow-left me-2" aria-hidden="true"></i>Volver al detalle</a></div></section>
@if ($errors->any())<div class="alert alert-danger" role="alert"><div class="fw-semibold mb-1">Revisá los datos señalados.</div><div class="small">Los cambios no fueron guardados porque hay información inválida.</div></div>@endif
<form method="POST" action="{{ route('repuestos.update', $repuesto) }}" novalidate>@csrf @method('PUT') @include('repuestos._fields')<div class="d-flex flex-column flex-sm-row justify-content-end gap-2"><a class="btn btn-light border" href="{{ route('repuestos.show', $repuesto) }}">Cancelar</a><button class="btn btn-autotech px-4" type="submit"><i class="bi bi-save me-2" aria-hidden="true"></i>Guardar cambios</button></div></form>
@endsection
