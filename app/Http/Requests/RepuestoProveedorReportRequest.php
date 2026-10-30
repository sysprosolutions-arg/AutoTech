<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RepuestoProveedorReportRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, ValidationRule|array<mixed>|string> */
    public function rules(): array
    {
        return [
            'proveedor_id' => [
                'nullable',
                'integer',
                Rule::exists('proveedores', 'id')->where('activo', true),
            ],
        ];
    }
}
