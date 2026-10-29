<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['orden_trabajo_id', 'tipo_servicio_id', 'repuesto_id', 'descripcion', 'cantidad', 'precio_referencia'])]
class DetalleOrdenTrabajo extends Model
{
    use HasFactory;

    protected $table = 'detalle_ordenes_trabajo';

    /** @return BelongsTo<OrdenTrabajo, $this> */
    public function ordenTrabajo(): BelongsTo
    {
        return $this->belongsTo(OrdenTrabajo::class);
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
            'precio_referencia' => 'decimal:2',
        ];
    }
}
