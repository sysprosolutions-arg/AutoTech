<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Panel') · {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="app-body">
    @php($currentUser = auth()->user())
    <aside class="app-sidebar" data-app-sidebar>
        <a class="sidebar-brand" href="{{ route('dashboard') }}">
            <span class="brand-mark" aria-hidden="true">AT</span>
            <span class="brand-wordmark">AutoTech <small>Sistema de gestión</small></span>
        </a>

        <div class="sidebar-section-label">Operación</div>
        <nav class="sidebar-nav" aria-label="Navegación principal">
            <a class="sidebar-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}" @if(request()->routeIs('dashboard')) aria-current="page" @endif>
                <i class="bi bi-grid-1x2" aria-hidden="true"></i>
                <span>Inicio</span>
            </a>
            @foreach ([
                ['Clientes', 'bi-people', null],
                ['Proveedores', 'bi-truck', null],
                ['Repuestos', 'bi-gear', $currentUser?->roles->pluck('name')->intersect(['administrador', 'repuestos'])->isNotEmpty() ? 'repuestos.create' : null],
                ['Compras', 'bi-cart3', null],
                ['Reparaciones', 'bi-tools', $currentUser?->roles->pluck('name')->intersect(['administrador', 'reparacion'])->isNotEmpty() ? 'reparaciones.index' : null],
                ['Turnos', 'bi-calendar3', 'turnos.index'],
                ['Facturación', 'bi-receipt', null],
            ] as [$label, $icon, $routeName])
                @if ($routeName)
                    @php($routePattern = $routeName === 'reparaciones.index' ? 'reparaciones.*' : ($routeName === 'repuestos.create' ? 'repuestos.*' : 'turnos.*'))
                    <a class="sidebar-link {{ request()->routeIs($routePattern) ? 'active' : '' }}" href="{{ route($routeName) }}" @if(request()->routeIs($routePattern)) aria-current="page" @endif>
                        <i class="bi {{ $icon }}" aria-hidden="true"></i>
                        <span>{{ $label }}</span>
                    </a>
                @else
                    <span class="sidebar-link disabled" aria-disabled="true">
                        <i class="bi {{ $icon }}" aria-hidden="true"></i>
                        <span>{{ $label }}</span>
                        <span class="sidebar-link-badge">Próximo</span>
                    </span>
                @endif
            @endforeach
        </nav>

        <div class="sidebar-footer">
            <small>AutoTech S.A.</small>
            <div class="small mt-1">Base técnica · v0.1.0</div>
        </div>
    </aside>

    <main class="app-main">
        <header class="app-topbar">
            <div class="d-flex align-items-center gap-3">
                <button class="sidebar-toggle" type="button" data-sidebar-toggle aria-label="Abrir navegación">
                    <i class="bi bi-list" aria-hidden="true"></i>
                </button>
                <div class="topbar-context">
                    <small>Área de trabajo</small>
                    <strong>@yield('context', 'Panel general')</strong>
                </div>
            </div>

            <div class="user-chip">
                <div class="user-avatar" aria-hidden="true">{{ str($currentUser?->name)->substr(0, 1)->upper() }}</div>
                <div class="user-meta">
                    <strong>{{ $currentUser?->name }}</strong>
                    <small>{{ $currentUser?->roles->first()?->display_name ?? 'Usuario autorizado' }}</small>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="btn btn-sm btn-outline-secondary border-0" type="submit" title="Cerrar sesión">
                        <i class="bi bi-box-arrow-right" aria-hidden="true"></i>
                        <span class="visually-hidden">Cerrar sesión</span>
                    </button>
                </form>
            </div>
        </header>

        <div class="app-content">
            @yield('content')
        </div>
    </main>
    @stack('scripts')
</body>
</html>
