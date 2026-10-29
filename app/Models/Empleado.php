<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['legajo', 'nombre', 'apellido', 'especialidad_id', 'estado_empleado_id'])]
class Empleado extends Model
{
    use HasFactory;

    /** @return BelongsTo<Especialidad, $this> */
    public function especialidad(): BelongsTo
    {
        return $this->belongsTo(Especialidad::class);
    }

    /** @return BelongsTo<EstadoEmpleado, $this> */
    public function estado(): BelongsTo
    {
        return $this->belongsTo(EstadoEmpleado::class, 'estado_empleado_id');
    }

    /** @return HasMany<OrdenTrabajo, $this> */
    public function ordenesTrabajo(): HasMany
    {
        return $this->hasMany(OrdenTrabajo::class);
    }
}
