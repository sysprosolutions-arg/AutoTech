@extends('layouts.app')

@section('title', 'Panel general')

@section('content')
<section class="mb-4">
    <div class="page-eyebrow">Centro operativo</div>
    <h1 class="page-title">Buen día, {{ str($user->name)->before(' ') }}.</h1>
    <p class="page-lead mb-0">La base técnica está activa. Desde este panel se incorporarán progresivamente las gestiones definidas para AutoTech S.A.</p>
</section>

<section class="status-panel mb-4" aria-labelledby="system-status-title">
    <div class="row align-items-end g-4 position-relative z-1">
        <div class="col-lg-8">
            <div class="status-pill mb-3"><span class="status-dot"></span> Sistema operativo</div>
            <h2 class="h3 fw-bold" id="system-status-title">Incremento técnico inicial</h2>
            <p class="mb-0 text-white-50">Repositorio, arquitectura base, autenticación por sesión y control de acceso preparados para continuar el desarrollo.</p>
        </div>
        <div class="col-lg-4 text-lg-end">
            <div class="small text-white-50">Alcance actual</div>
            <div class="fs-5 fw-semibold">ATSA-5 · ATSA-6 · ATSA-7</div>
        </div>
    </div>
</section>

<section class="mb-4" aria-labelledby="modules-title">
    <div class="d-flex align-items-end justify-content-between mb-3">
        <div>
            <div class="page-eyebrow">Mapa funcional</div>
            <h2 class="h4 fw-bold mt-1 mb-0" id="modules-title">Módulos del sistema</h2>
        </div>
        <span class="module-state">Estructura preparada</span>
    </div>

    <div class="row g-3">
        @foreach ($modules as $module)
            <div class="col-sm-6 col-xl-4">
                <article class="module-card position-relative">
                    <div class="d-flex align-items-start justify-content-between">
                        <div class="module-icon {{ $module['color'] }}"><i class="bi {{ $module['icon'] }}" aria-hidden="true"></i></div>
                        <span class="module-state">{{ isset($module['route']) ? 'Disponible' : 'Próximamente' }}</span>
                    </div>
                    <h3>{{ $module['name'] }}</h3>
                    <p>{{ $module['description'] }}</p>
                    @isset($module['route'])
                        <a class="stretched-link" href="{{ route($module['route']) }}"><span class="visually-hidden">Abrir {{ $module['name'] }}</span></a>
                    @endisset
                </article>
            </div>
        @endforeach
    </div>
</section>

<section class="roadmap-card" aria-labelledby="roadmap-title">
    <div class="page-eyebrow">Trazabilidad</div>
    <h2 class="h5 fw-bold mt-1 mb-4" id="roadmap-title">Base del incremento</h2>
    <div class="row g-4">
        <div class="col-lg-4">
            <div class="roadmap-step active">
                <div class="roadmap-number">1</div>
                <div><strong>Repositorio y estándares</strong><span>Versionado, documentación y estrategia Git.</span></div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="roadmap-step active">
                <div class="roadmap-number">2</div>
                <div><strong>Arquitectura ejecutable</strong><span>Laravel, Bootstrap y persistencia configurada.</span></div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="roadmap-step active pb-0">
                <div class="roadmap-number">3</div>
                <div><strong>Acceso seguro</strong><span>Login, sesiones, rutas protegidas y cierre de sesión.</span></div>
            </div>
        </div>
    </div>
</section>
@endsection
