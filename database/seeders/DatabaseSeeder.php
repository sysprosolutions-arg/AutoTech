<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $roles = collect([
            ['name' => 'administrador', 'display_name' => 'Administrador', 'description' => 'Acceso técnico y funcional completo.'],
            ['name' => 'clientes', 'display_name' => 'Responsable de Clientes', 'description' => 'Gestión de clientes y vehículos.'],
            ['name' => 'proveedores', 'display_name' => 'Responsable de Proveedores', 'description' => 'Gestión de proveedores.'],
            ['name' => 'repuestos', 'display_name' => 'Responsable de Repuestos', 'description' => 'Gestión de repuestos y stock.'],
            ['name' => 'compras', 'display_name' => 'Responsable de Compras', 'description' => 'Gestión de compras.'],
            ['name' => 'reparacion', 'display_name' => 'Responsable de Reparación', 'description' => 'Gestión de reparaciones y órdenes de trabajo.'],
            ['name' => 'turnos', 'display_name' => 'Responsable de Turnos', 'description' => 'Gestión de turnos y presupuestos.'],
            ['name' => 'facturacion', 'display_name' => 'Responsable de Facturación', 'description' => 'Gestión de facturas.'],
        ])->map(fn (array $role): Role => Role::query()->updateOrCreate(
            ['name' => $role['name']],
            $role,
        ));

        $administrator = User::query()->updateOrCreate([
            'username' => 'admin',
        ], [
            'name' => 'Administrador AutoTech',
            'email' => 'admin@autotech.local',
            'password' => Hash::make(env('AUTOTECH_DEMO_PASSWORD', 'AutoTech#2026')),
            'active' => true,
        ]);

        $administrator->roles()->sync($roles->pluck('id'));
    }
}
