<?php

namespace Database\Seeders;

use App\Models\Proveedor;
use App\Models\Repuesto;
use Illuminate\Database\Seeder;

class RepuestosMasterDataSeeder extends Seeder
{
    public function run(): void
    {
        $proveedores = collect([
            [
                'cuit' => '30-70123456-7',
                'razon_social' => 'Autopartes del Plata S.A.',
                'nombre_fantasia' => 'Autopartes del Plata',
                'localidad' => 'Ciudad Autónoma de Buenos Aires',
                'direccion' => 'Av. Warnes 1450',
                'telefono' => '11 4588-1200',
                'email' => 'ventas@autopartesdelplata.example',
            ],
            [
                'cuit' => '30-70987654-3',
                'razon_social' => 'Lubricantes Centro S.R.L.',
                'nombre_fantasia' => 'Lubricantes Centro',
                'localidad' => 'San Martín',
                'direccion' => 'Ruta 8 3250',
                'telefono' => '11 4754-8800',
                'email' => 'pedidos@lubricantescentro.example',
            ],
            [
                'cuit' => '30-71555111-9',
                'razon_social' => 'Electro Auto Sur S.A.',
                'nombre_fantasia' => 'Electro Auto Sur',
                'localidad' => 'Lanús',
                'direccion' => 'Hipólito Yrigoyen 4200',
                'telefono' => '11 4241-3300',
                'email' => 'comercial@electroautosur.example',
            ],
        ])->mapWithKeys(function (array $data): array {
            $proveedor = Proveedor::query()->firstOrCreate(
                ['cuit' => $data['cuit']],
                [...$data, 'activo' => true],
            );

            return [$data['cuit'] => $proveedor];
        });

        collect([
            ['codigo' => 'REP-ACE-001', 'descripcion' => 'Lubricante sintético multigrado para motores nafteros y diésel.', 'proveedor' => '30-70987654-3'],
            ['codigo' => 'REP-FIL-001', 'descripcion' => 'Filtro original compatible con motores Toyota seleccionados.', 'proveedor' => '30-70123456-7'],
            ['codigo' => 'REP-PAS-001', 'descripcion' => 'Juego de pastillas de freno delanteras de material semimetálico.', 'proveedor' => '30-70123456-7'],
            ['codigo' => 'REP-BAT-001', 'descripcion' => 'Batería de 12 voltios y 65 amperios-hora para uso automotor.', 'proveedor' => '30-71555111-9'],
            ['codigo' => 'REP-ACE-002', 'descripcion' => 'Lubricante semisintético para mantenimiento periódico de motores.', 'proveedor' => '30-70987654-3'],
            ['codigo' => 'REP-FIL-002', 'descripcion' => 'Elemento filtrante para el sistema de admisión del motor.', 'proveedor' => '30-70123456-7'],
            ['codigo' => 'REP-FIL-003', 'descripcion' => 'Filtro para retención de partículas del circuito de combustible.', 'proveedor' => '30-70123456-7'],
            ['codigo' => 'REP-FIL-004', 'descripcion' => 'Filtro antipolen para climatización y ventilación del habitáculo.', 'proveedor' => '30-70123456-7'],
            ['codigo' => 'REP-DIS-001', 'descripcion' => 'Disco ventilado para eje delantero de vehículos compatibles.', 'proveedor' => '30-70123456-7'],
            ['codigo' => 'REP-LIQ-001', 'descripcion' => 'Fluido hidráulico DOT 4 para sistemas de freno y embrague.', 'proveedor' => '30-70987654-3'],
            ['codigo' => 'REP-AMO-001', 'descripcion' => 'Amortiguador hidráulico delantero para suspensión independiente.', 'proveedor' => '30-70123456-7'],
            ['codigo' => 'REP-BUJ-001', 'descripcion' => 'Bujía resistiva para sistemas de encendido electrónico.', 'proveedor' => '30-71555111-9'],
            ['codigo' => 'REP-COR-001', 'descripcion' => 'Correa dentada para sincronización de distribución del motor.', 'proveedor' => '30-70123456-7'],
            ['codigo' => 'REP-TEN-001', 'descripcion' => 'Tensor automático para conjunto de distribución.', 'proveedor' => '30-70123456-7'],
            ['codigo' => 'REP-ALT-001', 'descripcion' => 'Alternador de 12 voltios para sistema de carga automotor.', 'proveedor' => '30-71555111-9'],
            ['codigo' => 'REP-ARR-001', 'descripcion' => 'Motor eléctrico de arranque para vehículos livianos.', 'proveedor' => '30-71555111-9'],
            ['codigo' => 'REP-LAM-001', 'descripcion' => 'Lámpara halógena H7 para ópticas delanteras.', 'proveedor' => '30-71555111-9'],
            ['codigo' => 'REP-FUS-001', 'descripcion' => 'Surtido de fusibles planos para circuitos eléctricos automotrices.', 'proveedor' => '30-71555111-9'],
            ['codigo' => 'REP-REF-001', 'descripcion' => 'Refrigerante orgánico concentrado para circuitos presurizados.', 'proveedor' => '30-70987654-3'],
            ['codigo' => 'REP-BOM-001', 'descripcion' => 'Bomba mecánica para circulación del refrigerante del motor.', 'proveedor' => '30-70123456-7'],
            ['codigo' => 'REP-TER-001', 'descripcion' => 'Termostato para regulación térmica del circuito de refrigeración.', 'proveedor' => '30-70123456-7'],
            ['codigo' => 'REP-EMB-001', 'descripcion' => 'Conjunto de placa, disco y rulemán de empuje.', 'proveedor' => '30-70123456-7'],
            ['codigo' => 'REP-ROT-001', 'descripcion' => 'Rótula para articulación de suspensión y tren delantero.', 'proveedor' => '30-70123456-7'],
            ['codigo' => 'REP-EXT-001', 'descripcion' => 'Extremo articulado para el sistema de dirección.', 'proveedor' => '30-70123456-7'],
            ['codigo' => 'REP-ESC-001', 'descripcion' => 'Escobilla aerodinámica para limpieza de parabrisas.', 'proveedor' => '30-70123456-7'],
            ['codigo' => 'REP-SEN-001', 'descripcion' => 'Sensor de oxígeno para control de mezcla y emisiones.', 'proveedor' => '30-71555111-9'],
            ['codigo' => 'REP-BOB-001', 'descripcion' => 'Bobina individual para sistema de encendido electrónico.', 'proveedor' => '30-71555111-9'],
            ['codigo' => 'REP-CAB-001', 'descripcion' => 'Juego de cables resistivos para bujías de encendido.', 'proveedor' => '30-71555111-9'],
        ])->each(function (array $data) use ($proveedores): void {
            $repuesto = Repuesto::query()->where('codigo', $data['codigo'])->first();

            if (! $repuesto instanceof Repuesto) {
                return;
            }

            $changes = [];

            if ($repuesto->descripcion === null) {
                $changes['descripcion'] = $data['descripcion'];
            }

            if ($repuesto->proveedor_id === null) {
                $changes['proveedor_id'] = $proveedores[$data['proveedor']]->id;
            }

            if ($changes !== []) {
                $repuesto->update($changes);
            }
        });
    }
}
