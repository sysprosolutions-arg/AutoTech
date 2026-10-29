<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['codigo', 'nombre'])]
class EstadoReparacion extends Model
{
    use HasFactory;

    protected $table = 'estados_reparacion';

    /** @return HasMany<Reparacion, $this> */
    public function reparaciones(): HasMany
    {
        return $this->hasMany(Reparacion::class);
    }
}
