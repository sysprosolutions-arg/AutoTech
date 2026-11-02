@include('dashboard-concepts.partials.top-module-nav')

<section class="mhl-dashboard" aria-label="Maqueta Monaco Harbour Live">
    <div class="mhl-water" aria-hidden="true"><i></i><i></i><i></i></div>
    <header class="mhl-header"><div><span>MONTE-CARLO · HARBOUR OPERATIONS</span><h1>MONACO<br><em>HARBOUR LIVE</em></h1><p>Precisión urbana para una operación sin margen de error.</p></div><div class="mhl-weather"><i class="bi bi-sun-fill"></i><strong>22°</strong><span>PISTA SECA</span><small>Capacidad 72%</small></div></header>

    <div class="mhl-layout">
        <aside class="mhl-tower" aria-label="Clasificación de reparaciones">
            <div class="mhl-tower-title"><span>LIVE TIMING</span><b>LAP / 08:24</b></div>
            @foreach ([['1','AD7','Frenos','01:42','—','best'],['2','AB1','Service','02:31','+0:49','run'],['3','AE3','Eléctrico','02:48','+1:06','run'],['4','AC4','Diagnóstico','03:02','PIT','pit'],['5','AA2','Suspensión','03:21','+1:39','run'],['6','AF8','Motor','03:56','OUT','out']] as [$position,$code,$job,$time,$delta,$state])
                <div class="mhl-time-row {{ $state }}"><strong>{{ $position }}</strong><i></i><span><b>{{ $code }}</b><small>{{ $job }}</small></span><time>{{ $time }}<small>{{ $delta }}</small></time></div>
            @endforeach
            <div class="mhl-summary"><span>VENTANA DE ENTREGA</span><strong>4</strong><small>vehículos antes de las 17:00</small></div>
        </aside>

        <article class="mhl-map">
            <div class="mhl-card-title"><div><span>URBAN REPAIR CIRCUIT</span><h2>Circuit de Monaco</h2></div><b>3.337 KM · 19 CURVAS</b></div>
            <svg viewBox="0 0 860 500" role="img" aria-label="Mapa operativo fiel al Circuito de Mónaco con telemetría de reparaciones">
                <path class="mhl-track-base" d="M157 397L145 341L175 293L225 268L272 282L313 251L356 264L399 230L438 245L479 218L520 229L560 198L606 202L649 172L690 181L725 147L710 111L666 103L624 121L585 96L541 111L502 84L457 99L416 73L375 91L331 69L292 91L252 82L218 111L204 151L169 181L144 223L120 274L126 326L157 397Z"/>
                <path class="mhl-sector-one" d="M157 397L145 341L175 293L225 268L272 282L313 251L356 264"/>
                <path class="mhl-sector-two" d="M356 264L399 230L438 245L479 218L520 229L560 198L606 202L649 172L690 181"/>
                <path class="mhl-sector-three" d="M690 181L725 147L710 111L666 103L624 121L585 96L541 111L502 84L457 99L416 73L375 91L331 69L292 91L252 82L218 111L204 151L169 181L144 223L120 274L126 326L157 397"/>
                <g class="mhl-turns">@foreach ([[157,397,'01'],[145,341,'02'],[175,293,'03'],[225,268,'04'],[313,251,'05'],[399,230,'06'],[479,218,'07'],[560,198,'08'],[649,172,'09'],[725,147,'10'],[666,103,'11'],[585,96,'12'],[502,84,'13'],[416,73,'14'],[331,69,'15'],[252,82,'16'],[204,151,'17'],[144,223,'18'],[120,274,'19']] as [$x,$y,$number])<circle cx="{{ $x }}" cy="{{ $y }}" r="10"/><text x="{{ $x }}" y="{{ $y + 4 }}">{{ $number }}</text>@endforeach</g>
                <g class="mhl-cars"><circle cx="225" cy="268" r="8"/><text x="238" y="260">AD7</text><circle cx="479" cy="218" r="8"/><text x="491" y="211">AB1</text><circle cx="666" cy="103" r="8"/><text x="675" y="89">AE3</text><circle cx="204" cy="151" r="8"/><text x="153" y="145">AC4</text></g>
                <g class="mhl-labels"><text x="85" y="438">RECEPCIÓN</text><text x="236" y="323">DIAGNÓSTICO</text><text x="466" y="276">REPARACIÓN</text><text x="636" y="65">CONTROL</text><text x="124" y="116">ENTREGA</text></g>
                <path class="mhl-drs" d="M126 326L157 397"/><text class="mhl-drs-label" x="184" y="392">DRS / SALIDA</text>
            </svg>
            <div class="mhl-track-legend"><span><i></i>Ingreso</span><span><i></i>Trabajo técnico</span><span><i></i>Control y entrega</span></div>
        </article>

        <aside class="mhl-pitwall"><span>PIT WALL</span><h2>Datos de la unidad</h2><div class="mhl-car-number">AD7<small>P01</small></div><dl><div><dt>Matrícula</dt><dd>AD 789 GH</dd></div><div><dt>Trabajo</dt><dd>Sistema de frenos</dd></div><div><dt>Mecánico</dt><dd>Marcos C.</dd></div><div><dt>Tiempo</dt><dd>01:42 h</dd></div><div><dt>Progreso</dt><dd>94%</dd></div></dl><a href="{{ route('reparaciones.index') }}">Abrir orden de trabajo ↗</a></aside>
    </div>

    <div class="mhl-stations"><div class="mhl-card-title"><div><span>GARAGE / HARBOUR</span><h2>Estaciones del taller</h2></div><b>ACTUALIZADO AHORA</b></div><div>
        @foreach ([['01','Frenos','AD7','94','ready'],['02','Service','AB1','62','active'],['03','Libre','—','100','free'],['04','Eléctrico','AE3','81','active'],['05','Diagnóstico','AC4','35','warning'],['06','Limpieza','—','18','idle']] as [$number,$task,$code,$progress,$state])<section class="{{ $state }}"><span>{{ $number }}</span><b>{{ $task }}</b><small>{{ $code }}</small><i><em style="width:{{ $progress }}%"></em></i><strong>{{ $progress }}%</strong></section>@endforeach
    </div></div>
</section>
