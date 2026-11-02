<section class="os-dashboard" aria-label="Maqueta Cielo abierto">
    <div class="os-cloud os-cloud-one" aria-hidden="true"></div><div class="os-cloud os-cloud-two" aria-hidden="true"></div>
    <header class="os-header">
        <div><span>PANEL DE OPERACIONES</span><h1>Buen día,<br>Administrador.</h1><p>El taller avanza despejado y sin demoras críticas.</p></div>
        <div class="os-weather"><i class="bi bi-sun"></i><span>24°</span><small>Operación despejada</small></div>
    </header>

    <div class="os-metrics">
        <article><span>Turnos</span><strong>07</strong><small><i class="bi bi-arrow-up"></i> 2 más que ayer</small></article>
        <article><span>En taller</span><strong>12</strong><small>4 finalizan hoy</small></article>
        <article><span>Eficiencia</span><strong>84%</strong><small><i class="bi bi-arrow-up"></i> 6% semanal</small></article>
        <article><span>Alertas</span><strong>03</strong><small>sin urgencias</small></article>
    </div>

    <div class="os-grid">
        <article class="os-card os-wave">
            <div class="os-title"><div><span>ÚLTIMOS 7 DÍAS</span><h2>Flujo de trabajo</h2></div><b>Semana 44 <i class="bi bi-chevron-down"></i></b></div>
            <svg viewBox="0 0 700 230" role="img" aria-label="Flujo semanal ilustrativo"><defs><linearGradient id="osArea" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#1687dd" stop-opacity=".28"/><stop offset="1" stop-color="#1687dd" stop-opacity="0"/></linearGradient></defs><path class="area" d="M15 185 C90 175 110 70 190 95 S310 200 390 130 S500 55 565 105 S640 115 685 40 L685 220 L15 220Z"/><path class="line" d="M15 185 C90 175 110 70 190 95 S310 200 390 130 S500 55 565 105 S640 115 685 40"/></svg>
            <div class="os-days"><span>LU</span><span>MA</span><span>MI</span><span>JU</span><span>VI</span><span>SÁ</span><span>DO</span></div>
        </article>
        <article class="os-card os-agenda"><div class="os-title"><div><span>PRÓXIMOS</span><h2>Agenda</h2></div><a href="{{ route('turnos.index') }}">Ver todo</a></div><div><time>08:30</time><span><b>AA 204 BB</b><small>Service</small></span><i class="bi bi-chevron-right"></i></div><div><time>10:00</time><span><b>AB 718 KT</b><small>Frenos</small></span><i class="bi bi-chevron-right"></i></div><div><time>11:30</time><span><b>AC 113 PV</b><small>Motor</small></span><i class="bi bi-chevron-right"></i></div></article>
        <article class="os-card os-message"><i class="bi bi-stars"></i><div><span>EN FOCO</span><h2>4 vehículos listos para entregar</h2><p>Todos completaron el control de calidad.</p></div><a href="{{ route('reparaciones.index') }}"><i class="bi bi-arrow-up-right"></i></a></article>
    </div>
</section>
