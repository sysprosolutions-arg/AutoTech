<section class="bh-dashboard" aria-label="Maqueta Geometría pop">
    <header class="bh-header">
        <div class="bh-symbol" aria-hidden="true"><i></i><b></b><span></span></div>
        <div><span>PANEL GENERAL · 01/11</span><h1>TODO EN<br>MOVIMIENTO.</h1></div>
        <p>Color para reconocer.<br>Forma para comprender.<br>Datos para decidir.</p>
    </header>

    <div class="bh-board">
        <article class="bh-block cobalt"><span>TURNOS</span><strong>07</strong><small>programados hoy</small><i class="bi bi-arrow-up-right"></i></article>
        <article class="bh-block yellow"><span>REPARACIONES</span><strong>12</strong><small>en movimiento</small><i class="bi bi-tools"></i></article>
        <article class="bh-block coral"><span>PENDIENTES</span><strong>04</strong><small>presupuestos</small><i class="bi bi-hourglass-split"></i></article>
        <article class="bh-chart">
            <div><span>RITMO SEMANAL</span><h2>+18%</h2></div>
            <div class="bh-columns">@foreach ([38,70,52,88,64,95,58] as $value)<i style="height: {{ $value }}%"></i>@endforeach</div>
            <div class="bh-labels"><span>L</span><span>M</span><span>X</span><span>J</span><span>V</span><span>S</span><span>D</span></div>
        </article>
        <article class="bh-focus"><span>01 / PRIORIDAD</span><strong>Entregar cuatro vehículos antes de las 17:00.</strong><a href="{{ route('reparaciones.index') }}">ABRIR REPARACIONES <i class="bi bi-arrow-right"></i></a></article>
        <article class="bh-list"><span>02 / SIGUIENTES</span><div><b>08:30</b><p>AA 204 BB · Service</p><i></i></div><div><b>10:00</b><p>AB 718 KT · Frenos</p><i></i></div><div><b>11:30</b><p>AC 113 PV · Motor</p><i></i></div></article>
    </div>
</section>
