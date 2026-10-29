<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePresupuestoDecisionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, ValidationRule|array<mixed>|string> */
    public function rules(): array
    {
        return [
            'decision' => ['required', Rule::in(['aprobado', 'rechazado'])],
            'fecha_decision' => ['required', 'date_format:Y-m-d', 'before_or_equal:today'],
            'medio_confirmacion' => ['required', Rule::in(['presencial', 'telefono', 'whatsapp', 'email'])],
            'observaciones_decision' => ['nullable', 'string', 'max:1000'],
        ];
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return [
            'decision.required' => 'Indicá si el cliente aprobó o rechazó el presupuesto.',
            'decision.in' => 'La decisión informada no es válida.',
            'fecha_decision.required' => 'Ingresá la fecha en que el cliente comunicó su decisión.',
            'fecha_decision.date_format' => 'La fecha de decisión no es válida.',
            'fecha_decision.before_or_equal' => 'La fecha de decisión no puede ser futura.',
            'medio_confirmacion.required' => 'Seleccioná el medio por el que el cliente confirmó su decisión.',
            'medio_confirmacion.in' => 'El medio de confirmación seleccionado no es válido.',
            'observaciones_decision.max' => 'Las observaciones no pueden superar los 1000 caracteres.',
        ];
    }
}
