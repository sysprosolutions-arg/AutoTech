<section class="wa-dashboard" aria-label="Maqueta Atelier blanco">
    <header class="wa-header">
        <div><span>AUTOTECH / DOMINGO 01</span><h1>Orden para avanzar.</h1><p>Una mirada precisa, serena y completamente enfocada en el trabajo de hoy.</p></div>
        <div class="wa-status"><i class="bi bi-check2-circle"></i><span>Operación estable</span><strong>84%</strong></div>
    </header>

    <div class="wa-metrics">
        <article><span>01 / Turnos</span><strong>07</strong><small>2 próximos</small></article>
        <article><span>02 / En taller</span><strong>12</strong><small>4 finalizan hoy</small></article>
        <article><span>03 / Presupuestos</span><strong>04</strong><small>requieren decisión</small></article>
        <article><span>04 / Stock</span><strong>03</strong><small>alertas activas</small></article>
    </div>

    <div class="wa-grid">
        <article class="wa-card wa-production">
            <div class="wa-title"><div><span>CAPACIDAD SEMANAL</span><h2>Ritmo del taller</h2></div><b>+18%</b></div>
            <div class="wa-bars" aria-label="Carga semanal ilustrativa">
                @foreach ([42, 68, 53, 84, 72, 92, 61] as $value)<i style="height: {{ $value }}%"><span>{{ $value }}</span></i>@endforeach
            </div>
            <div class="wa-days"><span>LU</span><span>MA</span><span>MI</span><span>JU</span><span>VI</span><span>SÁ</span><span>DO</span></div>
        </article>
        <article class="wa-card wa-agenda">
            <div class="wa-title"><div><span>AGENDA INMEDIATA</span><h2>Próximos ingresos</h2></div><a href="{{ route('turnos.index') }}">Ver agenda</a></div>
            <div><time>08:30</time><span><b>AA 204 BB</b><small>Service programado</small></span><em>Confirmado</em></div>
            <div><time>10:00</time><span><b>AB 718 KT</b><small>Diagnóstico eléctrico</small></span><em>En espera</em></div>
            <div><time>11:30</time><span><b>AC 113 PV</b><small>Tren delantero</small></span><em>Confirmado</em></div>
        </article>
        <article class="wa-card wa-priority">
            <span>PRIORIDAD DEL DÍA</span><strong>4</strong><h2>vehículos listos para entregar</h2><a href="{{ route('reparaciones.index') }}">Revisar reparaciones <i class="bi bi-arrow-right"></i></a>
        </article>
    </div>
</section>
