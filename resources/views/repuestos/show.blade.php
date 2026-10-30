@extends('layouts.app')

@section('title', $repuesto->codigo)
@section('context', 'Gestión de repuestos')

@section('content')
<section class="mb-4">
    <div class="page-eyebrow">ATSA-37 · Ficha del repuesto</div>
    <div class="d-flex flex-column flex-lg-row align-items-lg-end justify-content-between gap-3">
        <div>
            <div class="d-flex align-items-center gap-3 flex-wrap">
                <h1 class="page-title mb-0">{{ $repuesto->codigo }}</h1>
                <span class="state-badge state-{{ $repuesto->activo ? 'confirmado' : 'cancelado' }}">{{ $repuesto->activo ? 'Activo' : 'Inactivo' }}</span>
            </div>
            <p class="page-lead mb-0 mt-2">{{ $repuesto->nombre }} · {{ $repuesto->marca }}</p>
        </div>
        <div class="d-flex gap-2 flex-wrap"><a class="btn btn-success" href="{{ route('repuestos.stock', $repuesto) }}"><i class="bi bi-box-seam me-2" aria-hidden="true"></i>Actualizar stock</a><a class="btn btn-autotech" href="{{ route('repuestos.edit', $repuesto) }}"><i class="bi bi-pencil me-2" aria-hidden="true"></i>Modificar</a><a class="btn btn-outline-secondary" href="{{ route('repuestos.index') }}"><i class="bi bi-arrow-left me-2" aria-hidden="true"></i>Volver al catálogo</a></div>
    </div>
</section>

<div class="row g-4">
    <div class="col-xl-8">
        <section class="content-card" aria-labelledby="spare-part-detail-title">
            <div class="form-section-heading"><span class="form-section-icon"><i class="bi bi-gear" aria-hidden="true"></i></span><div><h2 id="spare-part-detail-title">Datos del repuesto</h2><p>Información comercial y clasificación de la pieza.</p></div></div>
            <dl class="detail-grid mb-0">
                <div><dt>Tipo</dt><dd>{{ $repuesto->original ? 'Original' : 'Alternativo' }}</dd></div>
                <div><dt>Marca</dt><dd>{{ $repuesto->marca }}</dd></div>
                <div><dt>Precio de venta</dt><dd>${{ number_format((float) $repuesto->precio_venta, 2, ',', '.') }}</dd></div>
                <div><dt>Existencias</dt><dd>{{ $repuesto->stock }} unidades</dd></div>
                <div class="detail-grid-wide"><dt>Descripción</dt><dd>{{ $repuesto->descripcion ?: 'Sin descripción registrada.' }}</dd></div>
            </dl>
        </section>
    </div>

    <div class="col-xl-4">
        <section class="content-card" aria-labelledby="supplier-title">
            <div class="form-section-heading"><span class="form-section-icon"><i class="bi bi-truck" aria-hidden="true"></i></span><div><h2 id="supplier-title">Proveedor</h2><p>Datos de abastecimiento asociados.</p></div></div>
            @if ($repuesto->proveedor)
                <dl class="decision-detail mb-0">
                    <div><dt>Razón social</dt><dd>{{ $repuesto->proveedor->razon_social }}</dd></div>
                    <div><dt>CUIT</dt><dd>{{ $repuesto->proveedor->cuit }}</dd></div>
                    <div><dt>Localidad</dt><dd>{{ $repuesto->proveedor->localidad }}</dd></div>
                    <div><dt>Contacto</dt><dd>{{ $repuesto->proveedor->telefono ?: 'No informado' }}<small class="d-block text-secondary">{{ $repuesto->proveedor->email ?: 'Sin correo registrado' }}</small></dd></div>
                </dl>
            @else
                <p class="text-secondary mb-0">Sin proveedor asignado.</p>
            @endif
        </section>
    </div>
</div>
@endsection
