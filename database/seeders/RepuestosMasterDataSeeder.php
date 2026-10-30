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
