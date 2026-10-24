<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request): View
    {
        $user = $request->user();
        abort_unless($user instanceof User, 401);
        $user->loadMissing('roles');

        $modules = [
            ['name' => 'Clientes', 'description' => 'Personas y vehículos', 'icon' => 'bi-people', 'color' => 'teal'],
            ['name' => 'Turnos', 'description' => 'Agenda del taller', 'icon' => 'bi-calendar3', 'color' => 'blue'],
            ['name' => 'Reparaciones', 'description' => 'Órdenes en seguimiento', 'icon' => 'bi-tools', 'color' => 'amber'],
            ['name' => 'Repuestos', 'description' => 'Stock y disponibilidad', 'icon' => 'bi-gear', 'color' => 'slate'],
            ['name' => 'Compras', 'description' => 'Abastecimiento', 'icon' => 'bi-cart3', 'color' => 'purple'],
            ['name' => 'Facturación', 'description' => 'Comprobantes y análisis', 'icon' => 'bi-receipt', 'color' => 'green'],
        ];

        return view('dashboard', [
            'modules' => $modules,
            'user' => $user,
        ]);
    }
}
