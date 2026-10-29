<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['codigo', 'nombre', 'descripcion', 'especialidad_id', 'precio_base', 'activo'])]
class TipoServicio extends Model
{
    use HasFactory;

    protected $table = 'tipos_servicio';

    /** @return BelongsTo<Especialidad, $this> */
    public function especialidad(): BelongsTo
    {
        return $this->belongsTo(Especialidad::class);
    }

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
            'precio_base' => 'decimal:2',
        ];
    }
}
