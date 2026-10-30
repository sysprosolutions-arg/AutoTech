@extends('layouts.app')

@section('title', 'Registrar repuesto')
@section('context', 'Gestión de repuestos')

@section('content')
<section class="mb-4">
    <div class="page-eyebrow">ATSA-34 · Registro</div>
    <div class="d-flex flex-column flex-lg-row align-items-lg-end justify-content-between gap-3">
        <div>
            <h1 class="page-title">Registrar repuesto</h1>
            <p class="page-lead mb-0">Incorporá una pieza al catálogo y dejá disponible su información comercial y de stock.</p>
        </div>
        <a class="btn btn-outline-secondary" href="{{ route('repuestos.index') }}"><i class="bi bi-search me-2" aria-hidden="true"></i>Consultar repuestos</a>
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
        <div class="small">El repuesto no fue registrado porque hay información incompleta o inválida.</div>
    </div>
@endif

<form method="POST" action="{{ route('repuestos.store') }}" novalidate>
    @csrf

    <div class="content-card mb-4">
        <div class="form-section-heading">
            <span class="form-section-icon"><i class="bi bi-upc-scan" aria-hidden="true"></i></span>
            <div><h2>Identificación</h2><p>Datos que permiten encontrar y reconocer la pieza en el catálogo.</p></div>
        </div>

        <div class="row g-3">
            <div class="col-md-4">
                <label class="form-label" for="codigo">Código <span class="required-mark">*</span></label>
                <input class="form-control @error('codigo') is-invalid @enderror" id="codigo" name="codigo" type="text" maxlength="50" value="{{ old('codigo') }}" placeholder="Ej.: REP-FIL-002" required>
                @error('codigo')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-8">
                <label class="form-label" for="nombre">Nombre <span class="required-mark">*</span></label>
                <input class="form-control @error('nombre') is-invalid @enderror" id="nombre" name="nombre" type="text" maxlength="150" value="{{ old('nombre') }}" placeholder="Ej.: Filtro de aire" required>
                @error('nombre')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-12">
                <label class="form-label" for="descripcion">Descripción <span class="required-mark">*</span></label>
                <textarea class="form-control @error('descripcion') is-invalid @enderror" id="descripcion" name="descripcion" rows="3" maxlength="500" placeholder="Características, compatibilidad y especificaciones principales." required>{{ old('descripcion') }}</textarea>
                @error('descripcion')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        </div>
    </div>

    <div class="content-card mb-4">
        <div class="form-section-heading">
            <span class="form-section-icon"><i class="bi bi-tags" aria-hidden="true"></i></span>
            <div><h2>Clasificación y abastecimiento</h2><p>Marca, categoría y proveedor responsable del suministro.</p></div>
        </div>

        <div class="row g-3">
            <div class="col-md-4">
                <label class="form-label" for="marca">Marca <span class="required-mark">*</span></label>
                <input class="form-control @error('marca') is-invalid @enderror" id="marca" name="marca" type="text" maxlength="100" value="{{ old('marca') }}" placeholder="Ej.: Bosch" required>
                @error('marca')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-4">
                <label class="form-label" for="tipo">Tipo <span class="required-mark">*</span></label>
                <select class="form-select @error('tipo') is-invalid @enderror" id="tipo" name="tipo" required>
                    <option value="">Seleccionar</option>
                    <option value="original" @selected(old('tipo') === 'original')>Original</option>
                    <option value="alternativo" @selected(old('tipo') === 'alternativo')>Alternativo</option>
                </select>
                @error('tipo')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-4">
                <label class="form-label" for="proveedor_id">Proveedor <span class="required-mark">*</span></label>
                <select class="form-select @error('proveedor_id') is-invalid @enderror" id="proveedor_id" name="proveedor_id" required>
                    <option value="">Seleccionar</option>
                    @foreach ($proveedores as $proveedor)
                        <option value="{{ $proveedor->id }}" @selected((string) old('proveedor_id') === (string) $proveedor->id)>{{ $proveedor->razon_social }} · {{ $proveedor->localidad }}</option>
                    @endforeach
                </select>
                @error('proveedor_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        </div>
    </div>

    <div class="content-card mb-4">
        <div class="form-section-heading">
            <span class="form-section-icon"><i class="bi bi-box-seam" aria-hidden="true"></i></span>
            <div><h2>Precio y disponibilidad</h2><p>Valores iniciales utilizados por presupuestos y control de existencias.</p></div>
        </div>

        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label" for="precio_venta">Precio de venta <span class="required-mark">*</span></label>
                <div class="input-group">
                    <span class="input-group-text">$</span>
                    <input class="form-control @error('precio_venta') is-invalid @enderror" id="precio_venta" name="precio_venta" type="number" min="0" max="9999999999.99" step="0.01" value="{{ old('precio_venta') }}" placeholder="0,00" required>
                    @error('precio_venta')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>
            <div class="col-md-6">
                <label class="form-label" for="stock">Stock inicial <span class="required-mark">*</span></label>
                <input class="form-control @error('stock') is-invalid @enderror" id="stock" name="stock" type="number" min="0" max="1000000" value="{{ old('stock', 0) }}" required>
                @error('stock')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        </div>
    </div>

    <div class="d-flex flex-column flex-sm-row justify-content-end gap-2">
        <a class="btn btn-light border" href="{{ route('dashboard') }}">Cancelar</a>
        <button class="btn btn-autotech px-4" type="submit"><i class="bi bi-save me-2" aria-hidden="true"></i>Registrar repuesto</button>
    </div>
</form>
@endsection
