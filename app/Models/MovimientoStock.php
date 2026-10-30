<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['repuesto_id', 'registrado_por', 'operacion', 'cantidad', 'stock_anterior', 'stock_resultante', 'motivo'])]
class MovimientoStock extends Model
{
    protected $table = 'movimientos_stock';

    /** @return BelongsTo<Repuesto, $this> */
    public function repuesto(): BelongsTo
    {
        return $this->belongsTo(Repuesto::class);
    }

    /** @return BelongsTo<User, $this> */
    public function registradoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'registrado_por');
    }

    /** @return array<string, string> */
    protected function casts(): array
    {
        return ['cantidad' => 'integer', 'stock_anterior' => 'integer', 'stock_resultante' => 'integer'];
    }
}
