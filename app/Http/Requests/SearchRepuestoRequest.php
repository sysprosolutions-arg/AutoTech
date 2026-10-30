<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SearchRepuestoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, ValidationRule|array<mixed>|string> */
    public function rules(): array
    {
        return [
            'buscar' => ['nullable', 'string', 'max:100'],
            'tipo' => ['nullable', Rule::in(['original', 'alternativo'])],
            'estado' => ['nullable', Rule::in(['activo', 'inactivo'])],
        ];
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return [
            'buscar.max' => 'La búsqueda no puede superar los 100 caracteres.',
            'tipo.in' => 'El tipo seleccionado no es válido.',
            'estado.in' => 'El estado seleccionado no es válido.',
        ];
    }
}
