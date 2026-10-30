<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'cuit',
    'razon_social',
    'nombre_fantasia',
    'localidad',
    'direccion',
    'telefono',
    'email',
    'activo',
])]
class Proveedor extends Model
{
    use HasFactory;

    protected $table = 'proveedores';

    /** @return HasMany<Repuesto, $this> */
    public function repuestos(): HasMany
    {
        return $this->hasMany(Repuesto::class);
    }

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'activo' => 'boolean',
        ];
    }
}
