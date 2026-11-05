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
        ])->each(fn (array $estado): EstadoTurno => EstadoTurno::query()->firstOrCreate(
            ['codigo' => $estado['codigo']],
            $estado,
        ));

        $clientes = collect([
            ['dni' => '30111222', 'nombre' => 'Ana', 'apellido' => 'Martínez', 'telefono' => '11 4123-7788', 'email' => 'ana.martinez@example.com'],
            ['dni' => '28777666', 'nombre' => 'Carlos', 'apellido' => 'Ramírez', 'telefono' => '11 4988-2301', 'email' => 'carlos.ramirez@example.com'],
            ['dni' => '33999111', 'nombre' => 'Lucía', 'apellido' => 'Fernández', 'telefono' => '11 4555-9012', 'email' => 'lucia.fernandez@example.com'],
            ['dni' => '26444888', 'nombre' => 'Jorge', 'apellido' => 'Pereyra', 'telefono' => '11 4302-7744', 'email' => 'jorge.pereyra@example.com'],
            ['dni' => '35666777', 'nombre' => 'Mariana', 'apellido' => 'López', 'telefono' => '11 4762-1180', 'email' => 'mariana.lopez@example.com'],
            ['dni' => '31888555', 'nombre' => 'Santiago', 'apellido' => 'Benítez', 'telefono' => '11 4201-6632', 'email' => 'santiago.benitez@example.com'],
            ['dni' => '37222444', 'nombre' => 'Paula', 'apellido' => 'Rossi', 'telefono' => '11 4546-9021', 'email' => 'paula.rossi@example.com'],
            ['dni' => '29555333', 'nombre' => 'Federico', 'apellido' => 'Suárez', 'telefono' => '11 4664-2289', 'email' => 'federico.suarez@example.com'],
        ])->mapWithKeys(function (array $cliente): array {
            $model = Cliente::query()->firstOrCreate(
                ['dni' => $cliente['dni']],
                [...$cliente, 'activo' => true],
            );

            return [$cliente['dni'] => $model];
        });

        collect([
            ['cliente_dni' => '30111222', 'patente' => 'AB123CD', 'marca' => 'Toyota', 'modelo' => 'Corolla', 'anio' => 2019, 'color' => 'Gris'],
            ['cliente_dni' => '30111222', 'patente' => 'AC456EF', 'marca' => 'Ford', 'modelo' => 'EcoSport', 'anio' => 2018, 'color' => 'Blanco'],
            ['cliente_dni' => '28777666', 'patente' => 'AD789GH', 'marca' => 'Volkswagen', 'modelo' => 'Amarok', 'anio' => 2021, 'color' => 'Negro'],
            ['cliente_dni' => '33999111', 'patente' => 'AE321JK', 'marca' => 'Chevrolet', 'modelo' => 'Onix', 'anio' => 2022, 'color' => 'Azul'],
            ['cliente_dni' => '26444888', 'patente' => 'AF654LM', 'marca' => 'Renault', 'modelo' => 'Sandero', 'anio' => 2020, 'color' => 'Rojo'],
            ['cliente_dni' => '26444888', 'patente' => 'AG987NP', 'marca' => 'Fiat', 'modelo' => 'Cronos', 'anio' => 2023, 'color' => 'Plata'],
            ['cliente_dni' => '35666777', 'patente' => 'AH246QR', 'marca' => 'Peugeot', 'modelo' => '208', 'anio' => 2022, 'color' => 'Blanco'],
            ['cliente_dni' => '31888555', 'patente' => 'AI135ST', 'marca' => 'Honda', 'modelo' => 'Civic', 'anio' => 2017, 'color' => 'Azul oscuro'],
            ['cliente_dni' => '31888555', 'patente' => 'AJ864UV', 'marca' => 'Nissan', 'modelo' => 'Frontier', 'anio' => 2021, 'color' => 'Gris'],
            ['cliente_dni' => '37222444', 'patente' => 'AK579WX', 'marca' => 'Jeep', 'modelo' => 'Renegade', 'anio' => 2020, 'color' => 'Verde'],
            ['cliente_dni' => '29555333', 'patente' => 'AL318YZ', 'marca' => 'Citroën', 'modelo' => 'C4 Cactus', 'anio' => 2019, 'color' => 'Negro'],
            ['cliente_dni' => '29555333', 'patente' => 'AM742BC', 'marca' => 'Ford', 'modelo' => 'Ranger', 'anio' => 2022, 'color' => 'Azul'],
        ])->each(function (array $vehiculo) use ($clientes): void {
            Vehiculo::query()->firstOrCreate([
                'patente' => $vehiculo['patente'],
            ], [
                'cliente_id' => $clientes[$vehiculo['cliente_dni']]->id,
                'marca' => $vehiculo['marca'],
                'modelo' => $vehiculo['modelo'],
                'anio' => $vehiculo['anio'],
                'color' => $vehiculo['color'],
                'activo' => true,
            ]);
        });
    }
}
