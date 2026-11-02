@php
    $navigationUser = $user ?? auth()->user();
    $navigationRoleNames = $navigationUser?->roles?->pluck('name') ?? collect();
    $navigationModules = $modules ?? [
        ['name' => 'Clientes', 'icon' => 'bi-people', 'route' => null, 'pattern' => null],
        ['name' => 'Proveedores', 'icon' => 'bi-truck', 'route' => null, 'pattern' => null],
        ['name' => 'Turnos', 'icon' => 'bi-calendar3', 'route' => 'turnos.index', 'pattern' => 'turnos.*'],
        ['name' => 'Reparaciones', 'icon' => 'bi-tools', 'route' => $navigationRoleNames->intersect(['administrador', 'reparacion'])->isNotEmpty() ? 'reparaciones.index' : null, 'pattern' => 'reparaciones.*'],
        ['name' => 'Repuestos', 'icon' => 'bi-gear', 'route' => $navigationRoleNames->intersect(['administrador', 'repuestos'])->isNotEmpty() ? 'repuestos.index' : null, 'pattern' => 'repuestos.*'],
        ['name' => 'Compras', 'icon' => 'bi-cart3', 'route' => null, 'pattern' => null],
        ['name' => 'Facturación', 'icon' => 'bi-receipt', 'route' => null, 'pattern' => null],
    ];
@endphp

<nav class="f1-top-nav" aria-label="Áreas de trabajo">
    <a class="f1-top-brand" href="{{ route('dashboard') }}" title="Inicio"><span>AT</span><strong>AutoTech<small>Operaciones del taller</small></strong></a>
    <div class="f1-top-modules">
        @foreach (array_slice($navigationModules, 0, 7) as $module)
            @if ($module['route'] ?? null)
                @php($modulePattern = $module['pattern'] ?? str($module['route'])->before('.').'.*')
                <a class="{{ request()->routeIs($modulePattern) ? 'active' : '' }}" href="{{ route($module['route']) }}" @if(request()->routeIs($modulePattern)) aria-current="page" @endif><i class="bi {{ $module['icon'] }}" aria-hidden="true"></i><span>{{ $module['name'] }}</span></a>
            @else
                <span aria-disabled="true"><i class="bi {{ $module['icon'] }}" aria-hidden="true"></i><span>{{ $module['name'] }}</span></span>
            @endif
        @endforeach
    </div>
</nav>
