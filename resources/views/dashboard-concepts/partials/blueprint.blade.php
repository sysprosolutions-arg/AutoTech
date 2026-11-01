<section class="bp-dashboard" aria-label="Maqueta Plano de taller">
    <header class="bp-header"><div><span>AUTOTECH / ESTACIÓN CENTRAL</span><h1>PLANO OPERATIVO <sup>02</sup></h1></div><div><b>01.11.26</b><small>Turno mañana · 08:24</small></div></header>
    <div class="bp-summary">
        <article><span>ENTRADAS</span><strong>{{ $preview['appointmentsToday'] }}</strong><small>vehículos agendados</small></article>
        <article><span>EN PROCESO</span><strong>{{ $preview['activeRepairs'] }}</strong><small>órdenes abiertas</small></article>
        <article><span>OCUPACIÓN</span><strong>{{ $preview['occupationRate'] }}%</strong><small>capacidad del taller</small></article>
        <article><span>ALERTAS</span><strong>{{ $preview['lowStock'] }}</strong><small>requieren revisión</small></article>
    </div>
    <div class="bp-grid">
        <article class="bp-workshop-map">
            <div class="bp-section-title"><span>A/01</span><h2>Estado de estaciones</h2><b>6 boxes</b></div>
            <div class="bp-bays">
                @foreach ([['01','AD789GH','FRENOS','busy'],['02','AB123CD','SERVICE','busy'],['03','—','LIBRE','free'],['04','AE321JK','ELÉCTRICO','busy'],['05','AC456EF','DIAGNÓSTICO','busy'],['06','—','LIMPIEZA','idle']] as [$number,$plate,$task,$state])
                    <div class="{{ $state }}"><small>BOX {{ $number }}</small><i class="bi bi-car-front-fill" aria-hidden="true"></i><b>{{ $plate }}</b><span>{{ $task }}</span></div>
                @endforeach
            </div>
        </article>
        <article class="bp-process">
            <div class="bp-section-title"><span>B/07</span><h2>Flujo de órdenes</h2></div>
            @foreach ([['Recepción',7,100],['Diagnóstico',5,74],['Reparación',12,88],['Control',4,48],['Entrega',9,68]] as [$label,$value,$width])
                <div class="bp-process-row"><span>{{ $label }}</span><div><i style="width: {{ $width }}%"></i></div><b>{{ str_pad((string)$value, 2, '0', STR_PAD_LEFT) }}</b></div>
            @endforeach
        </article>
        <article class="bp-stock">
            <div class="bp-section-title"><span>C/12</span><h2>Inventario crítico</h2><b>actualizado</b></div>
            <table><thead><tr><th>Código</th><th>Pieza</th><th>Stock</th></tr></thead><tbody><tr><td>REP-BAT-001</td><td>Batería 65 Ah</td><td><b>05</b></td></tr><tr><td>REP-PAS-001</td><td>Pastillas Bosch</td><td><b>08</b></td></tr><tr><td>REP-FIL-001</td><td>Filtro Toyota</td><td><b>12</b></td></tr></tbody></table>
        </article>
    </div>
</section>
