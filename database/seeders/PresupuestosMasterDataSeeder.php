<?php

namespace Database\Seeders;

use App\Models\Empleado;
use App\Models\Especialidad;
use App\Models\EstadoEmpleado;
use App\Models\EstadoOrdenTrabajo;
use App\Models\Repuesto;
use App\Models\TipoServicio;
use Illuminate\Database\Seeder;

class PresupuestosMasterDataSeeder extends Seeder
{
    /**
     * Seed employees, services, spare parts and work-order states used by Sprint 1.
     */
    public function run(): void
    {
        $especialidades = collect([
            ['codigo' => 'mecanica-general', 'nombre' => 'Mecánica general'],
            ['codigo' => 'electricidad', 'nombre' => 'Electricidad automotriz'],
            ['codigo' => 'frenos-suspension', 'nombre' => 'Frenos y suspensión'],
        ])->mapWithKeys(function (array $especialidad): array {
            $model = Especialidad::query()->firstOrCreate(
                ['codigo' => $especialidad['codigo']],
                $especialidad,
            );

            return [$especialidad['codigo'] => $model];
        });

        $estadosEmpleado = collect([
            ['codigo' => 'activo', 'nombre' => 'Activo'],
            ['codigo' => 'inactivo', 'nombre' => 'Inactivo'],
            ['codigo' => 'licencia', 'nombre' => 'De licencia'],
        ])->mapWithKeys(function (array $estado): array {
            $model = EstadoEmpleado::query()->firstOrCreate(
                ['codigo' => $estado['codigo']],
                $estado,
            );

            return [$estado['codigo'] => $model];
        });

        collect([
            ['legajo' => 'EMP-001', 'nombre' => 'Martín', 'apellido' => 'Gómez', 'especialidad' => 'mecanica-general'],
            ['legajo' => 'EMP-002', 'nombre' => 'Valeria', 'apellido' => 'Sosa', 'especialidad' => 'electricidad'],
            ['legajo' => 'EMP-003', 'nombre' => 'Diego', 'apellido' => 'Acosta', 'especialidad' => 'frenos-suspension'],
        ])->each(fn (array $empleado): Empleado => Empleado::query()->firstOrCreate(
            ['legajo' => $empleado['legajo']],
            [
                'nombre' => $empleado['nombre'],
                'apellido' => $empleado['apellido'],
                'especialidad_id' => $especialidades[$empleado['especialidad']]->id,
                'estado_empleado_id' => $estadosEmpleado['activo']->id,
            ],
        ));

        collect([
            ['codigo' => 'service-programado', 'nombre' => 'Service programado', 'descripcion' => 'Control general y mantenimiento preventivo.', 'especialidad' => 'mecanica-general', 'precio_base' => '85000.00'],
            ['codigo' => 'diagnostico-motor', 'nombre' => 'Diagnóstico de motor', 'descripcion' => 'Evaluación mecánica y diagnóstico de fallas.', 'especialidad' => 'mecanica-general', 'precio_base' => '45000.00'],
            ['codigo' => 'diagnostico-electrico', 'nombre' => 'Diagnóstico eléctrico', 'descripcion' => 'Control de batería, alternador y sistema eléctrico.', 'especialidad' => 'electricidad', 'precio_base' => '42000.00'],
            ['codigo' => 'revision-frenos', 'nombre' => 'Revisión de frenos', 'descripcion' => 'Inspección del sistema de frenado.', 'especialidad' => 'frenos-suspension', 'precio_base' => '38000.00'],
            ['codigo' => 'revision-suspension', 'nombre' => 'Revisión de suspensión', 'descripcion' => 'Control de amortiguadores, bujes y tren delantero.', 'especialidad' => 'frenos-suspension', 'precio_base' => '40000.00'],
        ])->each(fn (array $servicio): TipoServicio => TipoServicio::query()->firstOrCreate(
            ['codigo' => $servicio['codigo']],
            [
                'nombre' => $servicio['nombre'],
                'descripcion' => $servicio['descripcion'],
                'especialidad_id' => $especialidades[$servicio['especialidad']]->id,
                'precio_base' => $servicio['precio_base'],
                'activo' => true,
            ],
        ));

        collect([
            ['codigo' => 'REP-ACE-001', 'nombre' => 'Aceite sintético 5W-30', 'marca' => 'YPF Elaion', 'original' => false, 'precio_venta' => '32500.00', 'stock' => 18],
            ['codigo' => 'REP-FIL-001', 'nombre' => 'Filtro de aceite', 'marca' => 'Toyota', 'original' => true, 'precio_venta' => '18500.00', 'stock' => 12],
            ['codigo' => 'REP-PAS-001', 'nombre' => 'Juego de pastillas de freno', 'marca' => 'Bosch', 'original' => false, 'precio_venta' => '68000.00', 'stock' => 8],
            ['codigo' => 'REP-BAT-001', 'nombre' => 'Batería 12 V 65 Ah', 'marca' => 'Moura', 'original' => false, 'precio_venta' => '176000.00', 'stock' => 5],
        ])->each(fn (array $repuesto): Repuesto => Repuesto::query()->firstOrCreate(
            ['codigo' => $repuesto['codigo']],
            [
                'nombre' => $repuesto['nombre'],
                'marca' => $repuesto['marca'],
                'original' => $repuesto['original'],
                'precio_venta' => $repuesto['precio_venta'],
                'stock' => $repuesto['stock'],
                'activo' => true,
            ],
        ));

        collect([
            ['codigo' => 'emitida', 'nombre' => 'Emitida'],
            ['codigo' => 'en-proceso', 'nombre' => 'En proceso'],
            ['codigo' => 'finalizada', 'nombre' => 'Finalizada'],
            ['codigo' => 'cancelada', 'nombre' => 'Cancelada'],
        ])->each(fn (array $estado): EstadoOrdenTrabajo => EstadoOrdenTrabajo::query()->firstOrCreate(
            ['codigo' => $estado['codigo']],
            $estado,
        ));
    }
}
