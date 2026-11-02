@include('dashboard-concepts.partials.top-module-nav')

<section class="ilp-dashboard" aria-label="Maqueta Interlagos Pulse">
    <div class="ilp-landscape" aria-hidden="true"><i></i><i></i></div>
    <header class="ilp-header"><div><span>SÃO PAULO · INTERLAGOS / GARAGE LIVE</span><h1>INTERLAGOS<br><em>PULSE</em></h1><p>Ritmo, energía y trazabilidad en una sola vuelta.</p></div><div class="ilp-live"><i></i><span>SESIÓN EN VIVO</span><strong>84%</strong><small>PACE DEL TALLER</small></div></header>

    <div class="ilp-grid">
        <aside class="ilp-classification" aria-label="Tiempos del taller"><header><span>POS</span><b>UNIDAD</b><em>DELTA</em></header>@foreach ([['1','AD7','AD 789 GH','Frenos','01:42','—','best'],['2','AE3','AE 321 JK','Eléctrico','02:08','+0:26','run'],['3','AB1','AB 123 CD','Service','02:31','+0:49','run'],['4','AC4','AC 456 EF','Diagnóstico','02:56','PIT','pit'],['5','AF8','AF 882 RT','Motor','03:38','+1:56','run'],['6','AG5','AG 510 XO','Alineación','04:01','OUT','out']] as [$position,$code,$plate,$job,$time,$delta,$state])<div class="{{ $state }}"><strong>{{ $position }}</strong><i></i><span><b>{{ $code }} <small>{{ $plate }}</small></b><small>{{ $job }}</small></span><time>{{ $time }}<small>{{ $delta }}</small></time></div>@endforeach<div class="ilp-radio"><i class="bi bi-soundwave"></i><span><b>BOX 05</b><small>Repuesto confirmado</small></span></div></aside>

        <article class="ilp-map-card"><div class="ilp-card-title"><div><span>REPAIR LAP / ANTI-CLOCKWISE</span><h2>Autódromo José Carlos Pace</h2></div><b>4.309 KM · 15 CURVAS</b></div>
            <svg viewBox="0 0 820 500" role="img" aria-label="Mapa operativo fiel al circuito de Interlagos con posiciones y etapas de reparación">
                <path class="ilp-track-base" d="M675 92L676 184C677 251 641 313 584 340L530 365C484 386 430 378 393 345L340 298C309 271 266 270 234 297L193 332C159 361 108 345 94 303C82 266 103 229 137 211L202 177C235 160 253 131 254 94L255 61C256 30 290 13 316 31L374 73C402 94 441 94 469 72L514 37C544 14 588 29 601 65L613 100C624 132 651 150 675 143L675 92Z"/>
                <path class="ilp-sector-one" d="M675 92L676 184C677 251 641 313 584 340L530 365"/><path class="ilp-sector-two" d="M530 365C484 386 430 378 393 345L340 298C309 271 266 270 234 297L193 332C159 361 108 345 94 303C82 266 103 229 137 211"/><path class="ilp-sector-three" d="M137 211L202 177C235 160 253 131 254 94L255 61C256 30 290 13 316 31L374 73C402 94 441 94 469 72L514 37C544 14 588 29 601 65L613 100C624 132 651 150 675 143"/>
                <g class="ilp-turns">@foreach ([[675,92,'01'],[676,184,'02'],[641,313,'03'],[530,365,'04'],[430,378,'05'],[340,298,'06'],[234,297,'07'],[159,350,'08'],[94,303,'09'],[137,211,'10'],[202,177,'11'],[254,94,'12'],[316,31,'13'],[469,72,'14'],[601,65,'15']] as [$x,$y,$number])<circle cx="{{ $x }}" cy="{{ $y }}" r="11"/><text x="{{ $x }}" y="{{ $y + 4 }}">{{ $number }}</text>@endforeach</g>
                <g class="ilp-cars"><circle cx="676" cy="184" r="8"/><text x="691" y="180">AD7</text><circle cx="530" cy="365" r="8"/><text x="544" y="390">AE3</text><circle cx="234" cy="297" r="8"/><text x="196" y="282">AB1</text><circle cx="254" cy="94" r="8"/><text x="205" y="86">AC4</text></g>
                <g class="ilp-labels"><text x="694" y="103">RECEPCIÓN</text><text x="564" y="425">DIAGNÓSTICO</text><text x="128" y="393">REPUESTOS</text><text x="153" y="143">REPARACIÓN</text><text x="497" y="18">CONTROL</text></g><text class="ilp-center" x="347" y="215">INTERLAGOS</text><text class="ilp-center-sub" x="365" y="236">15 CURVAS</text>
            </svg>
            <div class="ilp-legend"><span><i></i>Sector 1</span><span><i></i>Sector 2</span><span><i></i>Sector 3</span><b><i></i>Vehículos</b></div>
        </article>

        <aside class="ilp-insight"><span>ESTRATEGIA DEL DÍA</span><strong>04</strong><h2>entregas antes de las 17:00</h2><div><i style="width:84%"></i></div><small>Ritmo proyectado 84%</small><a href="{{ route('reparaciones.index') }}">Ver reparaciones ↗</a></aside>
    </div>

    <div class="ilp-stations"><div class="ilp-card-title"><div><span>PIT LANE STATUS</span><h2>Boxes y equipos</h2></div><b>6 ESTACIONES</b></div><div>@foreach ([['01','AD7','Frenos','Marcos C.','94','ready'],['02','AB1','Service','Julián P.','62','active'],['03','—','Disponible','Sin asignar','100','free'],['04','AE3','Electricidad','Lucía A.','81','active'],['05','AC4','Diagnóstico','Nicolás R.','35','warning'],['06','—','Limpieza','Equipo B','18','idle']] as [$number,$code,$task,$crew,$progress,$state])<section class="{{ $state }}"><span>BOX {{ $number }}</span><i></i><strong>{{ $code }} · {{ $task }}</strong><small>{{ $crew }}</small><div><em style="width:{{ $progress }}%"></em></div><b>{{ $progress }}%</b></section>@endforeach</div></div>
</section>
