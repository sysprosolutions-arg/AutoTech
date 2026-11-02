<section class="sz-dashboard" aria-label="Maqueta Suzuka Flow">
    <header class="sz-header">
        <div class="sz-orbit"><span>21</span><i></i></div>
        <div><span>AUTOTECH TECHNICAL SESSION</span><h1>SUZUKA<br><em>FLOW</em></h1><p>Precisión, ritmo y control en cada cruce del proceso.</p></div>
        <div class="sz-shift"><span>TURNO ACTIVO</span><strong>08:24</strong><small><i></i> 6 ESTACIONES ONLINE</small></div>
    </header>

    <div class="sz-kpis">
        <article><span>01 / TURNOS</span><strong>07</strong><small>6 confirmados</small></article>
        <article><span>02 / EN CURSO</span><strong>12</strong><small>4 prioritarios</small></article>
        <article><span>03 / REPUESTOS</span><strong>91<sup>%</sup></strong><small>disponibilidad</small></article>
        <article><span>04 / CALIDAD</span><strong>98<sup>%</sup></strong><small>aprobación</small></article>
    </div>

    <div class="sz-grid">
        <article class="sz-card sz-route">
            <div class="sz-title"><div><span>FIGURE-EIGHT WORKFLOW</span><h2>Flujo técnico · Suzuka</h2></div><b>LIVE</b></div>
            <svg viewBox="0 0 760 430" role="img" aria-label="Flujo de reparación sobre una representación del circuito de Suzuka">
                <path class="sz-track-shadow" d="M110 250C125 173 189 117 270 118C342 119 375 171 416 210C449 241 490 268 550 252C610 236 657 186 646 137C636 94 585 74 544 94C501 115 484 166 456 204C417 257 378 316 309 340C237 364 160 343 120 296C108 282 105 266 110 250Z"/>
                <path class="sz-track" d="M110 250C125 173 189 117 270 118C342 119 375 171 416 210C449 241 490 268 550 252C610 236 657 186 646 137C636 94 585 74 544 94C501 115 484 166 456 204C417 257 378 316 309 340C237 364 160 343 120 296C108 282 105 266 110 250Z"/>
                <path class="sz-progress" d="M110 250C125 173 189 117 270 118C342 119 375 171 416 210C449 241 490 268 550 252"/>
                <path class="sz-bridge" d="M392 188L438 233"/>
                <g><circle cx="110" cy="250" r="13"/><text x="43" y="234">01 INGRESO</text></g>
                <g><circle cx="270" cy="118" r="13"/><text x="216" y="86">02 SCAN</text></g>
                <g><circle cx="550" cy="252" r="13"/><text x="573" y="274">03 PARTES</text></g>
                <g><circle cx="544" cy="94" r="13"/><text x="499" y="58">04 TALLER</text></g>
                <g><circle cx="309" cy="340" r="13"/><text x="257" y="385">05 CALIDAD</text></g>
                <text class="sz-map-label" x="333" y="274">SUZUKA</text>
                <text class="sz-map-sub" x="342" y="294">5.807 KM</text>
            </svg>
            <div class="sz-unit"><span>UNIDAD ACTUAL</span><strong>AE 321 JK</strong><small>Electricidad · etapa 04</small><em>81%</em></div>
        </article>

        <article class="sz-card sz-boxes">
            <div class="sz-title"><div><span>GARAGE MATRIX</span><h2>Estaciones</h2></div><b>6 TOTAL</b></div>
            <div class="sz-box-grid">
                @foreach ([['01','Frenos','AD 789 GH','active'],['02','Service','AB 123 CD','active'],['03','Libre','Disponible','free'],['04','Eléctrico','AE 321 JK','active'],['05','Diagnóstico','AC 456 EF','warning'],['06','Limpieza','08:40','idle']] as [$number,$task,$plate,$state])
                    <div class="{{ $state }}"><span>{{ $number }}</span><i></i><strong>{{ $task }}</strong><small>{{ $plate }}</small></div>
                @endforeach
            </div>
        </article>

        <article class="sz-card sz-queue">
            <div class="sz-title"><div><span>LIVE QUEUE</span><h2>Cola de reparación</h2></div><a href="{{ route('reparaciones.index') }}">TODAS ↗</a></div>
            @foreach ([['P1','AD 789 GH','Control final','11:20'],['P2','AE 321 JK','Electricidad','13:45'],['P3','AB 123 CD','Service','16:10']] as [$priority,$plate,$task,$time])
                <div class="sz-queue-row"><b>{{ $priority }}</b><span><strong>{{ $plate }}</strong><small>{{ $task }}</small></span><time>{{ $time }}</time></div>
            @endforeach
        </article>

        <article class="sz-card sz-quality"><span>QUALITY GATE</span><div class="sz-ring"><strong>98<sup>%</sup></strong></div><h2>Once unidades homologadas sin retrabajo.</h2><p>Control de seguridad y documentación completos.</p></article>

        <article class="sz-card sz-radio"><i class="bi bi-soundwave"></i><div><span>TEAM RADIO</span><strong>Box 05 solicita autorización para cambiar sensor ABS.</strong></div><a href="{{ route('repuestos.index') }}">Responder <i class="bi bi-arrow-right"></i></a></article>
    </div>
</section>
