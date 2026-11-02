@php
    $operationalDashboard = $operationalDashboard ?? false;
    $dashboardMetrics = $metrics ?? [];
    $displayAppointments = $operationalDashboard ? (int) ($dashboardMetrics['appointmentsToday'] ?? 0) : 7;
    $displayActiveRepairs = $operationalDashboard ? (int) ($dashboardMetrics['activeRepairs'] ?? 0) : 12;
    $displayCompletedRepairs = $operationalDashboard ? (int) ($dashboardMetrics['completedRepairsToday'] ?? 0) : 4;
    $displayPendingBudgets = $operationalDashboard ? (int) ($dashboardMetrics['pendingBudgets'] ?? 0) : 4;
    $displayLowStock = $operationalDashboard ? (int) ($dashboardMetrics['lowStock'] ?? 0) : 3;
    $repairRows = $operationalDashboard ? ($activeRepairs ?? collect()) : collect();
    $appointmentRows = $operationalDashboard ? ($upcomingAppointments ?? collect()) : collect();
    $stockRows = $operationalDashboard ? ($stockAlerts ?? collect()) : collect();
    $loadRows = $operationalDashboard ? ($weeklyLoad ?? collect()) : collect();
    $firstAppointment = $appointmentRows->first();
    $firstStockAlert = $stockRows->first();
    $longestRepair = $repairRows->sortByDesc('tiempo_empleado_minutos')->first();
    $maxWeeklyLoad = max(1, (int) $loadRows->max('value'));
    $maxRepairMinutes = max(1, (int) $repairRows->max('tiempo_empleado_minutos'));
    $roleNames = $user?->roles?->pluck('name') ?? collect();
    $canViewRepairs = ! $operationalDashboard || $roleNames->intersect(['administrador', 'reparacion'])->isNotEmpty();
    $canViewParts = ! $operationalDashboard || $roleNames->intersect(['administrador', 'repuestos'])->isNotEmpty();
@endphp

@unless ($operationalDashboard)
    @include('dashboard-concepts.partials.top-module-nav')
@endunless

<section class="ssl-dashboard" aria-label="Maqueta Suzuka Sakura en Vivo">
    <div class="ssl-sakura-scene" aria-hidden="true">
        <svg viewBox="0 0 1400 780" preserveAspectRatio="xMidYMid slice">
            <path class="ssl-branch branch-one" d="M1420 34C1280 70 1200 139 1120 226C1044 309 965 343 852 354"/>
            <path class="ssl-branch branch-two" d="M1325 82C1236 162 1191 246 1178 365"/>
            <path class="ssl-branch branch-three" d="M-40 746C111 658 226 648 376 682"/>
            <path class="ssl-branch branch-four" d="M-30 102C92 125 174 177 250 244"/>
            <path class="ssl-branch branch-five" d="M1420 628C1316 588 1242 536 1176 456"/>
            @foreach ([[1354,54,1.12],[1290,82,1],[1252,116,.66],[1228,142,.78],[1198,176,.5],[1170,208,.72],[1138,238,.5],[1108,264,.82],[1072,291,.52],[1035,315,.66],[995,330,.48],[960,338,.74],[1282,208,.48],[1248,247,.64],[1219,281,.5],[1194,316,.78],[44,680,.5],[108,686,.82],[146,674,.46],[180,666,.66],[220,666,.44],[260,674,.74],[298,684,.45],[335,692,.58],[24,112,.62],[72,132,.48],[118,153,.66],[166,181,.46],[208,214,.58],[1364,610,.74],[1312,582,.5],[1264,548,.66],[1220,506,.48],[1184,461,.6]] as [$x,$y,$scale])
                <g class="ssl-blossom" transform="translate({{ $x }} {{ $y }}) scale({{ $scale }})"><circle cx="0" cy="-14" r="13"/><circle cx="14" cy="-3" r="13"/><circle cx="8" cy="14" r="13"/><circle cx="-9" cy="14" r="13"/><circle cx="-14" cy="-3" r="13"/><circle class="core" cx="0" cy="1" r="6"/></g>
            @endforeach
        </svg>
    </div>
    <div class="ssl-petals" aria-hidden="true"><i></i><i></i><i></i><i></i><i></i><i></i></div>
    <div class="ssl-race-atmosphere" aria-hidden="true"><i class="ssl-sun-disc"></i><i class="ssl-checker-field"></i><i class="ssl-speed-line"></i></div>
    <header class="ssl-header">
        <div><span>AUTOTECH S.A. / CENTRO DE OPERACIONES EN VIVO</span><div class="ssl-title-row"><svg class="ssl-autotech-mark" viewBox="0 0 120 120" role="img" aria-label="Logo AutoTech"><defs><linearGradient id="autotechMarkGradient" x1="0" y1="0" x2="1" y2="1"><stop offset="0" stop-color="#e10600"/><stop offset="1" stop-color="#ed3159"/></linearGradient></defs><path class="ssl-mark-shield" d="M15 22 60 7l45 15v35c0 27-17 45-45 57C32 102 15 84 15 57Z"/><path class="ssl-mark-a" d="M31 79 51 34h17l20 45H72l-4-10H49l-4 10Zm23-23h10l-5-13Z"/><path class="ssl-mark-speed" d="M22 87h67M29 96h51"/></svg><h1>AUTOTECH<br><em>TELEMETRÍA</em></h1><span class="ssl-f1-flag" role="img" aria-label="Bandera a cuadros de Fórmula 1"><i></i></span></div><p>Precisión, velocidad y trazabilidad para cada reparación.</p></div>
        <div class="ssl-session"><span>SESIÓN ACTIVA</span><strong>{{ now()->format('H:i:s') }}</strong><small><i></i> {{ $displayActiveRepairs }} UNIDADES EN TALLER</small></div>
    </header>

    <section class="ssl-command-strip" aria-label="Estado general del taller">
        <header><span>ESTADO DEL SISTEMA</span><b>OPERACIÓN NORMAL <i></i></b></header>
        <div>
            <article class="ssl-command-main"><span>REPARACIONES ACTIVAS</span><strong>{{ str_pad((string) $displayActiveRepairs, 2, '0', STR_PAD_LEFT) }}</strong><div><b>{{ str_pad((string) $displayCompletedRepairs, 2, '0', STR_PAD_LEFT) }}</b><small>FINALIZADAS<br>HOY</small></div></article>
            <article><span>TURNOS</span><strong>{{ str_pad((string) $displayAppointments, 2, '0', STR_PAD_LEFT) }}</strong><small>HOY</small></article>
            <article class="ssl-command-alert"><span>PENDIENTES</span><strong>{{ str_pad((string) $displayPendingBudgets, 2, '0', STR_PAD_LEFT) }}</strong><small>PRESUPUESTOS</small></article>
            <article><span>{{ $operationalDashboard ? 'ALERTAS' : 'EFICIENCIA' }}</span><strong>{{ $operationalDashboard ? str_pad((string) $displayLowStock, 2, '0', STR_PAD_LEFT) : '84' }}</strong><small>{{ $operationalDashboard ? 'DE INVENTARIO' : 'POR CIENTO' }}</small></article>
        </div>
    </section>

    <section class="ssl-editorial-grid ssl-editorial-top" aria-label="Resumen y prioridades">
        <article class="ssl-day-figure"><span>LA CIFRA DEL DÍA</span><div><strong>{{ $displayActiveRepairs }}</strong><h2>{{ $displayActiveRepairs === 1 ? 'vehículo está siendo' : 'vehículos están siendo' }}<br>reparados ahora</h2></div><p>{{ $displayCompletedRepairs }} {{ $displayCompletedRepairs === 1 ? 'orden finalizó' : 'órdenes finalizaron' }} hoy. El panel utiliza información actualizada del taller.</p></article>
        <article class="ssl-operation-note">
            <span>NOTA DE OPERACIÓN</span>
            @if ($operationalDashboard && $firstStockAlert)
                <h2>Reponer<br>{{ $firstStockAlert->nombre }}.</h2>
                <p>{{ $firstStockAlert->codigo }} dispone de {{ $firstStockAlert->stock }} unidades y requiere seguimiento.</p>
            @elseif ($operationalDashboard)
                <h2>Inventario<br>controlado.</h2>
                <p>No hay repuestos por debajo del nivel de atención configurado.</p>
            @else
                <h2>Reponer antes<br>del martes.</h2>
                <p>Baterías 65 Ah y pastillas Bosch se encuentran por debajo del nivel recomendado.</p>
            @endif
            @if ($canViewParts)
                <a href="{{ route('repuestos.index') }}">REVISAR INVENTARIO ↗</a>
            @else
                <span class="ssl-restricted-action">INVENTARIO · ACCESO RESTRINGIDO</span>
            @endif
        </article>
    </section>

    <div class="ssl-live-grid">
        <article class="ssl-map-card">
            <div class="ssl-card-title"><div><span>MAPA DEL CIRCUITO / FLUJO DE REPARACIÓN</span><h2>Circuito Internacional de Suzuka</h2></div><b>5.807 KM · 18 CURVAS</b></div>
            <svg viewBox="0 0 860 500" role="img" aria-label="Mapa operativo fiel al trazado de Suzuka con las etapas de reparación">
                <path class="ssl-track-base" d="M770 393C717 342 655 278 604 246C566 222 526 223 502 248C478 273 486 315 520 327C552 339 582 318 580 289C577 251 531 232 490 240L421 270C383 286 341 273 321 238L285 175C266 142 231 129 198 146C165 163 129 150 122 116C113 77 153 47 190 61C229 76 240 122 272 147L355 210C393 238 443 248 490 240L528 195C554 165 570 121 591 85C611 51 647 39 670 64C692 88 677 121 650 143L596 190C630 192 666 207 694 232C728 262 756 313 785 347"/>
                <path class="ssl-sector-one" d="M770 393C717 342 655 278 604 246C566 222 526 223 502 248C478 273 486 315 520 327C552 339 582 318 580 289"/>
                <path class="ssl-sector-two" d="M580 289C577 251 531 232 490 240L421 270C383 286 341 273 321 238L285 175C266 142 231 129 198 146C165 163 129 150 122 116C113 77 153 47 190 61C229 76 240 122 272 147"/>
                <path class="ssl-sector-three" d="M272 147L355 210C393 238 443 248 490 240L528 195C554 165 570 121 591 85C611 51 647 39 670 64C692 88 677 121 650 143L596 190C630 192 666 207 694 232C728 262 756 313 785 347"/>
                <path class="ssl-bridge" d="M470 229L515 258"/>
                <g class="ssl-turns">
                    @foreach ([[770,393,'01'],[780,352,'02'],[729,308,'03'],[687,268,'04'],[641,242,'05'],[600,250,'06'],[577,291,'07'],[533,329,'08'],[490,240,'09'],[525,194,'10'],[577,110,'11'],[490,240,'12'],[321,238,'13'],[198,146,'14'],[122,116,'15'],[272,147,'16'],[355,210,'17'],[421,270,'18']] as [$x,$y,$number])
                        <circle cx="{{ $x }}" cy="{{ $y }}" r="11"/><text x="{{ $x }}" y="{{ $y + 4 }}">{{ $number }}</text>
                    @endforeach
                </g>
                <g class="ssl-cars"><circle cx="690" cy="265" r="8"/><text x="700" y="259">{{ $operationalDashboard ? str($repairRows->get(0)?->numero ?? '—')->afterLast('-')->limit(4, '') : 'AD7' }}</text><circle cx="520" cy="327" r="8"/><text x="532" y="352">{{ $operationalDashboard ? str($repairRows->get(1)?->numero ?? '—')->afterLast('-')->limit(4, '') : 'AE3' }}</text><circle cx="321" cy="238" r="8"/><text x="283" y="263">{{ $operationalDashboard ? str($repairRows->get(2)?->numero ?? '—')->afterLast('-')->limit(4, '') : 'AB1' }}</text><circle cx="190" cy="61" r="8"/><text x="202" y="52">{{ $operationalDashboard ? str($repairRows->get(3)?->numero ?? '—')->afterLast('-')->limit(4, '') : 'AC4' }}</text></g>
                <g class="ssl-flow-labels"><text x="690" y="430">RECEPCIÓN</text><text x="575" y="370">DIAGNÓSTICO</text><text x="275" y="305">REPUESTOS</text><text x="105" y="40">REPARACIÓN</text><text x="600" y="48">CONTROL</text></g>
                <g class="ssl-stage-pins"><circle cx="770" cy="393" r="15"/><text x="770" y="398">A</text><circle cx="520" cy="327" r="15"/><text x="520" y="332">B</text><circle cx="321" cy="238" r="15"/><text x="321" y="243">C</text><circle cx="190" cy="61" r="15"/><text x="190" y="66">D</text><circle cx="650" cy="143" r="15"/><text x="650" y="148">E</text></g>
                <g class="ssl-drs"><path d="M780 375L810 345"/><text x="720" y="455">ZONA DE ENTREGA</text></g>
            </svg>
            <div class="ssl-flow-details" aria-label="Detalle de etapas del flujo de reparación">
                <section><b>A</b><span><strong>Recepción</strong><small>{{ $operationalDashboard ? $displayAppointments.' turnos para hoy' : '3 ingresos · 12 min prom.' }}</small></span><em>A TIEMPO</em></section>
                <section><b>B</b><span><strong>Diagnóstico</strong><small>{{ $operationalDashboard ? $displayPendingBudgets.' presupuestos pendientes' : '2 unidades · +15 min' }}</small></span><em>EN CONTROL</em></section>
                <section><b>C</b><span><strong>Repuestos</strong><small>{{ $operationalDashboard ? $displayLowStock.' alertas de inventario' : '1 reserva pendiente' }}</small></span><em>{{ $displayLowStock > 0 ? 'ALERTA' : 'NORMAL' }}</em></section>
                <section><b>D</b><span><strong>Reparación</strong><small>{{ $operationalDashboard ? $displayActiveRepairs.' unidades activas' : '4 unidades · 81%' }}</small></span><em>ACTIVO</em></section>
                <section><b>E</b><span><strong>Control final</strong><small>{{ $operationalDashboard ? $displayCompletedRepairs.' finalizadas hoy' : '2 listas · calidad 98%' }}</small></span><em>LISTO</em></section>
            </div>
            <div class="ssl-legend"><span><i></i>Sector 1 · Ingreso</span><span><i></i>Sector 2 · Taller</span><span><i></i>Sector 3 · Calidad</span><b><i></i>Vehículos activos</b></div>
        </article>

        <aside class="ssl-tower" aria-label="Telemetría de vehículos">
            <div class="ssl-tower-head"><span>POS</span><b>VEHÍCULO / DELTA</b><em>TIEMPO</em></div>
            @if ($operationalDashboard)
                @forelse ($repairRows as $repair)
                    <div class="ssl-timing-row active"><strong>{{ $loop->iteration }}</strong><i></i><span><b>{{ str($repair->numero ?? 'R'.$repair->id)->afterLast('-')->limit(5, '') }} <small>{{ $repair->vehiculo->patente }}</small></b><small>{{ $repair->tipo->nombre }}</small></span><em>{{ $repair->estado->nombre }}<small>{{ $repair->tiempoEmpleadoFormateado() }}</small></em></div>
                @empty
                    <div class="ssl-tower-empty"><i class="bi bi-check2-circle"></i><strong>Sin reparaciones activas</strong><small>El taller no registra trabajos en proceso.</small></div>
                @endforelse
                <div class="ssl-tower-note"><span>MAYOR TIEMPO REGISTRADO</span><strong>{{ $longestRepair?->numero ?? 'Sin actividad' }} · {{ $longestRepair?->tiempoEmpleadoFormateado() ?? '0 horas' }}</strong><small>Información actual del taller</small></div>
            @else
                @foreach ([['1','AD7','AD 789 GH','Frenos','—','01:42','ready'],['2','AE3','AE 321 JK','Electricidad','+0:18','02:08','active'],['3','AB1','AB 123 CD','Mantenimiento','+0:41','02:31','active'],['4','AC4','AC 456 EF','Diagnóstico','+1:06','02:56','warning'],['5','AA2','AA 204 BB','Suspensión','EN TALLER','03:12','pit'],['6','AF8','AF 882 RT','Motor','+1:48','03:38','active'],['7','AG5','AG 510 XO','Alineación','FUERA','04:01','out']] as [$position,$code,$plate,$job,$delta,$time,$state])
                    <div class="ssl-timing-row {{ $state }}"><strong>{{ $position }}</strong><i></i><span><b>{{ $code }} <small>{{ $plate }}</small></b><small>{{ $job }}</small></span><em>{{ $delta }}<small>{{ $time }} h</small></em></div>
                @endforeach
                <div class="ssl-tower-note"><span>REPARACIÓN MÁS RÁPIDA</span><strong>AD7 · 01:42 h</strong><small>Control final completado</small></div>
            @endif
        </aside>
    </div>

    <section class="ssl-operations-grid" aria-label="Centro de operaciones del taller">
        <article class="ssl-next-flag">
            @if ($operationalDashboard && $firstAppointment)
                <span>PRÓXIMA BANDERA</span><strong>{{ str($firstAppointment->hora)->substr(0, 5) }}</strong><b>TURNO · {{ $firstAppointment->vehiculo->patente }}</b><small>{{ $firstAppointment->fecha->locale('es')->isoFormat('D [de] MMMM') }} · {{ $firstAppointment->motivo }}</small><a href="{{ route('turnos.show', $firstAppointment) }}">VER TURNO ↗</a>
            @elseif ($operationalDashboard)
                <span>PRÓXIMA BANDERA</span><strong>—</strong><b>SIN TURNOS PRÓXIMOS</b><small>La agenda no registra nuevas atenciones.</small><a href="{{ route('turnos.index') }}">VER AGENDA ↗</a>
            @else
                <span>PRÓXIMA BANDERA</span><strong>09:10</strong><b>ENTREGA · AA 204 BB</b><small>Control final completado. Documentación pendiente.</small><a href="{{ route('reparaciones.index') }}">VER DETALLE ↗</a>
            @endif
        </article>

        <article class="ssl-agenda-board">
            <header><h2><b>01</b> Agenda próxima</h2><a href="{{ route('turnos.index') }}">TODOS LOS TURNOS ↗</a></header>
            <div class="ssl-table-head"><span>HORA</span><span>VEHÍCULO</span><span>TRABAJO</span><span>ESTADO</span></div>
            @if ($operationalDashboard)
                @forelse ($appointmentRows->take(3) as $turno)
                    <div class="ssl-agenda-row"><time>{{ str($turno->hora)->substr(0, 5) }}</time><strong>{{ $turno->vehiculo->patente }}</strong><span>{{ $turno->motivo }}</span><em><i></i>{{ $turno->estado->nombre }}</em></div>
                @empty
                    <div class="ssl-agenda-empty">No hay turnos próximos registrados.</div>
                @endforelse
            @else
                @foreach ([['08:30','AA 204 BB','Mantenimiento','Confirmado'],['10:00','AB 718 KT','Frenos','En camino'],['11:30','AC 113 PV','Motor','Confirmado']] as [$time,$plate,$job,$status])
                    <div class="ssl-agenda-row"><time>{{ $time }}</time><strong>{{ $plate }}</strong><span>{{ $job }}</span><em><i></i>{{ $status }}</em></div>
                @endforeach
            @endif
        </article>

        <article class="ssl-capacity-board">
            <header><h2><b>02</b> Capacidad semanal</h2></header>
            @if ($operationalDashboard)
                @foreach ($loadRows->take(5) as $day)
                    @php($loadPercentage = max(8, (int) round(((int) $day['value'] / $maxWeeklyLoad) * 100)))
                    <div><span>{{ $day['label'] }}</span><i><em style="width:{{ $loadPercentage }}%"></em></i><b>{{ $day['value'] }}</b></div>
                @endforeach
            @else
                @foreach ([['Lun','68'],['Mar','84'],['Mié','57'],['Jue','91'],['Vie','76']] as [$day,$capacity])
                    <div><span>{{ $day }}</span><i><em style="width:{{ $capacity }}%"></em></i><b>{{ $capacity }}</b></div>
                @endforeach
            @endif
        </article>

        <article class="ssl-box-board">
            <header><div><span>ESTADO DE ESTACIONES</span><h2>Puestos del taller</h2></div><b>{{ $operationalDashboard ? str_pad((string) min($displayActiveRepairs, 6), 2, '0', STR_PAD_LEFT) : '04' }}/06 ACTIVOS</b></header>
            @if ($operationalDashboard)
                @forelse ($repairRows->take(6) as $repair)
                    @php($workloadPercentage = max(8, (int) round(($repair->tiempo_empleado_minutos / $maxRepairMinutes) * 100)))
                    <div class="active"><strong>{{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}</strong><span><b>{{ $repair->vehiculo->patente }}</b><small>{{ $repair->tipo->nombre }}</small></span><i><em style="width:{{ $workloadPercentage }}%"></em></i><b>{{ $repair->tiempoEmpleadoFormateado() }}</b></div>
                @empty
                    <div class="free"><strong>01</strong><span><b>DISPONIBLE</b><small>Sin asignar</small></span><i><em style="width:100%"></em></i><b>LIBRE</b></div>
                @endforelse
            @else
                @foreach ([['01','AD 789 GH','Frenos','68','active'],['02','AB 123 CD','Mantenimiento','42','active'],['03','DISPONIBLE','Sin asignar','100','free'],['04','AE 321 JK','Electricidad','81','active'],['05','AC 456 EF','Diagnóstico','35','warning'],['06','LIMPIEZA','Disponible 08:40','18','idle']] as [$number,$plate,$task,$progress,$state])
                    <div class="{{ $state }}"><strong>{{ $number }}</strong><span><b>{{ $plate }}</b><small>{{ $task }}</small></span><i><em style="width:{{ $progress }}%"></em></i><b>{{ $progress }}%</b></div>
                @endforeach
            @endif
        </article>

        <article class="ssl-tech-board">
            <header><div><span>CONTROL TÉCNICO / EN VIVO</span><h2>Control técnico</h2></div><b>{{ $operationalDashboard ? ($displayActiveRepairs > 0 ? 'EN PROCESO' : 'SIN PENDIENTES') : '84% COMPLETO' }}</b></header>
            @if ($operationalDashboard)
                @forelse ($repairRows->take(4) as $repair)
                    @php($controlPercentage = max(8, (int) round(($repair->tiempo_empleado_minutos / $maxRepairMinutes) * 100)))
                    <div><strong>{{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}</strong><span><b>{{ $repair->numero ?? 'R'.$repair->id }}</b><small>{{ $repair->empleado->nombre }} {{ $repair->empleado->apellido }}</small></span><i><em style="width:{{ $controlPercentage }}%"></em></i><b>{{ $repair->estado->nombre }}</b></div>
                @empty
                    <div><strong>01</strong><span><b>SIN CONTROL PENDIENTE</b><small>No hay reparaciones activas</small></span><i><em style="width:100%"></em></i><b>NORMAL</b></div>
                @endforelse
            @else
                @foreach ([['01','AA 204 BB','Mantenimiento programado','100','APROBADO'],['02','AB 718 KT','Sistema de frenos','82','EN CONTROL'],['03','AC 113 PV','Reparación de motor','64','EN TALLER'],['04','AD 490 PL','Diagnóstico eléctrico','91','CONTROL']] as [$number,$plate,$task,$progress,$state])
                    <div><strong>{{ $number }}</strong><span><b>{{ $plate }}</b><small>{{ $task }}</small></span><i><em style="width:{{ $progress }}%"></em></i><b>{{ $state }}</b></div>
                @endforeach
            @endif
        </article>

        <article class="ssl-alert-board">
            <header><span>ALERTAS INTELIGENTES</span><h2>3 acciones sugeridas</h2></header>
            @if ($operationalDashboard)
                <div><i class="bi bi-box-seam"></i><span>
                    @if ($firstStockAlert)
                        <b>{{ $firstStockAlert->nombre }}</b> requiere reposición: {{ $firstStockAlert->stock }} unidades disponibles
                    @else
                        <b>Inventario normal</b> sin alertas de stock bajo
                    @endif
                </span></div>
                <div><i class="bi bi-person-gear"></i><span><b>{{ $displayPendingBudgets }} {{ $displayPendingBudgets === 1 ? 'presupuesto pendiente' : 'presupuestos pendientes' }}</b> de decisión del cliente</span></div>
                <div><i class="bi bi-clock-history"></i><span>
                    @if ($longestRepair)
                        <b>{{ $longestRepair->numero ?? 'Reparación '.$longestRepair->id }}</b> registra {{ $longestRepair->tiempoEmpleadoFormateado() }}
                    @else
                        <b>Operación al día</b> sin reparaciones activas
                    @endif
                </span></div>
            @else
                <div><i class="bi bi-box-seam"></i><span><b>Batería 65 Ah</b> alcanzó stock mínimo</span></div>
                <div><i class="bi bi-person-gear"></i><span><b>2 presupuestos</b> sin respuesta hace 48 h</span></div>
                <div><i class="bi bi-clock-history"></i><span><b>OT-00019</b> supera el tiempo estimado</span></div>
            @endif
        </article>
    </section>

    <section class="ssl-bottom-signals" aria-label="Comunicaciones y prioridades">
        <article class="ssl-delivery-banner">
            <i class="bi bi-flag-fill"></i>
            <div><span>PRIORIDAD DEL EQUIPO</span><strong>{{ $operationalDashboard ? 'Dar seguimiento a '.$displayActiveRepairs.' reparaciones activas.' : 'Entregar 4 vehículos antes de las 17:00.' }}</strong><small>{{ $operationalDashboard ? $displayPendingBudgets.' presupuestos esperan una decisión.' : 'Documentación y control final pendientes.' }}</small></div>
            @if ($canViewRepairs)
                <a href="{{ route('reparaciones.index') }}">VER REPARACIONES ↗</a>
            @endif
        </article>
        <article class="ssl-team-radio">
            <i class="bi bi-soundwave"></i>
            <div>
                <span>RADIO DEL EQUIPO</span>
                <strong>
                    @if ($operationalDashboard && $firstStockAlert)
                        Reponer {{ $firstStockAlert->nombre }} para sostener la operación.
                    @elseif ($operationalDashboard)
                        No se registran alertas críticas de inventario.
                    @else
                        El puesto 05 solicita autorización para cambiar el sensor ABS.
                    @endif
                </strong>
                <small>{{ $operationalDashboard ? 'Información sincronizada con repuestos.' : 'Repuesto confirmado · técnico asignado' }}</small>
            </div>
            @if ($canViewParts)
                <a href="{{ route('repuestos.index') }}">ABRIR REPUESTOS ↗</a>
            @endif
        </article>
        <article class="ssl-ready-banner">
            <i class="bi bi-check2-circle"></i>
            <div><span>TRABAJOS FINALIZADOS</span><strong>{{ $operationalDashboard ? $displayCompletedRepairs.' '.($displayCompletedRepairs === 1 ? 'unidad finalizada hoy.' : 'unidades finalizadas hoy.') : 'Cuatro unidades están listas para entregar.' }}</strong><small>{{ $operationalDashboard ? 'Consulta el historial para revisar sus detalles.' : 'Documentación completa y controles finales conformes.' }}</small></div>
            @if ($canViewRepairs)
                <a href="{{ route('reparaciones.index') }}">VER HISTORIAL ↗</a>
            @endif
        </article>
    </section>
</section>
