<section class="pl-dashboard" aria-label="Maqueta Pit Lane">
    <header class="pl-header">
        <div class="pl-flag" aria-hidden="true"></div>
        <div><span>CONTROL DE OPERACIONES</span><h1>PIT LANE</h1><p>Toda la actividad del taller, medida vuelta a vuelta.</p></div>
        <div class="pl-clock"><span>HORA LOCAL</span><strong>08:24</strong><small>DOM 01 NOV</small></div>
    </header>

    <div class="pl-scoreboard">
        <article><span>TURNOS</span><strong>07</strong><small>+2 vs. ayer</small></article>
        <article><span>EN PISTA</span><strong>12</strong><small>4 por finalizar</small></article>
        <article><span>EFICIENCIA</span><strong>84<sup>%</sup></strong><small>objetivo 80%</small></article>
        <article><span>ALERTAS</span><strong>03</strong><small>stock crítico</small></article>
    </div>

    <div class="pl-grid">
        <article class="pl-card pl-bays">
            <div class="pl-title"><div><span>BOXES / ESTADO EN VIVO</span><h2>Actividad por puesto</h2></div><b>6 puestos</b></div>
            <div class="pl-lanes">
                @foreach ([['01','AA 204 BB','Service','82'],['02','AB 718 KT','Frenos','64'],['03','AC 113 PV','Motor','38'],['04','AD 490 PL','Eléctrico','91']] as [$box,$plate,$job,$progress])
                    <div><strong>{{ $box }}</strong><span><b>{{ $plate }}</b><small>{{ $job }}</small></span><i><em style="width: {{ $progress }}%"></em></i><mark>{{ $progress }}%</mark></div>
                @endforeach
            </div>
        </article>
        <article class="pl-card pl-telemetry">
            <div class="pl-title"><div><span>TELEMETRÍA</span><h2>Tiempo por reparación</h2></div></div>
            <svg viewBox="0 0 500 180" role="img" aria-label="Telemetría semanal ilustrativa"><path class="pl-gridline" d="M10 40H490M10 90H490M10 140H490"/><path class="pl-line" d="M10 130 C75 115,80 45,145 72 S230 145,290 92 S365 30,490 55"/><circle cx="145" cy="72" r="5"/><circle cx="290" cy="92" r="5"/><circle cx="490" cy="55" r="5"/></svg>
            <div class="pl-legend"><span><i></i>Objetivo 3,2 h</span><strong>Promedio 2,8 h</strong></div>
        </article>
        <article class="pl-card pl-next"><span>PRÓXIMA BANDERA</span><time>09:10</time><h2>Entrega · AA 204 BB</h2><p>Control final completado. Documentación pendiente.</p><button type="button">Ver detalle <i class="bi bi-arrow-up-right"></i></button></article>
    </div>
</section>
