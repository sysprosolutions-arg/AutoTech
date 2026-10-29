<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['codigo', 'nombre', 'marca', 'original', 'precio_venta', 'stock', 'activo'])]
class Repuesto extends Model
{
    use HasFactory;

    /** @return HasMany<DetallePresupuesto, $this> */
    public function detallesPresupuesto(): HasMany
    {
        return $this->hasMany(DetallePresupuesto::class);
    }

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'activo' => 'boolean',
            'original' => 'boolean',
            'precio_venta' => 'decimal:2',
            'stock' => 'integer',
        ];
    }
}
