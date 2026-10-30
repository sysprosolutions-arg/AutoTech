<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class RepuestoMarcaReportRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, ValidationRule|array<mixed>|string> */
    public function rules(): array
    {
        return ['marca' => ['nullable', 'string', 'max:100']];
    }

    protected function prepareForValidation(): void
    {
        $this->merge(['marca' => trim((string) $this->input('marca')) ?: null]);
    }
}
