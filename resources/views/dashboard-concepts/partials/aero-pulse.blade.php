<section class="ap-dashboard" aria-label="Maqueta Aero Pulse">
    <div class="ap-flow ap-flow-one" aria-hidden="true"></div><div class="ap-flow ap-flow-two" aria-hidden="true"></div>
    <header class="ap-header">
        <div><span>AUTOTECH / PERFORMANCE FLOW</span><h1>AERO<br><em>PULSE</em></h1><p>Información que se mueve al ritmo del taller.</p></div>
        <div class="ap-live"><i></i><span>SISTEMA EN VIVO</span><strong>84<small>%</small></strong></div>
    </header>

    <div class="ap-kpis">
        <article><i class="bi bi-calendar2-check"></i><span>TURNOS</span><strong>07</strong><small>+18% hoy</small></article>
        <article><i class="bi bi-tools"></i><span>EN TALLER</span><strong>12</strong><small>4 próximos a salir</small></article>
        <article><i class="bi bi-lightning-charge"></i><span>RITMO</span><strong>2.8<em>h</em></strong><small>tiempo promedio</small></article>
        <article><i class="bi bi-exclamation-circle"></i><span>ALERTAS</span><strong>03</strong><small>prioridad media</small></article>
    </div>

    <div class="ap-grid">
        <article class="ap-panel ap-energy">
            <div class="ap-title"><div><span>ENERGÍA SEMANAL</span><h2>Impulso operativo</h2></div><b>+18%</b></div>
            <svg viewBox="0 0 720 260" role="img" aria-label="Impulso operativo ilustrativo"><defs><linearGradient id="apFill" x1="0" y1="0" x2="1" y2="0"><stop offset="0" stop-color="#1368ff"/><stop offset="1" stop-color="#ff5c35"/></linearGradient></defs><path class="ghost" d="M15 205 C95 220 105 55 190 91 S295 238 370 150 S475 42 548 110 S650 142 705 35"/><path class="pulse" d="M15 205 C95 220 105 55 190 91 S295 238 370 150 S475 42 548 110 S650 142 705 35"/><circle cx="190" cy="91" r="8"/><circle cx="370" cy="150" r="8"/><circle cx="548" cy="110" r="8"/><circle cx="705" cy="35" r="8"/></svg>
            <div class="ap-days"><span>LUN</span><span>MAR</span><span>MIÉ</span><span>JUE</span><span>VIE</span><span>SÁB</span><span>DOM</span></div>
        </article>
        <article class="ap-panel ap-orbit"><div class="ap-title"><div><span>DISTRIBUCIÓN</span><h2>Estado actual</h2></div></div><div class="ap-wheel"><div><strong>12</strong><small>vehículos</small></div></div><div class="ap-legend"><span><i></i>En proceso 7</span><span><i></i>Control 3</span><span><i></i>Listos 2</span></div></article>
        <article class="ap-panel ap-stream"><div class="ap-title"><div><span>STREAM / PRÓXIMOS</span><h2>Flujo de entradas</h2></div><a href="{{ route('turnos.index') }}">Ver agenda</a></div><div><time>08:30</time><span><b>AA 204 BB</b><small>Service</small></span><em>ON TIME</em></div><div><time>10:00</time><span><b>AB 718 KT</b><small>Frenos</small></span><em>READY</em></div><div><time>11:30</time><span><b>AC 113 PV</b><small>Motor</small></span><em>ON TIME</em></div></article>
        <article class="ap-panel ap-action"><i class="bi bi-arrow-up-right"></i><span>FOCO DEL DÍA</span><h2>4 entregas listas</h2><p>Control de calidad superado.</p><a href="{{ route('reparaciones.index') }}">Abrir reparaciones</a></article>
    </div>
</section>
