<section class="mpw-dashboard" aria-label="Maqueta Monza Pit Wall">
    <header class="mpw-header">
        <div class="mpw-wordmark"><span>AT</span><i></i></div>
        <div>
            <span>AUTOTECH / FORMULA WORKSHOP</span>
            <h1>MONZA<br><em>PIT WALL</em></h1>
            <p>La reparación avanza por el circuito más veloz del taller.</p>
        </div>
        <div class="mpw-session"><span>SESIÓN 44</span><strong>08:24:16</strong><small><i></i> OPERACIÓN EN VIVO</small></div>
    </header>

    <div class="mpw-timing" aria-label="Indicadores operativos">
        <article><span>LAP 01</span><small>TURNOS</small><strong>07</strong><em>+2 CONF.</em></article>
        <article><span>LAP 02</span><small>EN TALLER</small><strong>12</strong><em>84% CAP.</em></article>
        <article><span>LAP 03</span><small>TIEMPO MEDIO</small><strong>2.8<sup>H</sup></strong><em>−12 MIN</em></article>
        <article><span>LAP 04</span><small>LISTOS</small><strong>04</strong><em>ANTES 17H</em></article>
    </div>

    <div class="mpw-grid">
        <article class="mpw-card mpw-track-card">
            <div class="mpw-title"><div><span>LIVE REPAIR TRACE</span><h2>Flujo de reparación · Monza</h2></div><b>5/5 GATES</b></div>
            <div class="mpw-track-wrap">
                <svg viewBox="0 0 690 430" role="img" aria-label="Flujo de reparación sobre una representación del circuito de Monza">
                    <path class="mpw-track-shadow" d="M472 386C408 394 300 394 231 380C164 367 117 330 106 276C95 221 110 174 140 128C164 91 213 63 262 52C306 42 354 48 379 70C400 89 394 111 370 119C341 128 300 111 278 125C259 137 262 161 285 169C318 181 367 154 398 163C426 171 433 195 412 211C385 232 330 219 306 237C286 252 291 277 318 287C351 299 392 278 423 286C453 294 471 317 472 386Z"/>
                    <path class="mpw-track" d="M472 386C408 394 300 394 231 380C164 367 117 330 106 276C95 221 110 174 140 128C164 91 213 63 262 52C306 42 354 48 379 70C400 89 394 111 370 119C341 128 300 111 278 125C259 137 262 161 285 169C318 181 367 154 398 163C426 171 433 195 412 211C385 232 330 219 306 237C286 252 291 277 318 287C351 299 392 278 423 286C453 294 471 317 472 386Z"/>
                    <path class="mpw-progress" d="M472 386C408 394 300 394 231 380C164 367 117 330 106 276C95 221 110 174 140 128C164 91 213 63 262 52C306 42 354 48 379 70"/>
                    <g class="mpw-stop stop-one"><circle cx="472" cy="386" r="13"/><text x="493" y="392">01 RECEPCIÓN</text></g>
                    <g class="mpw-stop stop-two"><circle cx="119" cy="306" r="13"/><text x="22" y="335">02 DIAGNÓSTICO</text></g>
                    <g class="mpw-stop stop-three"><circle cx="177" cy="89" r="13"/><text x="65" y="74">03 REPUESTOS</text></g>
                    <g class="mpw-stop stop-four"><circle cx="398" cy="163" r="13"/><text x="427" y="158">04 REPARACIÓN</text></g>
                    <g class="mpw-stop stop-five"><circle cx="423" cy="286" r="13"/><text x="460" y="290">05 CONTROL</text></g>
                    <text class="mpw-map-label" x="250" y="221">MONZA</text>
                    <text class="mpw-map-sub" x="252" y="242">5.793 KM · 11 CURVAS</text>
                </svg>
                <div class="mpw-current"><span>UNIDAD EN VUELTA</span><strong>AB 718 KT</strong><small>Reparación de frenos · 68%</small><i><em style="width:68%"></em></i></div>
            </div>
        </article>

        <article class="mpw-card mpw-stations">
            <div class="mpw-title"><div><span>PIT LANE</span><h2>Estado de estaciones</h2></div><b>6 BOXES</b></div>
            <div class="mpw-station-grid">
                @foreach ([['01','AD 789 GH','Frenos','busy','68%'],['02','AB 123 CD','Service','busy','42%'],['03','—','Libre','free','READY'],['04','AE 321 JK','Eléctrico','busy','81%'],['05','AC 456 EF','Diagnóstico','warn','CHECK'],['06','—','Limpieza','idle','12 MIN']] as [$box,$plate,$task,$state,$progress])
                    <div class="{{ $state }}"><span>BOX {{ $box }}</span><i></i><strong>{{ $plate }}</strong><small>{{ $task }}</small><em>{{ $progress }}</em></div>
                @endforeach
            </div>
        </article>

        <article class="mpw-card mpw-crew">
            <div class="mpw-title"><div><span>CREW ALLOCATION</span><h2>Equipo en pista</h2></div><a href="{{ route('reparaciones.index') }}">VER OT ↗</a></div>
            @foreach ([['MC','Marcos C.','Frenos','BOX 01'],['LA','Lucía A.','Electricidad','BOX 04'],['JP','Julián P.','Motor','STANDBY']] as [$initials,$name,$specialty,$box])
                <div class="mpw-crew-row"><b>{{ $initials }}</b><span><strong>{{ $name }}</strong><small>{{ $specialty }}</small></span><em>{{ $box }}</em></div>
            @endforeach
        </article>

        <article class="mpw-card mpw-alert"><span>RACE CONTROL</span><strong><i class="bi bi-exclamation-triangle-fill"></i> STOCK CRÍTICO</strong><p>Pastillas Bosch · quedan 8 unidades.</p><a href="{{ route('repuestos.index') }}">Revisar inventario <i class="bi bi-arrow-right"></i></a></article>
    </div>
</section>
