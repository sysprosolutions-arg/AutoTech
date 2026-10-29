<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['reparacion_id', 'tipo', 'tipo_servicio_id', 'repuesto_id', 'descripcion', 'cantidad'])]
class DetalleReparacion extends Model
{
    use HasFactory;

    protected $table = 'detalle_reparaciones';

    /** @return BelongsTo<Reparacion, $this> */
    public function reparacion(): BelongsTo
    {
        return $this->belongsTo(Reparacion::class);
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
        return ['cantidad' => 'integer'];
    }
}
