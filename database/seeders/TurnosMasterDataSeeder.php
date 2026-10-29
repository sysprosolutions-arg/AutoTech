<?php

namespace Database\Seeders;

use App\Models\Cliente;
use App\Models\EstadoTurno;
use App\Models\Vehiculo;
use Illuminate\Database\Seeder;

class TurnosMasterDataSeeder extends Seeder
{
    /**
     * Seed clients, vehicles and appointment states used by Sprint 1.
     */
    public function run(): void
    {
        collect([
            ['codigo' => 'pendiente', 'nombre' => 'Pendiente'],
            ['codigo' => 'confirmado', 'nombre' => 'Confirmado'],
            ['codigo' => 'atendido', 'nombre' => 'Atendido'],
            ['codigo' => 'cancelado', 'nombre' => 'Cancelado'],
        ])->each(fn (array $estado): EstadoTurno => EstadoTurno::query()->updateOrCreate(
            ['codigo' => $estado['codigo']],
            $estado,
        ));

        $ana = Cliente::query()->updateOrCreate([
            'dni' => '30111222',
        ], [
            'nombre' => 'Ana',
            'apellido' => 'Martínez',
            'telefono' => '11 4123-7788',
            'email' => 'ana.martinez@example.com',
            'activo' => true,
        ]);

        $carlos = Cliente::query()->updateOrCreate([
            'dni' => '28777666',
        ], [
            'nombre' => 'Carlos',
            'apellido' => 'Ramírez',
            'telefono' => '11 4988-2301',
            'email' => 'carlos.ramirez@example.com',
            'activo' => true,
        ]);

        $lucia = Cliente::query()->updateOrCreate([
            'dni' => '33999111',
        ], [
            'nombre' => 'Lucía',
            'apellido' => 'Fernández',
            'telefono' => '11 4555-9012',
            'email' => 'lucia.fernandez@example.com',
            'activo' => true,
        ]);

        collect([
            ['cliente' => $ana, 'patente' => 'AB123CD', 'marca' => 'Toyota', 'modelo' => 'Corolla', 'anio' => 2019, 'color' => 'Gris'],
            ['cliente' => $ana, 'patente' => 'AC456EF', 'marca' => 'Ford', 'modelo' => 'EcoSport', 'anio' => 2018, 'color' => 'Blanco'],
            ['cliente' => $carlos, 'patente' => 'AD789GH', 'marca' => 'Volkswagen', 'modelo' => 'Amarok', 'anio' => 2021, 'color' => 'Negro'],
            ['cliente' => $lucia, 'patente' => 'AE321JK', 'marca' => 'Chevrolet', 'modelo' => 'Onix', 'anio' => 2022, 'color' => 'Azul'],
        ])->each(function (array $vehiculo): void {
            Vehiculo::query()->updateOrCreate([
                'patente' => $vehiculo['patente'],
            ], [
                'cliente_id' => $vehiculo['cliente']->id,
                'marca' => $vehiculo['marca'],
                'modelo' => $vehiculo['modelo'],
                'anio' => $vehiculo['anio'],
                'color' => $vehiculo['color'],
                'activo' => true,
            ]);
        });
    }
}
