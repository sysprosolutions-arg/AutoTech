<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateRepuestoStockRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, ValidationRule|array<mixed>|string> */
    public function rules(): array
    {
        return [
            'operacion' => ['required', Rule::in(['entrada', 'salida'])],
            'cantidad' => ['required', 'integer', 'min:1', 'max:1000000'],
            'motivo' => ['required', 'string', 'max:500'],
        ];
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return [
            'operacion.required' => 'Seleccioná el tipo de movimiento.',
            'operacion.in' => 'El tipo de movimiento no es válido.',
            'cantidad.required' => 'Ingresá la cantidad.',
            'cantidad.integer' => 'La cantidad debe ser un número entero.',
            'cantidad.min' => 'La cantidad debe ser mayor a cero.',
            'motivo.required' => 'Ingresá el motivo del movimiento.',
        ];
    }
}
