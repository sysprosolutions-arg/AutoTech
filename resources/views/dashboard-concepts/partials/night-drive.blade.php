<section class="nd-dashboard" aria-label="Maqueta Ruta nocturna">
    <header class="nd-header"><div><span>AUTOTECH / LIVE OPS</span><h1>NIGHT<span>DRIVE</span></h1></div><div class="nd-clock"><strong>08:24</strong><span>01 NOV 2026</span></div></header>
    <div class="nd-route-card">
        <div class="nd-route-copy"><span>RITMO DEL TALLER</span><h2>{{ $preview['completionRate'] }}<sup>%</sup></h2><p>Objetivo diario completado</p><div><b>16</b><span>órdenes resueltas</span><b>03</b><span>en riesgo</span></div></div>
        <svg viewBox="0 0 850 250" aria-hidden="true"><path class="ghost" d="M0 190 C110 235 125 20 255 73 S400 224 520 134 S665 5 850 70"/><path class="route" d="M0 190 C110 235 125 20 255 73 S400 224 520 134 S665 5 850 70"/><circle cx="255" cy="73" r="9"/><circle cx="520" cy="134" r="9"/><circle cx="850" cy="70" r="9"/></svg>
    </div>
    <div class="nd-metrics">
        @foreach ([['Turnos', $preview['appointmentsToday'], 'bi-calendar4-week'], ['Reparaciones', $preview['activeRepairs'], 'bi-wrench-adjustable-circle'], ['Pendientes', $preview['pendingBudgets'], 'bi-hourglass-split'], ['Stock bajo', $preview['lowStock'], 'bi-box2-heart']] as [$label,$value,$icon])<article><i class="bi {{ $icon }}"></i><span>{{ $label }}</span><strong>{{ str_pad((string)$value, 2, '0', STR_PAD_LEFT) }}</strong><small>↗ operativo</small></article>@endforeach
    </div>
    <div class="nd-lower-grid">
        <article class="nd-heat"><div class="nd-section-title"><span>INTENSIDAD / 7 DÍAS</span><h2>Mapa de carga</h2></div><div class="nd-heat-grid">@foreach (range(1, 42) as $cell)<i style="--level: {{ (($cell * 7) % 5) + 1 }}"></i>@endforeach</div><div class="nd-heat-label"><span>Lun</span><span>Mar</span><span>Mié</span><span>Jue</span><span>Vie</span><span>Sáb</span><span>Dom</span></div></article>
        <article class="nd-feed"><div class="nd-section-title"><span>SEÑALES</span><h2>Radar operativo</h2></div><ul><li><i></i><p><b>Box 04</b><span>Diagnóstico finalizado</span></p><time>hace 4m</time></li><li><i></i><p><b>REP-BAT-001</b><span>Stock mínimo alcanzado</span></p><time>hace 12m</time></li><li><i></i><p><b>OT-00019</b><span>Control de calidad pendiente</span></p><time>hace 18m</time></li></ul></article>
    </div>
</section>
