<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['codigo', 'nombre'])]
class Especialidad extends Model
{
    use HasFactory;

    protected $table = 'especialidades';

    /** @return HasMany<Empleado, $this> */
    public function empleados(): HasMany
    {
        return $this->hasMany(Empleado::class);
    }

    /** @return HasMany<TipoServicio, $this> */
    public function tiposServicio(): HasMany
    {
        return $this->hasMany(TipoServicio::class);
    }
}
