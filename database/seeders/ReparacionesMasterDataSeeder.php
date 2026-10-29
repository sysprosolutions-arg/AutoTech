<?php

namespace Database\Seeders;

use App\Models\EstadoReparacion;
use App\Models\TipoReparacion;
use Illuminate\Database\Seeder;

class ReparacionesMasterDataSeeder extends Seeder
{
    /**
     * Seed repair types and lifecycle states used by Sprint 1.
     */
    public function run(): void
    {
        collect([
            ['codigo' => 'preventiva', 'nombre' => 'Mantenimiento preventivo', 'descripcion' => 'Service y tareas destinadas a prevenir fallas.'],
            ['codigo' => 'correctiva', 'nombre' => 'Reparación correctiva', 'descripcion' => 'Corrección de una falla diagnosticada.'],
            ['codigo' => 'diagnostico', 'nombre' => 'Diagnóstico', 'descripcion' => 'Evaluación técnica y detección de fallas.'],
            ['codigo' => 'inspeccion', 'nombre' => 'Inspección', 'descripcion' => 'Control técnico sin sustitución obligatoria de componentes.'],
        ])->each(fn (array $tipo): TipoReparacion => TipoReparacion::query()->firstOrCreate(
            ['codigo' => $tipo['codigo']],
            [...$tipo, 'activo' => true],
        ));

        collect([
            ['codigo' => 'en-proceso', 'nombre' => 'En proceso'],
            ['codigo' => 'finalizada', 'nombre' => 'Finalizada'],
            ['codigo' => 'cancelada', 'nombre' => 'Cancelada'],
        ])->each(fn (array $estado): EstadoReparacion => EstadoReparacion::query()->firstOrCreate(
            ['codigo' => $estado['codigo']],
            $estado,
        ));
    }
}
