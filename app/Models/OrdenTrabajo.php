<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

#[Fillable(['numero', 'presupuesto_id', 'turno_id', 'vehiculo_id', 'empleado_id', 'estado_orden_trabajo_id', 'emitida_por', 'fecha_emision', 'instrucciones'])]
class OrdenTrabajo extends Model
{
    use HasFactory;

    protected $table = 'ordenes_trabajo';

    /** @return BelongsTo<Presupuesto, $this> */
    public function presupuesto(): BelongsTo
    {
        return $this->belongsTo(Presupuesto::class);
    }

    /** @return BelongsTo<Turno, $this> */
    public function turno(): BelongsTo
    {
        return $this->belongsTo(Turno::class);
    }

    /** @return BelongsTo<Vehiculo, $this> */
    public function vehiculo(): BelongsTo
    {
        return $this->belongsTo(Vehiculo::class);
    }

    /** @return BelongsTo<Empleado, $this> */
    public function empleado(): BelongsTo
    {
        return $this->belongsTo(Empleado::class);
    }

    /** @return BelongsTo<EstadoOrdenTrabajo, $this> */
    public function estado(): BelongsTo
    {
        return $this->belongsTo(EstadoOrdenTrabajo::class, 'estado_orden_trabajo_id');
    }

    /** @return BelongsTo<User, $this> */
    public function emitidaPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'emitida_por');
    }

    /** @return HasMany<DetalleOrdenTrabajo, $this> */
    public function detalles(): HasMany
    {
        return $this->hasMany(DetalleOrdenTrabajo::class);
    }

    /** @return HasOne<Reparacion, $this> */
    public function reparacion(): HasOne
    {
        return $this->hasOne(Reparacion::class);
    }

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'fecha_emision' => 'datetime',
        ];
    }
}
