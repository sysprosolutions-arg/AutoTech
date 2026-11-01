<section class="ss-dashboard" aria-label="Maqueta Atardecer mecánico">
    <header class="ss-hero">
        <div class="ss-sun" aria-hidden="true"></div><div class="ss-road" aria-hidden="true"></div>
        <div class="ss-hero-copy"><span>DOMINGO, 1 DE NOVIEMBRE</span><h1>Todo listo para<br>poner el día en marcha.</h1><p>{{ $preview['appointmentsToday'] }} turnos, {{ $preview['activeRepairs'] }} vehículos en taller y un equipo preparado.</p><a href="{{ route('turnos.index') }}">Ver agenda <i class="bi bi-arrow-right"></i></a></div>
        <div class="ss-weather"><i class="bi bi-sun"></i><strong>24°</strong><span>Buenos Aires<br>Despejado</span></div>
    </header>
    <div class="ss-ribbon">
        @foreach ([['Turnos', $preview['appointmentsToday'], '09:30 próximo'], ['En taller', $preview['activeRepairs'], '4 finalizan hoy'], ['Eficiencia', $preview['completionRate'].'%', '+6% esta semana'], ['Facturación', $preview['monthlyRevenue'], 'proyección mensual']] as [$label,$value,$hint])
            <article><span>{{ $label }}</span><strong>{{ $value }}</strong><small>{{ $hint }}</small></article>
        @endforeach
    </div>
    <div class="ss-content-grid">
        <article class="ss-card ss-timeline"><div class="ss-card-title"><span>En movimiento</span><h2>La mañana del taller</h2></div><ol><li class="done"><time>08:00</time><div><b>Apertura y recepción</b><span>3 vehículos ingresados</span></div></li><li class="active"><time>09:15</time><div><b>Diagnóstico eléctrico</b><span>Chevrolet Onix · AE321JK</span></div></li><li><time>10:00</time><div><b>Control de frenos</b><span>Volkswagen Amarok · AD789GH</span></div></li><li><time>11:30</time><div><b>Entrega programada</b><span>Toyota Corolla · AB123CD</span></div></li></ol></article>
        <article class="ss-card ss-donut-card"><div class="ss-card-title"><span>Capacidad</span><h2>Boxes ocupados</h2></div><div class="ss-donut" style="--value: {{ $preview['occupationRate'] }}"><div><strong>{{ $preview['occupationRate'] }}%</strong><span>4 de 6 boxes</span></div></div><div class="ss-legend"><span><i></i>En uso 4</span><span><i></i>Disponibles 2</span></div></article>
        <article class="ss-card ss-team"><div class="ss-card-title"><span>Equipo</span><h2>Especialistas asignados</h2></div><div><span>MG</span><p><b>Martín Gómez</b><small>Mecánica general · 3 OT</small></p><em>76%</em></div><div><span>LP</span><p><b>Laura Pérez</b><small>Electricidad · 2 OT</small></p><em>54%</em></div><div><span>RS</span><p><b>Raúl Sosa</b><small>Frenos · 2 OT</small></p><em>62%</em></div></article>
    </div>
</section>
