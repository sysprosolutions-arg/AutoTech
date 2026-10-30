<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Database\Query\Builder;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTurnoRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'cliente_id' => ['required', 'integer', Rule::exists('clientes', 'id')->where('activo', true)],
            'vehiculo_id' => [
                'required',
                'integer',
                Rule::exists('vehiculos', 'id')->where(
                    fn (Builder $query): Builder => $query
                        ->where('cliente_id', $this->integer('cliente_id'))
                        ->where('activo', true),
                ),
            ],
            'fecha' => ['required', 'date_format:Y-m-d', 'after_or_equal:today'],
            'hora' => ['required', 'date_format:H:i', 'after_or_equal:08:00', 'before_or_equal:17:00'],
            'motivo' => ['required', 'string', 'max:500'],
            'observaciones' => ['nullable', 'string', 'max:1000'],
        ];
    }

    /**
     * Get custom validation messages.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'cliente_id.required' => 'Seleccioná un cliente.',
            'cliente_id.exists' => 'El cliente seleccionado no está disponible.',
            'vehiculo_id.required' => 'Seleccioná un vehículo.',
            'vehiculo_id.exists' => 'El vehículo no pertenece al cliente seleccionado o no está disponible.',
            'fecha.required' => 'Ingresá la fecha del turno.',
            'fecha.date_format' => 'La fecha ingresada no es válida.',
            'fecha.after_or_equal' => 'La fecha del turno no puede ser anterior a hoy.',
            'hora.required' => 'Ingresá la hora del turno.',
            'hora.date_format' => 'La hora ingresada no es válida.',
            'hora.after_or_equal' => 'El horario de atención comienza a las 08:00.',
            'hora.before_or_equal' => 'El horario de atención finaliza a las 17:00.',
            'motivo.required' => 'Describí el motivo del turno.',
            'motivo.max' => 'El motivo no puede superar los 500 caracteres.',
            'observaciones.max' => 'Las observaciones no pueden superar los 1000 caracteres.',
        ];
    }
}
