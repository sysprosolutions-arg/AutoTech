<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Validation\Rule;

class UpdateRepuestoRequest extends StoreRepuestoRequest
{
    /** @return array<string, ValidationRule|array<mixed>|string> */
    public function rules(): array
    {
        $rules = parent::rules();
        $rules['codigo'] = [
            'required',
            'string',
            'max:50',
            Rule::unique('repuestos', 'codigo')->ignore($this->route('repuesto')),
        ];

        return $rules;
    }
}
