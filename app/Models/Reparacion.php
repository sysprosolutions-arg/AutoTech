<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'numero',
    'orden_trabajo_id',
    'vehiculo_id',
    'empleado_id',
    'tipo_reparacion_id',
    'estado_reparacion_id',
    'registrado_por',
    'fecha_inicio',
    'fecha_fin',
    'tiempo_empleado_minutos',
    'trabajos_realizados',
    'incidencias',
])]
class Reparacion extends Model
{
    use HasFactory;

    protected $table = 'reparaciones';

    /** @return BelongsTo<OrdenTrabajo, $this> */
    public function ordenTrabajo(): BelongsTo
    {
        return $this->belongsTo(OrdenTrabajo::class);
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

    /** @return BelongsTo<TipoReparacion, $this> */
    public function tipo(): BelongsTo
    {
        return $this->belongsTo(TipoReparacion::class, 'tipo_reparacion_id');
    }

    /** @return BelongsTo<EstadoReparacion, $this> */
    public function estado(): BelongsTo
    {
        return $this->belongsTo(EstadoReparacion::class, 'estado_reparacion_id');
    }

    /** @return BelongsTo<User, $this> */
    public function registradoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'registrado_por');
    }

    /** @return HasMany<DetalleReparacion, $this> */
    public function detalles(): HasMany
    {
        return $this->hasMany(DetalleReparacion::class);
    }

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'fecha_inicio' => 'datetime',
            'fecha_fin' => 'datetime',
            'tiempo_empleado_minutos' => 'integer',
        ];
    }
}
