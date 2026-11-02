<section class="mt-dashboard" aria-label="Maqueta Mónaco Data Club">
    <header class="mt-header">
        <div><span>AUTOTECH PERFORMANCE CLUB</span><h1>Mónaco<br><em>Data Club</em></h1><p>Precisión técnica con una lectura refinada de cada movimiento del taller.</p></div>
        <div class="mt-emblem"><i class="bi bi-bezier2"></i><span>URBAN<br>CIRCUIT</span><strong>01</strong></div>
    </header>

    <div class="mt-metrics">
        <article><span>TURNOS DE HOY</span><strong>07</strong><small>agenda confirmada</small></article>
        <article><span>VEHÍCULOS EN TALLER</span><strong>12</strong><small>ocupación 72%</small></article>
        <article><span>TIEMPO PROMEDIO</span><strong>2.8<em>h</em></strong><small>−12 min semanal</small></article>
        <article><span>CALIDAD DE SALIDA</span><strong>98<em>%</em></strong><small>últimos 30 días</small></article>
    </div>

    <div class="mt-grid">
        <article class="mt-card mt-circuit">
            <div class="mt-title"><div><span>TRAZADO OPERATIVO</span><h2>Flujo de reparación</h2></div><b>LIVE</b></div>
            <svg viewBox="0 0 620 360" role="img" aria-label="Circuito operativo ilustrativo"><path class="shadow" d="M87 252 C55 189 111 145 174 157 C231 168 235 74 305 70 C377 65 399 136 454 151 C527 171 571 229 522 283 C474 335 416 270 359 286 C284 307 264 243 203 258 C152 270 112 304 87 252Z"/><path class="track" d="M87 252 C55 189 111 145 174 157 C231 168 235 74 305 70 C377 65 399 136 454 151 C527 171 571 229 522 283 C474 335 416 270 359 286 C284 307 264 243 203 258 C152 270 112 304 87 252Z"/><circle cx="174" cy="157" r="7"/><circle cx="305" cy="70" r="7"/><circle cx="454" cy="151" r="7"/><circle cx="359" cy="286" r="7"/><text x="120" y="140">RECEPCIÓN</text><text x="275" y="43">DIAGNÓSTICO</text><text x="460" y="137">TALLER</text><text x="330" y="325">CONTROL</text></svg>
            <div class="mt-track-meta"><span><b>4</b> estaciones</span><span><b>12</b> unidades</span><span><b>84%</b> eficiencia</span></div>
        </article>
        <article class="mt-card mt-telemetry">
            <div class="mt-title"><div><span>TELEMETRÍA</span><h2>Detalle de sesión</h2></div><a href="{{ route('turnos.index') }}">Agenda ↗</a></div>
            <div class="mt-reading"><span>CAPACIDAD</span><strong>72%</strong><i><em style="width:72%"></em></i></div>
            <div class="mt-reading"><span>PRODUCTIVIDAD</span><strong>84%</strong><i><em style="width:84%"></em></i></div>
            <div class="mt-reading"><span>REPUESTOS</span><strong>91%</strong><i><em style="width:91%"></em></i></div>
            <div class="mt-next"><span>PRÓXIMA ENTRADA</span><time>08:30</time><div><b>AA 204 BB</b><small>Service programado</small></div></div>
        </article>
        <article class="mt-card mt-concierge"><span>PRIORIDAD DEL EQUIPO</span><h2>Entregar 4 vehículos antes de las 17:00.</h2><p>Documentación y control final pendientes.</p><a href="{{ route('reparaciones.index') }}">Ver reparaciones <i class="bi bi-arrow-right"></i></a></article>
    </div>
</section>
