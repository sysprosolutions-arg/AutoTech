<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'cliente_id',
    'vehiculo_id',
    'estado_turno_id',
    'registrado_por',
    'fecha',
    'hora',
    'motivo',
    'observaciones',
])]
class Turno extends Model
{
    use HasFactory;

    /**
     * Get the client associated with the appointment.
     *
     * @return BelongsTo<Cliente, $this>
     */
    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class);
    }

    /**
     * Get the vehicle associated with the appointment.
     *
     * @return BelongsTo<Vehiculo, $this>
     */
    public function vehiculo(): BelongsTo
    {
        return $this->belongsTo(Vehiculo::class);
    }

    /**
     * Get the current state of the appointment.
     *
     * @return BelongsTo<EstadoTurno, $this>
     */
    public function estado(): BelongsTo
    {
        return $this->belongsTo(EstadoTurno::class, 'estado_turno_id');
    }

    /**
     * Get the user who registered the appointment.
     *
     * @return BelongsTo<User, $this>
     */
    public function registradoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'registrado_por');
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'fecha' => 'date',
        ];
    }
}
