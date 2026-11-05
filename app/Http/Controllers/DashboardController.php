<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Presupuesto;
use App\Models\Proveedor;
use App\Models\Reparacion;
use App\Models\Repuesto;
use App\Models\Turno;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request): View
    {
        return view('dashboard', $this->dashboardData($request));
    }

    public function concepts(Request $request): View
    {
        return view('dashboard-concepts.index', [
            ...$this->dashboardData($request),
            'concepts' => $this->conceptCatalog(),
        ]);
    }

    public function concept(Request $request, string $concept): View
    {
        $concepts = $this->conceptCatalog();
        abort_unless(array_key_exists($concept, $concepts), 404);

        return view('dashboard-concepts.show', [
            ...$this->dashboardData($request),
            'concept' => $concepts[$concept],
            'concepts' => $concepts,
            'preview' => [
                'appointmentsToday' => 7,
                'activeRepairs' => 12,
                'pendingBudgets' => 4,
                'lowStock' => 3,
                'completionRate' => 84,
                'occupationRate' => 72,
                'monthlyRevenue' => '$8,4 M',
                'averageTicket' => '$286 K',
                'weeklyLoad' => [48, 72, 65, 88, 76, 54, 31],
            ],
        ]);
    }

    /** @return array<string, mixed> */
    private function dashboardData(Request $request): array
    {
        $user = $request->user();
        abort_unless($user instanceof User, 401);
        $user->loadMissing('roles');

        $now = CarbonImmutable::now();
        $today = $now->startOfDay();
        $metrics = [
            'appointmentsToday' => Turno::query()
                ->whereDate('fecha', $today)
                ->whereHas('estado', fn (Builder $query): Builder => $query->where('codigo', '!=', 'cancelado'))
                ->count(),
            'activeRepairs' => Reparacion::query()->whereHas('estado', fn (Builder $query): Builder => $query->where('codigo', 'en-proceso'))->count(),
            'completedRepairsToday' => Reparacion::query()->whereNotNull('fecha_fin')->whereDate('fecha_fin', $today)->count(),
            'pendingBudgets' => Presupuesto::query()
                ->where(function (Builder $query): void {
                    $query
                        ->doesntHave('aprobacion')
                        ->orWhereHas('aprobacion', fn (Builder $approvalQuery): Builder => $approvalQuery->where('estado', 'pendiente'));
                })
                ->count(),
            'lowStock' => Repuesto::query()->where('activo', true)->where('stock', '<=', 5)->count(),
            'activeClients' => Cliente::query()->where('activo', true)->count(),
            'activeSuppliers' => Proveedor::query()->where('activo', true)->count(),
        ];

        $upcomingAppointments = Turno::query()
            ->with(['cliente', 'vehiculo', 'estado'])
            ->whereHas('estado', fn (Builder $query): Builder => $query->whereNotIn('codigo', ['atendido', 'cancelado']))
            ->where(function (Builder $query) use ($now, $today): void {
                $query
                    ->whereDate('fecha', '>', $today)
                    ->orWhere(function (Builder $todayQuery) use ($now, $today): void {
                        $todayQuery
                            ->whereDate('fecha', $today)
                            ->whereTime('hora', '>=', $now->format('H:i:s'));
                    });
            })
            ->orderBy('fecha')
            ->orderBy('hora')
            ->limit(4)
            ->get();

        $stockAlerts = Repuesto::query()
            ->where('activo', true)
            ->where('stock', '<=', 5)
            ->orderBy('stock')
            ->orderBy('nombre')
            ->limit(4)
            ->get();

        $activeRepairs = Reparacion::query()
            ->with(['vehiculo', 'empleado', 'tipo', 'estado'])
            ->whereHas('estado', fn (Builder $query): Builder => $query->where('codigo', 'en-proceso'))
            ->orderBy('fecha_inicio')
            ->limit(7)
            ->get();

        $weeklyLoad = collect(range(4, 0))->map(function (int $daysAgo) use ($today): array {
            $date = $today->subDays($daysAgo);

            return [
                'date' => $date->toDateString(),
                'label' => $date->locale('es')->isoFormat('dd'),
                'value' => Turno::query()->whereDate('fecha', $date)->count(),
            ];
        });

        return [
            'metrics' => $metrics,
            'modules' => $this->modules($user),
            'activeRepairs' => $activeRepairs,
            'stockAlerts' => $stockAlerts,
            'upcomingAppointments' => $upcomingAppointments,
            'user' => $user,
            'weeklyLoad' => $weeklyLoad,
            'dashboardUpdatedAt' => $now,
        ];
    }

    /** @return array<int, array<string, mixed>> */
    private function modules(User $user): array
    {
        $roleNames = $user->roles->pluck('name');

        return [
            ['name' => 'Clientes', 'description' => 'Personas y vehículos', 'icon' => 'bi-people', 'color' => 'teal'],
            ['name' => 'Proveedores', 'description' => 'Red de abastecimiento', 'icon' => 'bi-truck', 'color' => 'coral'],
            [
                'name' => 'Turnos',
                'description' => 'Agenda del taller',
                'icon' => 'bi-calendar3',
                'color' => 'blue',
                'visible' => $roleNames->intersect(['administrador', 'turnos'])->isNotEmpty(),
                'route' => $roleNames->intersect(['administrador', 'turnos'])->isNotEmpty()
                    ? 'turnos.index'
                    : null,
            ],
            [
                'name' => 'Reparaciones',
                'description' => 'Órdenes en seguimiento',
                'icon' => 'bi-tools',
                'color' => 'amber',
                'visible' => $roleNames->intersect(['administrador', 'reparacion'])->isNotEmpty(),
                'route' => $roleNames->intersect(['administrador', 'reparacion'])->isNotEmpty()
                    ? 'reparaciones.index'
                    : null,
            ],
            [
                'name' => 'Repuestos',
                'description' => 'Stock y disponibilidad',
                'icon' => 'bi-gear',
                'color' => 'slate',
                'visible' => $roleNames->intersect(['administrador', 'repuestos'])->isNotEmpty(),
                'route' => $roleNames->intersect(['administrador', 'repuestos'])->isNotEmpty()
                    ? 'repuestos.index'
                    : null,
            ],
            ['name' => 'Compras', 'description' => 'Abastecimiento', 'icon' => 'bi-cart3', 'color' => 'purple'],
            ['name' => 'Facturación', 'description' => 'Comprobantes y análisis', 'icon' => 'bi-receipt', 'color' => 'green'],
        ];
    }

    /** @return array<string, array<string, string>> */
    private function conceptCatalog(): array
    {
        return [
            'control-tower' => ['slug' => 'control-tower', 'number' => '01', 'name' => 'Torre de control', 'tagline' => 'Precisión ejecutiva', 'description' => 'Panel oscuro, concentrado y orientado a decisiones rápidas.', 'icon' => 'bi-radar'],
            'blueprint' => ['slug' => 'blueprint', 'number' => '02', 'name' => 'Plano de taller', 'tagline' => 'Ingeniería visible', 'description' => 'Retícula técnica, cian eléctrico y lectura por estaciones.', 'icon' => 'bi-bounding-box-circles'],
            'sunset' => ['slug' => 'sunset', 'number' => '03', 'name' => 'Atardecer mecánico', 'tagline' => 'Editorial y humano', 'description' => 'Una portada cálida con curvas, profundidad y ritmo visual.', 'icon' => 'bi-sunset'],
            'night-drive' => ['slug' => 'night-drive', 'number' => '04', 'name' => 'Ruta nocturna', 'tagline' => 'Neón operativo', 'description' => 'Alto contraste, mapas de intensidad y señales luminosas.', 'icon' => 'bi-sign-turn-right'],
            'ledger' => ['slug' => 'ledger', 'number' => '05', 'name' => 'Libro de taller', 'tagline' => 'Datos sin adorno', 'description' => 'Tipografía expresiva, bloques impresos y jerarquía radical.', 'icon' => 'bi-journal-richtext'],
            'aurora' => ['slug' => 'aurora', 'number' => '06', 'name' => 'Aurora', 'tagline' => 'Calma tecnológica', 'description' => 'Cristal translúcido, color atmosférico y métricas suaves.', 'icon' => 'bi-stars'],
            'white-atelier' => ['slug' => 'white-atelier', 'number' => '07', 'name' => 'Atelier blanco', 'tagline' => 'Precisión serena', 'description' => 'Marfil, azul cobalto y una composición de estudio contemporáneo.', 'icon' => 'bi-rulers'],
            'pit-lane' => ['slug' => 'pit-lane', 'number' => '08', 'name' => 'Pit Lane', 'tagline' => 'Ritmo de competición', 'description' => 'Telemetría clara, acentos rojos y lectura inmediata del taller.', 'icon' => 'bi-speedometer2'],
            'garden' => ['slug' => 'garden', 'number' => '09', 'name' => 'Jardín de servicio', 'tagline' => 'Operación orgánica', 'description' => 'Verdes suaves, formas naturales y una experiencia cercana.', 'icon' => 'bi-flower1'],
            'bauhaus' => ['slug' => 'bauhaus', 'number' => '10', 'name' => 'Geometría pop', 'tagline' => 'Energía modular', 'description' => 'Bloques gráficos, color primario y métricas con carácter.', 'icon' => 'bi-circle-square'],
            'open-sky' => ['slug' => 'open-sky', 'number' => '11', 'name' => 'Cielo abierto', 'tagline' => 'Claridad atmosférica', 'description' => 'Azules luminosos, capas translúcidas y una interfaz que respira.', 'icon' => 'bi-cloud-sun'],
            'swiss-grid' => ['slug' => 'swiss-grid', 'number' => '12', 'name' => 'Sistema suizo', 'tagline' => 'Orden radical', 'description' => 'Retícula editorial, blanco preciso y señalética roja.', 'icon' => 'bi-grid-1x2'],
            'pole-position' => ['slug' => 'pole-position', 'number' => '13', 'name' => 'Pole Position', 'tagline' => 'Parrilla de salida', 'description' => 'Tablero de carrera, sectores y prioridades listas para largar.', 'icon' => 'bi-flag'],
            'monaco-telemetry' => ['slug' => 'monaco-telemetry', 'number' => '14', 'name' => 'Mónaco Data Club', 'tagline' => 'Telemetría de precisión', 'description' => 'Lujo deportivo, trazado urbano y datos técnicos refinados.', 'icon' => 'bi-bezier2'],
            'aero-pulse' => ['slug' => 'aero-pulse', 'number' => '15', 'name' => 'Aero Pulse', 'tagline' => 'Velocidad fluida', 'description' => 'Aerodinámica luminosa, energía eléctrica y movimiento continuo.', 'icon' => 'bi-wind'],
            'grand-prix-atelier' => ['slug' => 'grand-prix-atelier', 'number' => '16', 'name' => 'Grand Prix Atelier', 'tagline' => 'Carrera ejecutiva', 'description' => 'Tablero de sectores con acabado editorial y elegancia deportiva.', 'icon' => 'bi-stopwatch'],
            'riviera-grid' => ['slug' => 'riviera-grid', 'number' => '17', 'name' => 'Riviera Grid', 'tagline' => 'Circuito mediterráneo', 'description' => 'Telemetría costera, parrilla dinámica y color técnico refinado.', 'icon' => 'bi-water'],
            'parc-ferme' => ['slug' => 'parc-ferme', 'number' => '18', 'name' => 'Parc Fermé', 'tagline' => 'Control de precisión', 'description' => 'Inspección premium, datos homologados y prioridades bajo control.', 'icon' => 'bi-shield-check'],
            'monza-pit-wall' => ['slug' => 'monza-pit-wall', 'number' => '19', 'name' => 'Monza Pit Wall', 'tagline' => 'Velocidad de reparación', 'description' => 'El flujo del taller recorre Monza con boxes, tiempos y alertas en vivo.', 'icon' => 'bi-flag-fill'],
            'silverstone-command' => ['slug' => 'silverstone-command', 'number' => '20', 'name' => 'Silverstone Command', 'tagline' => 'Race control del taller', 'description' => 'Estaciones, equipos y entregas coordinados sobre un circuito histórico.', 'icon' => 'bi-broadcast-pin'],
            'suzuka-flow' => ['slug' => 'suzuka-flow', 'number' => '21', 'name' => 'Suzuka Flow', 'tagline' => 'Precisión en ocho', 'description' => 'Un trazado técnico para supervisar cada etapa, recurso y control de calidad.', 'icon' => 'bi-infinity'],
            'suzuka-sakura-live' => ['slug' => 'suzuka-sakura-live', 'number' => '22', 'name' => 'Suzuka Sakura en Vivo', 'tagline' => 'Telemetría en primavera', 'description' => 'Suzuka, sakura y seguimiento de cada reparación en una torre de tiempos.', 'icon' => 'bi-flower2'],
            'monaco-harbour-live' => ['slug' => 'monaco-harbour-live', 'number' => '23', 'name' => 'Monaco Harbour Live', 'tagline' => 'Precisión en la Riviera', 'description' => 'Circuito urbano, datos de vehículos y estaciones frente al puerto.', 'icon' => 'bi-water'],
            'interlagos-pulse' => ['slug' => 'interlagos-pulse', 'number' => '24', 'name' => 'Interlagos Pulse', 'tagline' => 'Energía de taller', 'description' => 'El circuito brasileño organiza ritmos, boxes y tiempos de entrega.', 'icon' => 'bi-activity'],
            'spa-rain-radar' => ['slug' => 'spa-rain-radar', 'number' => '25', 'name' => 'Spa Rain Radar', 'tagline' => 'Estrategia en las Ardenas', 'description' => 'Clima, elevación y reparaciones sincronizadas sobre Spa-Francorchamps.', 'icon' => 'bi-cloud-rain'],
            'zandvoort-dune-live' => ['slug' => 'zandvoort-dune-live', 'number' => '26', 'name' => 'Zandvoort Dune Live', 'tagline' => 'Datos entre dunas', 'description' => 'Curvas peraltadas, viento costero y un taller teñido de naranja.', 'icon' => 'bi-wind'],
            'singapore-night-grid' => ['slug' => 'singapore-night-grid', 'number' => '27', 'name' => 'Singapore Night Grid', 'tagline' => 'Control urbano nocturno', 'description' => 'Telemetría luminosa, skyline y operación de precisión en Marina Bay.', 'icon' => 'bi-buildings'],
        ];
    }
}
