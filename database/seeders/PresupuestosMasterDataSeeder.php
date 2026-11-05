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
            ['codigo' => 'REP-BAT-001', 'nombre' => 'Batería 12 V 65 Ah', 'marca' => 'Moura', 'original' => false, 'precio_venta' => '176000.00', 'stock' => 4],
            ['codigo' => 'REP-ACE-002', 'nombre' => 'Aceite semisintético 10W-40', 'marca' => 'Shell Helix', 'original' => false, 'precio_venta' => '28700.00', 'stock' => 22],
            ['codigo' => 'REP-FIL-002', 'nombre' => 'Filtro de aire', 'marca' => 'Mann-Filter', 'original' => false, 'precio_venta' => '24900.00', 'stock' => 14],
            ['codigo' => 'REP-FIL-003', 'nombre' => 'Filtro de combustible', 'marca' => 'Bosch', 'original' => false, 'precio_venta' => '31500.00', 'stock' => 9],
            ['codigo' => 'REP-FIL-004', 'nombre' => 'Filtro de habitáculo', 'marca' => 'Mahle', 'original' => false, 'precio_venta' => '22500.00', 'stock' => 16],
            ['codigo' => 'REP-DIS-001', 'nombre' => 'Disco de freno delantero', 'marca' => 'Brembo', 'original' => false, 'precio_venta' => '118000.00', 'stock' => 6],
            ['codigo' => 'REP-LIQ-001', 'nombre' => 'Líquido de frenos DOT 4', 'marca' => 'Bosch', 'original' => false, 'precio_venta' => '15400.00', 'stock' => 20],
            ['codigo' => 'REP-AMO-001', 'nombre' => 'Amortiguador delantero', 'marca' => 'Monroe', 'original' => false, 'precio_venta' => '146000.00', 'stock' => 5],
            ['codigo' => 'REP-BUJ-001', 'nombre' => 'Bujía de encendido', 'marca' => 'NGK', 'original' => false, 'precio_venta' => '12800.00', 'stock' => 24],
            ['codigo' => 'REP-COR-001', 'nombre' => 'Correa de distribución', 'marca' => 'Gates', 'original' => false, 'precio_venta' => '89500.00', 'stock' => 7],
            ['codigo' => 'REP-TEN-001', 'nombre' => 'Tensor de distribución', 'marca' => 'SKF', 'original' => false, 'precio_venta' => '77500.00', 'stock' => 6],
            ['codigo' => 'REP-ALT-001', 'nombre' => 'Alternador 12 V', 'marca' => 'Bosch', 'original' => false, 'precio_venta' => '398000.00', 'stock' => 3],
            ['codigo' => 'REP-ARR-001', 'nombre' => 'Motor de arranque', 'marca' => 'Valeo', 'original' => false, 'precio_venta' => '352000.00', 'stock' => 4],
            ['codigo' => 'REP-LAM-001', 'nombre' => 'Lámpara halógena H7', 'marca' => 'Osram', 'original' => false, 'precio_venta' => '18500.00', 'stock' => 30],
            ['codigo' => 'REP-FUS-001', 'nombre' => 'Kit de fusibles automotrices', 'marca' => 'Bussmann', 'original' => false, 'precio_venta' => '11900.00', 'stock' => 25],
            ['codigo' => 'REP-REF-001', 'nombre' => 'Refrigerante orgánico concentrado', 'marca' => 'TotalEnergies', 'original' => false, 'precio_venta' => '21400.00', 'stock' => 17],
            ['codigo' => 'REP-BOM-001', 'nombre' => 'Bomba de agua', 'marca' => 'Dolz', 'original' => false, 'precio_venta' => '96500.00', 'stock' => 7],
            ['codigo' => 'REP-TER-001', 'nombre' => 'Termostato de motor', 'marca' => 'Mahle', 'original' => false, 'precio_venta' => '42800.00', 'stock' => 10],
            ['codigo' => 'REP-EMB-001', 'nombre' => 'Kit de embrague', 'marca' => 'Sachs', 'original' => false, 'precio_venta' => '415000.00', 'stock' => 6],
            ['codigo' => 'REP-ROT-001', 'nombre' => 'Rótula de suspensión', 'marca' => 'TRW', 'original' => false, 'precio_venta' => '58500.00', 'stock' => 8],
            ['codigo' => 'REP-EXT-001', 'nombre' => 'Extremo de dirección', 'marca' => 'Lemförder', 'original' => false, 'precio_venta' => '63200.00', 'stock' => 8],
            ['codigo' => 'REP-ESC-001', 'nombre' => 'Escobilla limpiaparabrisas', 'marca' => 'Bosch', 'original' => false, 'precio_venta' => '26900.00', 'stock' => 18],
            ['codigo' => 'REP-SEN-001', 'nombre' => 'Sensor de oxígeno', 'marca' => 'Denso', 'original' => false, 'precio_venta' => '128000.00', 'stock' => 6],
            ['codigo' => 'REP-BOB-001', 'nombre' => 'Bobina de encendido', 'marca' => 'Delphi', 'original' => false, 'precio_venta' => '118500.00', 'stock' => 7],
            ['codigo' => 'REP-CAB-001', 'nombre' => 'Juego de cables de bujía', 'marca' => 'NGK', 'original' => false, 'precio_venta' => '74600.00', 'stock' => 9],
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
