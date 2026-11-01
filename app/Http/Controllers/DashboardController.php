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

        $today = CarbonImmutable::today();
        $metrics = [
            'appointmentsToday' => Turno::query()->whereDate('fecha', $today)->count(),
            'activeRepairs' => Reparacion::query()->whereHas('estado', fn ($query) => $query->where('codigo', 'en-proceso'))->count(),
            'pendingBudgets' => Presupuesto::query()->doesntHave('aprobacion')->count(),
            'lowStock' => Repuesto::query()->where('activo', true)->where('stock', '<=', 5)->count(),
            'activeClients' => Cliente::query()->where('activo', true)->count(),
            'activeSuppliers' => Proveedor::query()->where('activo', true)->count(),
        ];

        $upcomingAppointments = Turno::query()
            ->with(['cliente', 'vehiculo', 'estado'])
            ->whereDate('fecha', '>=', $today)
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

        $weeklyLoad = collect(range(6, 0))->map(function (int $daysAgo) use ($today): array {
            $date = $today->subDays($daysAgo);

            return [
                'label' => $date->locale('es')->isoFormat('dd'),
                'value' => Turno::query()->whereDate('fecha', $date)->count(),
            ];
        });

        return [
            'metrics' => $metrics,
            'modules' => $this->modules($user),
            'stockAlerts' => $stockAlerts,
            'upcomingAppointments' => $upcomingAppointments,
            'user' => $user,
            'weeklyLoad' => $weeklyLoad,
        ];
    }

    /** @return array<int, array<string, string|null>> */
    private function modules(User $user): array
    {
        $roleNames = $user->roles->pluck('name');

        return [
            ['name' => 'Clientes', 'description' => 'Personas y vehículos', 'icon' => 'bi-people', 'color' => 'teal'],
            ['name' => 'Proveedores', 'description' => 'Red de abastecimiento', 'icon' => 'bi-truck', 'color' => 'coral'],
            ['name' => 'Turnos', 'description' => 'Agenda del taller', 'icon' => 'bi-calendar3', 'color' => 'blue', 'route' => 'turnos.index'],
            [
                'name' => 'Reparaciones',
                'description' => 'Órdenes en seguimiento',
                'icon' => 'bi-tools',
                'color' => 'amber',
                'route' => $roleNames->intersect(['administrador', 'reparacion'])->isNotEmpty()
                    ? 'reparaciones.index'
                    : null,
            ],
            [
                'name' => 'Repuestos',
                'description' => 'Stock y disponibilidad',
                'icon' => 'bi-gear',
                'color' => 'slate',
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
        ];
    }
}
