<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['codigo', 'nombre', 'descripcion', 'activo'])]
class TipoReparacion extends Model
{
    use HasFactory;

    protected $table = 'tipos_reparacion';

    /** @return HasMany<Reparacion, $this> */
    public function reparaciones(): HasMany
    {
        return $this->hasMany(Reparacion::class);
    }

    /** @return array<string, string> */
    protected function casts(): array
    {
        return ['activo' => 'boolean'];
    }
}
