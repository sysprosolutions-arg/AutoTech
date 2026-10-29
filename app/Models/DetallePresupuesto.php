<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['presupuesto_id', 'tipo', 'tipo_servicio_id', 'repuesto_id', 'descripcion', 'cantidad', 'precio_unitario', 'subtotal'])]
class DetallePresupuesto extends Model
{
    use HasFactory;

    /** @return BelongsTo<Presupuesto, $this> */
    public function presupuesto(): BelongsTo
    {
        return $this->belongsTo(Presupuesto::class);
    }

    /** @return BelongsTo<TipoServicio, $this> */
    public function tipoServicio(): BelongsTo
    {
        return $this->belongsTo(TipoServicio::class);
    }

    /** @return BelongsTo<Repuesto, $this> */
    public function repuesto(): BelongsTo
    {
        return $this->belongsTo(Repuesto::class);
    }

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'cantidad' => 'integer',
            'precio_unitario' => 'decimal:2',
            'subtotal' => 'decimal:2',
        ];
    }
}
