<section class="pp-dashboard" aria-label="Maqueta Pole Position">
    <header class="pp-header">
        <div class="pp-mark"><span>AT</span><i></i></div>
        <div><span>RACE CONTROL / TALLER</span><h1>POLE<br>POSITION</h1><p>Las prioridades del día, alineadas para una salida perfecta.</p></div>
        <div class="pp-session"><span>SESIÓN ACTIVA</span><strong>08:24:16</strong><small><i></i> PISTA OPERATIVA</small></div>
    </header>

    <div class="pp-grid-line" aria-label="Indicadores principales">
        <article><span>P01</span><div><small>TURNOS</small><strong>07</strong></div><em>+2</em></article>
        <article><span>P02</span><div><small>EN BOXES</small><strong>12</strong></div><em>4 OUT</em></article>
        <article><span>P03</span><div><small>EFICIENCIA</small><strong>84%</strong></div><em>BEST</em></article>
        <article><span>P04</span><div><small>ALERTAS</small><strong>03</strong></div><em>CHECK</em></article>
    </div>

    <div class="pp-content">
        <article class="pp-card pp-sector-board">
            <div class="pp-title"><div><span>LIVE / SECTORES</span><h2>Rendimiento del taller</h2></div><b>WEEK 44</b></div>
            <div class="pp-sectors"><div><span>S1</span><strong>92.4</strong><small>Recepción</small><i class="purple"></i></div><div><span>S2</span><strong>84.1</strong><small>Diagnóstico</small><i class="green"></i></div><div><span>S3</span><strong>78.8</strong><small>Reparación</small><i class="yellow"></i></div></div>
            <div class="pp-laps">
                @foreach ([['01','AA 204 BB','Service','01:42:18','OUT'],['02','AB 718 KT','Frenos','02:18:44','PIT'],['03','AC 113 PV','Motor','03:07:12','RUN'],['04','AD 490 PL','Eléctrico','01:33:09','OUT']] as [$position,$plate,$job,$time,$status])
                    <div><b>{{ $position }}</b><span><strong>{{ $plate }}</strong><small>{{ $job }}</small></span><time>{{ $time }}</time><em class="{{ strtolower($status) }}">{{ $status }}</em></div>
                @endforeach
            </div>
        </article>
        <article class="pp-card pp-strategy">
            <div class="pp-title"><div><span>ESTRATEGIA</span><h2>Carga por hora</h2></div></div>
            <div class="pp-curve"><svg viewBox="0 0 500 210" role="img" aria-label="Curva de carga ilustrativa"><path class="grid" d="M15 40H485M15 90H485M15 140H485M15 190H485"/><path class="line" d="M15 175 C80 170 82 72 145 88 S230 160 290 118 S350 35 405 68 S455 115 485 42"/><circle cx="145" cy="88" r="6"/><circle cx="290" cy="118" r="6"/><circle cx="485" cy="42" r="6"/></svg></div>
            <div class="pp-compounds"><span><i class="soft"></i>Rápido 3</span><span><i class="medium"></i>Medio 6</span><span><i class="hard"></i>Extenso 3</span></div>
        </article>
        <article class="pp-card pp-message"><span>TEAM RADIO</span><i class="bi bi-soundwave"></i><div><strong>Cuatro vehículos listos para la bandera a cuadros.</strong><small>Control de calidad completado.</small></div><a href="{{ route('reparaciones.index') }}">OPEN <i class="bi bi-arrow-up-right"></i></a></article>
    </div>
</section>
