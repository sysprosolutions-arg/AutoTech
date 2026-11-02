<section class="sc-dashboard" aria-label="Maqueta Silverstone Command">
    <header class="sc-header">
        <div><span>AT / RACE OPERATIONS</span><h1>SILVERSTONE<br><em>COMMAND</em></h1><p>Control total de estaciones, equipos y entregas.</p></div>
        <div class="sc-live"><i></i><span>RACE CONTROL</span><strong>LIVE</strong><small>01 NOV · TURNO MAÑANA</small></div>
    </header>

    <div class="sc-control-strip">
        <span><i></i> TALLER OPERATIVO</span><p><b>MENSAJE:</b> Box 05 espera confirmación de repuesto.</p><time>08:24:16</time>
    </div>

    <div class="sc-grid">
        <article class="sc-panel sc-circuit">
            <div class="sc-title"><div><span>WORKFLOW MAP / 20</span><h2>Vuelta de reparación · Silverstone</h2></div><b>84% PACE</b></div>
            <svg viewBox="0 0 720 420" role="img" aria-label="Flujo de reparación sobre una representación del circuito de Silverstone">
                <path class="sc-track-shadow" d="M590 330L516 352L454 325L402 339L344 310L288 319L242 286L184 301L130 267L158 219L119 174L165 132L224 147L270 110L326 133L377 94L430 119L484 91L530 126L576 111L603 159L569 205L611 247L576 279L590 330Z"/>
                <path class="sc-track" d="M590 330L516 352L454 325L402 339L344 310L288 319L242 286L184 301L130 267L158 219L119 174L165 132L224 147L270 110L326 133L377 94L430 119L484 91L530 126L576 111L603 159L569 205L611 247L576 279L590 330Z"/>
                <path class="sc-sector-one" d="M590 330L516 352L454 325L402 339L344 310L288 319L242 286"/>
                <path class="sc-sector-two" d="M242 286L184 301L130 267L158 219L119 174L165 132L224 147L270 110L326 133"/>
                <g><circle cx="590" cy="330" r="12"/><text x="608" y="348">ENTRADA</text></g>
                <g><circle cx="242" cy="286" r="12"/><text x="151" y="328">DIAGNÓSTICO</text></g>
                <g><circle cx="165" cy="132" r="12"/><text x="81" y="110">REPUESTOS</text></g>
                <g><circle cx="377" cy="94" r="12"/><text x="341" y="66">TALLER</text></g>
                <g><circle cx="611" cy="247" r="12"/><text x="628" y="252">CONTROL</text></g>
                <text class="sc-center-label" x="315" y="213">SILVERSTONE</text>
                <text class="sc-center-sub" x="340" y="234">18 CURVAS</text>
            </svg>
            <div class="sc-sector-legend"><span><i></i>Recepción</span><span><i></i>Diagnóstico</span><span><i></i>Reparación</span><span><i></i>Calidad</span></div>
        </article>

        <article class="sc-panel sc-status">
            <div class="sc-title"><div><span>STATION STATUS</span><h2>Boxes del taller</h2></div><b>04/06 ACTIVOS</b></div>
            @foreach ([['01','AD 789 GH','Frenos','68','green'],['02','AB 123 CD','Service','42','green'],['03','DISPONIBLE','Sin asignar','100','free'],['04','AE 321 JK','Electricidad','81','green'],['05','AC 456 EF','Diagnóstico','35','yellow'],['06','LIMPIEZA','Disponible 08:40','18','neutral']] as [$number,$plate,$task,$progress,$state])
                <div class="sc-station"><strong>{{ $number }}</strong><span><b>{{ $plate }}</b><small>{{ $task }}</small></span><i><em class="{{ $state }}" style="width:{{ $progress }}%"></em></i><mark class="{{ $state }}">{{ $progress }}%</mark></div>
            @endforeach
        </article>

        <article class="sc-panel sc-deliveries">
            <div class="sc-title"><div><span>PREDICTIVE WINDOW</span><h2>Próximas banderas</h2></div><a href="{{ route('reparaciones.index') }}">DETALLE ↗</a></div>
            <div class="sc-delivery"><time>11:20</time><span><b>AD 789 GH</b><small>Frenos · Control final</small></span><em>ON TIME</em></div>
            <div class="sc-delivery"><time>13:45</time><span><b>AE 321 JK</b><small>Electricidad · En taller</small></span><em>+10 MIN</em></div>
            <div class="sc-delivery"><time>16:10</time><span><b>AB 123 CD</b><small>Service · En taller</small></span><em>ON TIME</em></div>
        </article>

        <article class="sc-panel sc-pulse"><span>WORKSHOP PACE</span><strong>84<sup>%</sup></strong><div><i style="height:45%"></i><i style="height:68%"></i><i style="height:52%"></i><i style="height:88%"></i><i style="height:73%"></i><i style="height:94%"></i><i style="height:84%"></i></div><small>Productividad de las últimas 7 horas</small></article>
    </div>
</section>
