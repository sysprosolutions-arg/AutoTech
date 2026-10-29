<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

#[Fillable(['numero', 'turno_id', 'registrado_por', 'fecha_emision', 'valido_hasta', 'total', 'observaciones'])]
class Presupuesto extends Model
{
    use HasFactory;

    /** @return BelongsTo<Turno, $this> */
    public function turno(): BelongsTo
    {
        return $this->belongsTo(Turno::class);
    }

    /** @return BelongsTo<User, $this> */
    public function registradoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'registrado_por');
    }

    /** @return HasMany<DetallePresupuesto, $this> */
    public function detalles(): HasMany
    {
        return $this->hasMany(DetallePresupuesto::class);
    }

    /** @return HasOne<AprobacionPresupuesto, $this> */
    public function aprobacion(): HasOne
    {
        return $this->hasOne(AprobacionPresupuesto::class);
    }

    /** @return HasOne<OrdenTrabajo, $this> */
    public function ordenTrabajo(): HasOne
    {
        return $this->hasOne(OrdenTrabajo::class);
    }

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'fecha_emision' => 'date',
            'valido_hasta' => 'date',
            'total' => 'decimal:2',
        ];
    }
}
