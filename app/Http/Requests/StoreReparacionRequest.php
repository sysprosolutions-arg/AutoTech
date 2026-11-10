<?php

namespace App\Http\Requests;

use App\Models\Repuesto;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class StoreReparacionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, ValidationRule|array<mixed>|string> */
    public function rules(): array
    {
        return [
            'orden_trabajo_id' => ['required', 'integer', Rule::exists('ordenes_trabajo', 'id'), Rule::unique('reparaciones', 'orden_trabajo_id')],
            'tipo_reparacion_id' => ['required', 'integer', Rule::exists('tipos_reparacion', 'id')->where('activo', true)],
            'tiempo_empleado_horas' => ['required', 'numeric', 'min:0.25', 'max:166.67'],
            'trabajos_realizados' => ['required', 'string', 'max:2000'],
            'incidencias' => ['nullable', 'string', 'max:2000'],
            'repuestos' => ['nullable', 'array'],
            'repuestos.*' => ['required', 'array:repuesto_id,cantidad'],
            'repuestos.*.repuesto_id' => [
                'required',
                'integer',
                'distinct:strict',
                Rule::exists('repuestos', 'id'),
            ],
            'repuestos.*.cantidad' => ['required', 'integer', 'min:1', 'max:99'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $timeInHours = $this->input('tiempo_empleado_horas');

        if (is_string($timeInHours)) {
            $this->merge([
                'tiempo_empleado_horas' => str_replace(',', '.', trim($timeInHours)),
            ]);
        }
    }

    /** @return array<int, callable> */
    public function after(): array
    {
        return [
            function (Validator $validator): void {
                $input = $this->input('repuestos', []);

                if (! is_array($input)) {
                    return;
                }

                $repuestos = Repuesto::query()
                    ->whereIn('id', collect($input)->pluck('repuesto_id')->filter())
                    ->get()
                    ->keyBy('id');

                foreach ($input as $index => $item) {
                    if (! is_array($item)) {
                        continue;
                    }

                    $repuesto = $repuestos->get((int) ($item['repuesto_id'] ?? 0));
                    $cantidad = (int) ($item['cantidad'] ?? 0);

                    if (! $repuesto instanceof Repuesto) {
                        continue;
                    }

                    if (! $repuesto->activo) {
                        $validator->errors()->add(
                            "repuestos.{$index}.repuesto_id",
                            "El repuesto {$repuesto->nombre} no se encuentra activo.",
                        );
                    }

                    if ($cantidad > $repuesto->stock) {
                        $validator->errors()->add(
                            "repuestos.{$index}.cantidad",
                            "La cantidad utilizada de {$repuesto->nombre} supera el stock disponible ({$repuesto->stock}).",
                        );
                    }
                }
            },
        ];
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return [
            'orden_trabajo_id.required' => 'Seleccioná una Orden de Trabajo.',
            'orden_trabajo_id.exists' => 'La Orden de Trabajo seleccionada no existe.',
            'orden_trabajo_id.unique' => 'La Orden de Trabajo ya tiene una reparación registrada.',
            'tipo_reparacion_id.required' => 'Seleccioná el tipo de reparación.',
            'tipo_reparacion_id.exists' => 'El tipo de reparación seleccionado no está disponible.',
            'tiempo_empleado_horas.required' => 'Ingresá el tiempo empleado en horas.',
            'tiempo_empleado_horas.numeric' => 'El tiempo empleado debe expresarse en horas.',
            'tiempo_empleado_horas.min' => 'El tiempo empleado mínimo es de 0,25 horas.',
            'tiempo_empleado_horas.max' => 'El tiempo empleado no puede superar las 166,67 horas.',
            'trabajos_realizados.required' => 'Describí los trabajos realizados.',
            'trabajos_realizados.max' => 'La descripción no puede superar los 2000 caracteres.',
            'incidencias.max' => 'Las incidencias no pueden superar los 2000 caracteres.',
            'repuestos.*.repuesto_id.exists' => 'Uno de los repuestos seleccionados no existe.',
            'repuestos.*.cantidad.min' => 'La cantidad utilizada debe ser mayor a cero.',
        ];
    }
}
