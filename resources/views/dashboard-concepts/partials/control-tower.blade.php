<section class="ct-dashboard" aria-label="Maqueta Torre de control">
    <header class="ct-header">
        <div><span>DOMINGO · 01 NOV 2026</span><h1>El taller, bajo control.</h1><p>Buen día, {{ str($user->name)->before(' ') }}. Estas son las señales que requieren tu atención.</p></div>
        <div class="ct-live"><i></i> Operación en vivo</div>
    </header>

    <div class="ct-metric-row">
        @foreach ([
            ['Turnos', $preview['appointmentsToday'], '+2 vs. ayer', 'bi-calendar2-week'],
            ['En taller', $preview['activeRepairs'], '3 por finalizar', 'bi-tools'],
            ['Presupuestos', $preview['pendingBudgets'], 'Esperan decisión', 'bi-file-earmark-check'],
            ['Stock crítico', $preview['lowStock'], 'Reponer hoy', 'bi-box-seam'],
        ] as [$label, $value, $hint, $icon])
            <article><i class="bi {{ $icon }}" aria-hidden="true"></i><span>{{ $label }}</span><strong>{{ str_pad((string) $value, 2, '0', STR_PAD_LEFT) }}</strong><small>{{ $hint }}</small></article>
        @endforeach
    </div>

    <div class="ct-layout">
        <article class="ct-panel ct-flow-panel">
            <div class="ct-panel-title"><div><span>Flujo semanal</span><h2>Carga operativa</h2></div><b>+18%</b></div>
            <svg class="ct-line-chart" viewBox="0 0 700 230" role="img" aria-label="Curva ascendente de actividad semanal">
                <defs><linearGradient id="ctArea" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#ffcf56" stop-opacity=".34"/><stop offset="1" stop-color="#ffcf56" stop-opacity="0"/></linearGradient></defs>
                <path class="area" d="M0 190 C75 175 98 102 170 124 S270 186 330 122 S438 62 492 96 S593 145 700 32 L700 230 L0 230 Z"/>
                <path class="line" d="M0 190 C75 175 98 102 170 124 S270 186 330 122 S438 62 492 96 S593 145 700 32"/>
                @foreach ([190, 124, 122, 96, 32] as $index => $y)<circle cx="{{ [0, 170, 330, 492, 700][$index] }}" cy="{{ $y }}" r="5"/>@endforeach
            </svg>
            <div class="ct-chart-labels"><span>Lun</span><span>Mar</span><span>Mié</span><span>Jue</span><span>Vie</span><span>Sáb</span><span>Dom</span></div>
        </article>

        <article class="ct-panel ct-ring-panel">
            <span>Objetivo del día</span>
            <div class="ct-ring" style="--progress: {{ $preview['completionRate'] }}"><div><strong>{{ $preview['completionRate'] }}%</strong><small>completado</small></div></div>
            <p><b>16 de 19</b> órdenes dentro del tiempo previsto.</p>
        </article>

        <article class="ct-panel ct-agenda-panel">
            <div class="ct-panel-title"><div><span>Siguiente bloque</span><h2>Agenda inmediata</h2></div><a href="{{ route('turnos.index') }}">Abrir agenda</a></div>
            <div class="ct-agenda">
                @foreach ([['08:30', 'AB123CD', 'Service 60.000 km', 'Box 02'], ['09:15', 'AE321JK', 'Diagnóstico eléctrico', 'Box 04'], ['10:00', 'AD789GH', 'Frenos delanteros', 'Box 01']] as [$time, $plate, $task, $box])
                    <div><time>{{ $time }}</time><b>{{ $plate }}</b><span>{{ $task }}</span><em>{{ $box }}</em></div>
                @endforeach
            </div>
        </article>

        <article class="ct-panel ct-alert-panel">
            <span>Alertas inteligentes</span><h2>3 acciones sugeridas</h2>
            <ul><li><i class="bi bi-box-seam"></i><span><b>Batería 65 Ah</b> alcanzó stock mínimo</span></li><li><i class="bi bi-person-check"></i><span><b>2 presupuestos</b> sin respuesta hace 48 h</span></li><li><i class="bi bi-clock-history"></i><span><b>OT-00019</b> supera el tiempo estimado</span></li></ul>
        </article>
    </div>
</section>
