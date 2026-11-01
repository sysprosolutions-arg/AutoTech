@extends('layouts.app')

@section('title', 'Panel general')

@section('content')
<section class="ops-hero mb-4">
    <div>
        <div class="page-eyebrow">Centro operativo</div>
        <h1 class="page-title">Buen día, {{ str($user->name)->before(' ') }}.</h1>
        <p class="page-lead mb-0">Una mirada rápida al movimiento del taller, las prioridades del día y el estado del inventario.</p>
    </div>
    <a class="btn btn-outline-secondary ops-concepts-link" href="{{ route('dashboard.concepts.index') }}">
        <i class="bi bi-palette2 me-2" aria-hidden="true"></i>Explorar 6 propuestas visuales
    </a>
</section>

<section class="ops-metrics mb-4" aria-label="Indicadores principales">
    @foreach ([
        ['Turnos de hoy', $metrics['appointmentsToday'], 'bi-calendar2-check', 'teal'],
        ['Reparaciones activas', $metrics['activeRepairs'], 'bi-tools', 'blue'],
        ['Presupuestos pendientes', $metrics['pendingBudgets'], 'bi-file-earmark-text', 'amber'],
        ['Alertas de stock', $metrics['lowStock'], 'bi-exclamation-triangle', 'coral'],
        ['Clientes activos', $metrics['activeClients'], 'bi-people', 'purple'],
        ['Proveedores activos', $metrics['activeSuppliers'], 'bi-truck', 'green'],
    ] as [$label, $value, $icon, $tone])
        <article class="ops-metric-card tone-{{ $tone }}">
            <div class="ops-metric-icon"><i class="bi {{ $icon }}" aria-hidden="true"></i></div>
            <div>
                <strong>{{ $value }}</strong>
                <span>{{ $label }}</span>
            </div>
        </article>
    @endforeach
</section>

<div class="row g-4 mb-4">
    <div class="col-xl-7">
        <section class="content-card ops-chart-card h-100" aria-labelledby="weekly-load-title">
            <div class="ops-card-heading">
                <div>
                    <span>Actividad reciente</span>
                    <h2 id="weekly-load-title">Turnos registrados</h2>
                </div>
                <span class="ops-live-chip"><i class="bi bi-activity" aria-hidden="true"></i> Últimos 7 días</span>
            </div>
            @php($maxWeeklyLoad = max(1, (int) $weeklyLoad->max('value')))
            <div class="ops-bar-chart" aria-label="Cantidad de turnos por día">
                @foreach ($weeklyLoad as $day)
                    <div class="ops-bar-column">
                        <span class="ops-bar-value">{{ $day['value'] }}</span>
                        <div class="ops-bar-track"><span style="height: {{ max(8, ($day['value'] / $maxWeeklyLoad) * 100) }}%"></span></div>
                        <small>{{ $day['label'] }}</small>
                    </div>
                @endforeach
            </div>
        </section>
    </div>
    <div class="col-xl-5">
        <section class="content-card h-100" aria-labelledby="agenda-title">
            <div class="ops-card-heading">
                <div><span>Agenda</span><h2 id="agenda-title">Próximos turnos</h2></div>
                <a href="{{ route('turnos.index') }}">Ver agenda</a>
            </div>
            @if ($upcomingAppointments->isEmpty())
                <div class="ops-compact-empty"><i class="bi bi-calendar2-plus" aria-hidden="true"></i><span>No hay turnos próximos registrados.</span></div>
            @else
                <div class="ops-agenda-list">
                    @foreach ($upcomingAppointments as $turno)
                        <article>
                            <time datetime="{{ $turno->fecha->format('Y-m-d') }}"><strong>{{ $turno->fecha->format('d') }}</strong><span>{{ $turno->fecha->locale('es')->isoFormat('MMM') }}</span></time>
                            <div><strong>{{ $turno->hora }} · {{ $turno->vehiculo->patente }}</strong><span>{{ $turno->cliente->nombre }} {{ $turno->cliente->apellido }} · {{ $turno->motivo }}</span></div>
                            <span class="state-badge state-{{ $turno->estado->codigo }}">{{ $turno->estado->nombre }}</span>
                        </article>
                    @endforeach
                </div>
            @endif
        </section>
    </div>
</div>

<section class="mb-4" aria-labelledby="modules-title">
    <div class="d-flex align-items-end justify-content-between mb-3">
        <div>
            <h2 class="h4 fw-bold mb-1" id="modules-title">Módulos del sistema</h2>
            <p class="text-secondary small mb-0">Accesos y responsabilidades de AutoTech S.A.</p>
        </div>
        <span class="ops-module-count">7 áreas</span>
    </div>

    <div class="row g-3">
        @foreach ($modules as $module)
            <div class="col-sm-6 col-xl-3">
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

<section class="content-card" aria-labelledby="stock-alerts-title">
    <div class="ops-card-heading">
        <div><span>Inventario</span><h2 id="stock-alerts-title">Repuestos que requieren atención</h2></div>
        @if ($user->roles->pluck('name')->intersect(['administrador', 'repuestos'])->isNotEmpty())
            <a href="{{ route('repuestos.index') }}">Abrir repuestos</a>
        @endif
    </div>
    @if ($stockAlerts->isEmpty())
        <div class="ops-compact-empty is-success"><i class="bi bi-check2-circle" aria-hidden="true"></i><span>No hay alertas de stock bajo.</span></div>
    @else
        <div class="ops-stock-grid">
            @foreach ($stockAlerts as $repuesto)
                <article><div><strong>{{ $repuesto->codigo }}</strong><span>{{ $repuesto->nombre }}</span></div><b>{{ $repuesto->stock }} u.</b></article>
            @endforeach
        </div>
    @endif
</section>
@endsection
