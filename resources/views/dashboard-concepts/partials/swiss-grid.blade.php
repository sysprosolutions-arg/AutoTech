<section class="sg-dashboard" aria-label="Maqueta Sistema suizo">
    <header class="sg-header"><div><b>AT</b><span>AUTOTECH<br>OPERATIONS</span></div><div><span>PANEL GENERAL</span><strong>01—11—26</strong></div><p>Información esencial para<br>decisiones operativas.</p></header>
    <div class="sg-rule"><span>ESTADO DEL SISTEMA</span><b>OPERACIÓN NORMAL</b><i></i></div>

    <div class="sg-hero-grid">
        <article class="sg-main"><span>REPARACIONES ACTIVAS</span><strong>12</strong><div><b>04</b><small>finalizan hoy</small></div></article>
        <article class="sg-kpi"><span>TURNOS</span><strong>07</strong><small>HOY</small></article>
        <article class="sg-kpi red"><span>PENDIENTES</span><strong>04</strong><small>PRESUPUESTOS</small></article>
        <article class="sg-kpi"><span>EFICIENCIA</span><strong>84</strong><small>POR CIENTO</small></article>
    </div>

    <div class="sg-data-grid">
        <article class="sg-schedule"><div class="sg-title"><span>01</span><h2>Agenda próxima</h2><a href="{{ route('turnos.index') }}">TODOS LOS TURNOS ↗</a></div><table><thead><tr><th>Hora</th><th>Vehículo</th><th>Trabajo</th><th>Estado</th></tr></thead><tbody><tr><td>08:30</td><td>AA 204 BB</td><td>Service</td><td><i></i>Confirmado</td></tr><tr><td>10:00</td><td>AB 718 KT</td><td>Frenos</td><td><i></i>En camino</td></tr><tr><td>11:30</td><td>AC 113 PV</td><td>Motor</td><td><i></i>Confirmado</td></tr></tbody></table></article>
        <article class="sg-capacity"><div class="sg-title"><span>02</span><h2>Capacidad semanal</h2></div>@foreach ([['Lun',68],['Mar',84],['Mié',57],['Jue',91],['Vie',76]] as [$day,$value])<div><span>{{ $day }}</span><i><em style="width: {{ $value }}%"></em></i><b>{{ $value }}</b></div>@endforeach</article>
        <article class="sg-alert"><div class="sg-title"><span>03</span><h2>Atención</h2></div><strong>3</strong><p>repuestos alcanzaron el nivel mínimo de existencias.</p><a href="{{ route('repuestos.index') }}">REVISAR INVENTARIO →</a></article>
    </div>
</section>
