<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreRepuestoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, ValidationRule|array<mixed>|string> */
    public function rules(): array
    {
        return [
            'codigo' => ['required', 'string', 'max:50', Rule::unique('repuestos', 'codigo')],
            'nombre' => ['required', 'string', 'max:150'],
            'descripcion' => ['required', 'string', 'max:500'],
            'marca' => ['required', 'string', 'max:100'],
            'tipo' => ['required', Rule::in(['original', 'alternativo'])],
            'proveedor_id' => ['required', 'integer', Rule::exists('proveedores', 'id')->where('activo', true)],
            'precio_venta' => ['required', 'numeric', 'min:0', 'max:9999999999.99'],
            'stock' => ['required', 'integer', 'min:0', 'max:1000000'],
        ];
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return [
            'codigo.required' => 'Ingresá el código del repuesto.',
            'codigo.unique' => 'Ya existe un repuesto con ese código.',
            'nombre.required' => 'Ingresá el nombre del repuesto.',
            'descripcion.required' => 'Ingresá una descripción del repuesto.',
            'marca.required' => 'Ingresá la marca del repuesto.',
            'tipo.required' => 'Seleccioná si el repuesto es original o alternativo.',
            'tipo.in' => 'El tipo de repuesto seleccionado no es válido.',
            'proveedor_id.required' => 'Seleccioná un proveedor.',
            'proveedor_id.exists' => 'El proveedor seleccionado no está disponible.',
            'precio_venta.required' => 'Ingresá el precio de venta.',
            'precio_venta.numeric' => 'El precio de venta debe ser numérico.',
            'precio_venta.min' => 'El precio de venta no puede ser negativo.',
            'stock.required' => 'Ingresá el stock inicial.',
            'stock.integer' => 'El stock inicial debe ser un número entero.',
            'stock.min' => 'El stock inicial no puede ser negativo.',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'codigo' => mb_strtoupper(trim((string) $this->input('codigo'))),
            'nombre' => trim((string) $this->input('nombre')),
            'descripcion' => trim((string) $this->input('descripcion')),
            'marca' => trim((string) $this->input('marca')),
        ]);
    }
}
