<section class="rg-dashboard" aria-label="Maqueta Riviera Grid">
    <div class="rg-sun" aria-hidden="true"></div>
    <header class="rg-header">
        <div><span>COASTAL PERFORMANCE / AUTOTECH</span><h1>Riviera<br><em>Grid</em></h1><p>Una parrilla luminosa para conducir la operación del taller.</p></div>
        <div class="rg-weather"><i class="bi bi-sun"></i><strong>24°</strong><span>PISTA SECA</span><small>Condición ideal</small></div>
    </header>

    <div class="rg-status"><span><i></i> TRACK OPEN</span><b>08:24</b><p>Próxima entrada AA 204 BB · Service programado</p><a href="{{ route('turnos.index') }}">Agenda ↗</a></div>

    <div class="rg-grid">
        <article class="rg-card rg-board">
            <div class="rg-title"><div><span>STARTING GRID</span><h2>Parrilla del taller</h2></div><b>12 EN CURSO</b></div>
            @foreach ([['01','AA 204 BB','Service','92','OUT'],['02','AB 718 KT','Frenos','74','PIT'],['03','AC 113 PV','Motor','61','RUN'],['04','AD 490 PL','Eléctrico','48','RUN']] as [$position,$plate,$job,$progress,$status])
                <div class="rg-entry"><strong>{{ $position }}</strong><span><b>{{ $plate }}</b><small>{{ $job }}</small></span><i><em style="width:{{ $progress }}%"></em></i><mark>{{ $progress }}%</mark><u class="{{ strtolower($status) }}">{{ $status }}</u></div>
            @endforeach
        </article>
        <article class="rg-card rg-lap">
            <div class="rg-title"><div><span>BEST LAP</span><h2>Ritmo semanal</h2></div><b>+18%</b></div>
            <svg viewBox="0 0 520 210" role="img" aria-label="Ritmo semanal ilustrativo"><path class="grid" d="M15 45H505M15 100H505M15 155H505"/><path class="line" d="M15 174 C68 166 90 70 143 91 S224 177 278 123 S347 42 401 80 S457 110 505 34"/><circle cx="143" cy="91" r="6"/><circle cx="278" cy="123" r="6"/><circle cx="505" cy="34" r="6"/></svg>
            <div class="rg-days"><span>LU</span><span>MA</span><span>MI</span><span>JU</span><span>VI</span><span>SÁ</span><span>DO</span></div>
        </article>
        <article class="rg-card rg-stats"><div><span>TURNOS</span><strong>07</strong></div><div><span>EFICIENCIA</span><strong>84%</strong></div><div><span>ALERTAS</span><strong>03</strong></div></article>
        <article class="rg-card rg-radio"><i class="bi bi-soundwave"></i><div><span>TEAM RADIO</span><h2>“Cuatro vehículos superaron el control final.”</h2></div><a href="{{ route('reparaciones.index') }}"><i class="bi bi-arrow-right"></i></a></article>
    </div>
</section>
