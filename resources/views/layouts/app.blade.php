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
    <aside class="app-sidebar" data-app-sidebar>
        <a class="sidebar-brand" href="{{ route('dashboard') }}">
            <span class="brand-mark" aria-hidden="true">AT</span>
            <span class="brand-wordmark">AutoTech <small>Sistema de gestión</small></span>
        </a>

        <div class="sidebar-section-label">Operación</div>
        <nav class="sidebar-nav" aria-label="Navegación principal">
            <a class="sidebar-link active" href="{{ route('dashboard') }}" aria-current="page">
                <i class="bi bi-grid-1x2" aria-hidden="true"></i>
                <span>Inicio</span>
            </a>
            @foreach ([
                ['Clientes', 'bi-people'],
                ['Proveedores', 'bi-truck'],
                ['Repuestos', 'bi-gear'],
                ['Compras', 'bi-cart3'],
                ['Reparaciones', 'bi-tools'],
                ['Turnos', 'bi-calendar3'],
                ['Facturación', 'bi-receipt'],
            ] as [$label, $icon])
                <span class="sidebar-link disabled" aria-disabled="true">
                    <i class="bi {{ $icon }}" aria-hidden="true"></i>
                    <span>{{ $label }}</span>
                    <span class="sidebar-link-badge">Próximo</span>
                </span>
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
                    <strong>Panel general</strong>
                </div>
            </div>

            <div class="user-chip">
                <div class="user-avatar" aria-hidden="true">{{ str($user->name)->substr(0, 1)->upper() }}</div>
                <div class="user-meta">
                    <strong>{{ $user->name }}</strong>
                    <small>{{ $user->roles->first()?->display_name ?? 'Usuario autorizado' }}</small>
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
</body>
</html>
