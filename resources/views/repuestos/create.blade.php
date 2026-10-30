@extends('layouts.app')

@section('title', 'Registrar repuesto')
@section('context', 'Gestión de repuestos')

@section('content')
<section class="mb-4">
    <div class="page-eyebrow">ATSA-34 · Registro</div>
    <div class="d-flex flex-column flex-lg-row align-items-lg-end justify-content-between gap-3">
        <div><h1 class="page-title">Registrar repuesto</h1><p class="page-lead mb-0">Incorporá una pieza al catálogo y dejá disponible su información comercial y de stock.</p></div>
        <a class="btn btn-outline-secondary" href="{{ route('repuestos.index') }}"><i class="bi bi-search me-2" aria-hidden="true"></i>Consultar repuestos</a>
    </div>
</section>
@if (session('status'))<div class="alert alert-success d-flex align-items-center gap-2" role="status"><i class="bi bi-check-circle-fill" aria-hidden="true"></i><span>{{ session('status') }}</span></div>@endif
@if ($errors->any())<div class="alert alert-danger" role="alert"><div class="fw-semibold mb-1">Revisá los datos señalados.</div><div class="small">El repuesto no fue registrado porque hay información incompleta o inválida.</div></div>@endif
<form method="POST" action="{{ route('repuestos.store') }}" novalidate>
    @csrf
    @include('repuestos._fields', ['repuesto' => null])
    <div class="d-flex flex-column flex-sm-row justify-content-end gap-2"><a class="btn btn-light border" href="{{ route('dashboard') }}">Cancelar</a><button class="btn btn-autotech px-4" type="submit"><i class="bi bi-save me-2" aria-hidden="true"></i>Registrar repuesto</button></div>
</form>
@endsection
