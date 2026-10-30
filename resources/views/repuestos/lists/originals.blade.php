@extends('layouts.app')

@section('title', 'Repuestos originales')
@section('context', 'Listados de repuestos')

@section('content')
<section class="mb-4">
    <div class="page-eyebrow">ATSA-41 · Listado</div>
    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-end gap-3">
        <div>
            <h1 class="page-title">Repuestos originales</h1>
            <p class="page-lead mb-0">Piezas activas provistas o avaladas por el fabricante del vehículo.</p>
        </div>
        <div class="d-flex gap-2 report-screen-actions">
            <button class="btn btn-outline-secondary" type="button" data-print-report>
                <i class="bi bi-printer me-2" aria-hidden="true"></i>Imprimir
            </button>
            <a class="btn btn-outline-secondary" href="{{ route('repuestos.index') }}">Volver al catálogo</a>
        </div>
    </div>
</section>

@include('repuestos.lists.partials.spare-parts-table', [
    'ariaLabel' => 'Listado de repuestos originales',
    'emptyIcon' => 'bi-patch-check',
    'emptyTitle' => 'No hay repuestos originales activos',
    'emptyDescription' => 'El listado se actualizará cuando se registre una pieza original.',
])
@endsection
