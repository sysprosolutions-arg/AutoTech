<?php

namespace App\Http\Requests;

use App\Models\DetalleOrdenTrabajo;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Database\Query\Builder;
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
            'tiempo_empleado_horas' => ['required', 'numeric', 'min:0.01', 'max:166.67'],
            'trabajos_realizados' => ['required', 'string', 'max:2000'],
            'incidencias' => ['nullable', 'string', 'max:2000'],
            'repuestos' => ['nullable', 'array'],
            'repuestos.*' => ['required', 'array:detalle_id,cantidad'],
            'repuestos.*.detalle_id' => [
                'required',
                'integer',
                'distinct:strict',
                Rule::exists('detalle_ordenes_trabajo', 'id')->where(
                    fn (Builder $query): Builder => $query
                        ->where('orden_trabajo_id', $this->integer('orden_trabajo_id'))
                        ->whereNotNull('repuesto_id'),
                ),
            ],
            'repuestos.*.cantidad' => ['required', 'integer', 'min:1', 'max:99'],
        ];
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

                $detalles = DetalleOrdenTrabajo::query()
                    ->with('repuesto')
                    ->whereIn('id', collect($input)->pluck('detalle_id')->filter())
                    ->get()
                    ->keyBy('id');

                foreach ($input as $index => $item) {
                    if (! is_array($item)) {
                        continue;
                    }

                    $detalle = $detalles->get((int) ($item['detalle_id'] ?? 0));
                    $cantidad = (int) ($item['cantidad'] ?? 0);

                    if (! $detalle instanceof DetalleOrdenTrabajo || ! $detalle->repuesto) {
                        continue;
                    }

                    if ($cantidad > $detalle->cantidad) {
                        $validator->errors()->add(
                            "repuestos.{$index}.cantidad",
                            "La cantidad utilizada de {$detalle->descripcion} supera la autorizada en la OT ({$detalle->cantidad}).",
                        );
                    }

                    if ($cantidad > $detalle->repuesto->stock) {
                        $validator->errors()->add(
                            "repuestos.{$index}.cantidad",
                            "La cantidad utilizada de {$detalle->descripcion} supera el stock disponible ({$detalle->repuesto->stock}).",
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
            'tiempo_empleado_horas.min' => 'El tiempo empleado debe ser mayor a cero.',
            'tiempo_empleado_horas.max' => 'El tiempo empleado no puede superar las 166,67 horas.',
            'trabajos_realizados.required' => 'Describí los trabajos realizados.',
            'trabajos_realizados.max' => 'La descripción no puede superar los 2000 caracteres.',
            'incidencias.max' => 'Las incidencias no pueden superar los 2000 caracteres.',
            'repuestos.*.detalle_id.exists' => 'Uno de los repuestos no pertenece a la Orden de Trabajo.',
            'repuestos.*.cantidad.min' => 'La cantidad utilizada debe ser mayor a cero.',
        ];
    }
}
