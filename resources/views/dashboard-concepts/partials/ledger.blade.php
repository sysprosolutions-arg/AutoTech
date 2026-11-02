<section class="lg-dashboard" aria-label="Maqueta Libro de taller">
    <header class="lg-masthead"><div><span>AUTOTECH S.A.</span><small>BOLETÍN OPERATIVO · EDICIÓN 01/11/2026</small></div><h1>EL TALLER</h1><p>Decisiones claras. Datos a la vista.</p></header>
    <div class="lg-headline-grid">
        <article class="lg-main-story"><span>LA CIFRA DEL DÍA</span><strong>{{ $preview['activeRepairs'] }}</strong><h2>vehículos están siendo reparados ahora</h2><p>Cuatro órdenes finalizarán antes del mediodía. La ocupación se mantiene dentro de la capacidad prevista.</p></article>
        <article class="lg-kpi-list">@foreach ([['Turnos confirmados',$preview['appointmentsToday']],['Presupuestos a resolver',$preview['pendingBudgets']],['Alertas de inventario',$preview['lowStock']],['Eficiencia semanal',$preview['completionRate'].'%']] as [$label,$value])<div><span>{{ $label }}</span><b>{{ $value }}</b></div>@endforeach</article>
    </div>
    <div class="lg-rule"><span>AGENDA · INVENTARIO · PRODUCTIVIDAD · CLIENTES · PROVEEDORES ·</span></div>
    <div class="lg-columns">
        <article class="lg-schedule"><h2>Próximas entradas</h2><table><thead><tr><th>Hora</th><th>Vehículo</th><th>Trabajo</th><th>Box</th></tr></thead><tbody><tr><td>08:30</td><td><b>AB123CD</b></td><td>Service 60.000 km</td><td>02</td></tr><tr><td>09:15</td><td><b>AE321JK</b></td><td>Diagnóstico eléctrico</td><td>04</td></tr><tr><td>10:00</td><td><b>AD789GH</b></td><td>Frenos delanteros</td><td>01</td></tr><tr><td>11:30</td><td><b>AC456EF</b></td><td>Control general</td><td>03</td></tr></tbody></table></article>
        <article class="lg-bars"><h2>Ocupación por especialidad</h2>@foreach ([['Mecánica general',86],['Electricidad',64],['Frenos',72],['Diagnóstico',48]] as [$label,$value])<div><span>{{ $label }}</span><b>{{ $value }}%</b><i><em style="width: {{ $value }}%"></em></i></div>@endforeach</article>
        <article class="lg-note"><span>NOTA DE OPERACIÓN</span><h2>Reponer antes del martes.</h2><p>Baterías 65 Ah y pastillas Bosch se encuentran por debajo del nivel recomendado.</p><a href="{{ route('repuestos.index') }}">Revisar inventario →</a></article>
    </div>
</section>
