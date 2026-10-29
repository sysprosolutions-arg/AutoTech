<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['presupuesto_id', 'estado', 'registrado_por', 'fecha_decision', 'medio_confirmacion', 'observaciones'])]
class AprobacionPresupuesto extends Model
{
    use HasFactory;

    protected $table = 'aprobaciones_presupuesto';

    /** @return BelongsTo<Presupuesto, $this> */
    public function presupuesto(): BelongsTo
    {
        return $this->belongsTo(Presupuesto::class);
    }

    /** @return BelongsTo<User, $this> */
    public function registradoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'registrado_por');
    }

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'fecha_decision' => 'datetime',
        ];
    }
}
