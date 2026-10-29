<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['codigo', 'nombre'])]
class EstadoEmpleado extends Model
{
    use HasFactory;

    protected $table = 'estados_empleado';

    /** @return HasMany<Empleado, $this> */
    public function empleados(): HasMany
    {
        return $this->hasMany(Empleado::class);
    }
}
