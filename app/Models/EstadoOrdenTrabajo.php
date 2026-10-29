<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['codigo', 'nombre'])]
class EstadoOrdenTrabajo extends Model
{
    use HasFactory;

    protected $table = 'estados_orden_trabajo';

    /** @return HasMany<OrdenTrabajo, $this> */
    public function ordenesTrabajo(): HasMany
    {
        return $this->hasMany(OrdenTrabajo::class);
    }
}
