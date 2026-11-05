<?php

namespace App\Http\Requests;

use App\Models\DetalleOrdenTrabajo;
use App\Models\Empleado;
use App\Models\Reparacion;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Database\Query\Builder;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class UpdateReparacionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, ValidationRule|array<mixed>|string> */
    public function rules(): array
    {
        $repair = $this->route('reparacion');
        $workOrderId = $repair instanceof Reparacion ? $repair->orden_trabajo_id : 0;

        return [
            'empleado_id' => ['required', 'integer', Rule::exists('empleados', 'id')],
            'tipo_reparacion_id' => ['required', 'integer', Rule::exists('tipos_reparacion', 'id')->where('activo', true)],
            'tiempo_empleado_horas' => ['required', 'numeric', 'min:0.25', 'max:166.67'],
            'trabajos_realizados' => ['required', 'string', 'max:2000'],
            'incidencias' => ['nullable', 'string', 'max:2000'],
            'servicios' => ['required', 'array', 'min:1'],
            'servicios.*' => [
                'required',
                'integer',
                'distinct:strict',
                Rule::exists('detalle_ordenes_trabajo', 'id')->where(
                    fn (Builder $query): Builder => $query
                        ->where('orden_trabajo_id', $workOrderId)
                        ->whereNotNull('tipo_servicio_id'),
                ),
            ],
            'repuestos' => ['nullable', 'array'],
            'repuestos.*' => ['required', 'array:detalle_id,cantidad'],
            'repuestos.*.detalle_id' => [
                'required',
                'integer',
                'distinct:strict',
                Rule::exists('detalle_ordenes_trabajo', 'id')->where(
                    fn (Builder $query): Builder => $query
                        ->where('orden_trabajo_id', $workOrderId)
                        ->whereNotNull('repuesto_id'),
                ),
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
                $repair = $this->route('reparacion');

                if (! $repair instanceof Reparacion) {
                    return;
                }

                if (! $repair->canBeModified()) {
                    $validator->errors()->add('estado', 'La reparación no puede modificarse porque está cancelada.');

                    return;
                }

                $employee = Empleado::query()->with('estado')->find($this->integer('empleado_id'));

                if ($employee instanceof Empleado && $employee->estado->codigo !== 'activo') {
                    $validator->errors()->add('empleado_id', 'El mecánico seleccionado no se encuentra activo.');
                }

                $serviceDetails = DetalleOrdenTrabajo::query()
                    ->with('tipoServicio')
                    ->whereIn('id', collect($this->input('servicios', []))->filter())
                    ->get();

                if ($employee instanceof Empleado && $serviceDetails->contains(
                    fn (DetalleOrdenTrabajo $detail): bool => $detail->tipoServicio?->especialidad_id !== $employee->especialidad_id,
                )) {
                    $validator->errors()->add('empleado_id', 'La especialidad del mecánico no es compatible con los servicios seleccionados.');
                }

                $inputParts = $this->input('repuestos', []);

                if (! is_array($inputParts)) {
                    return;
                }

                $currentQuantities = $repair->detalles()
                    ->where('tipo', 'repuesto')
                    ->pluck('cantidad', 'repuesto_id');
                $partDetails = DetalleOrdenTrabajo::query()
                    ->with('repuesto')
                    ->whereIn('id', collect($inputParts)->pluck('detalle_id')->filter())
                    ->get()
                    ->keyBy('id');

                foreach ($inputParts as $index => $item) {
                    if (! is_array($item)) {
                        continue;
                    }

                    $detail = $partDetails->get((int) ($item['detalle_id'] ?? 0));
                    $quantity = (int) ($item['cantidad'] ?? 0);

                    if (! $detail instanceof DetalleOrdenTrabajo || ! $detail->repuesto) {
                        continue;
                    }

                    if ($quantity > $detail->cantidad) {
                        $validator->errors()->add(
                            "repuestos.{$index}.cantidad",
                            "La cantidad utilizada de {$detail->descripcion} supera la autorizada en la OT ({$detail->cantidad}).",
                        );
                    }

                    $availableStock = $detail->repuesto->stock + (int) ($currentQuantities[$detail->repuesto_id] ?? 0);

                    if ($quantity > $availableStock) {
                        $validator->errors()->add(
                            "repuestos.{$index}.cantidad",
                            "La cantidad utilizada de {$detail->descripcion} supera el stock disponible ({$availableStock}).",
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
            'empleado_id.required' => 'Seleccioná el mecánico asignado.',
            'empleado_id.exists' => 'El mecánico seleccionado no existe.',
            'tipo_reparacion_id.required' => 'Seleccioná el tipo de reparación.',
            'tipo_reparacion_id.exists' => 'El tipo de reparación seleccionado no está disponible.',
            'tiempo_empleado_horas.required' => 'Ingresá el tiempo empleado en horas.',
            'tiempo_empleado_horas.numeric' => 'El tiempo empleado debe expresarse en horas.',
            'tiempo_empleado_horas.min' => 'El tiempo empleado mínimo es de 0,25 horas.',
            'tiempo_empleado_horas.max' => 'El tiempo empleado no puede superar las 166,67 horas.',
            'trabajos_realizados.required' => 'Describí los trabajos realizados.',
            'trabajos_realizados.max' => 'La descripción no puede superar los 2000 caracteres.',
            'incidencias.max' => 'Las incidencias no pueden superar los 2000 caracteres.',
            'servicios.required' => 'Seleccioná al menos un servicio realizado.',
            'servicios.min' => 'Seleccioná al menos un servicio realizado.',
            'servicios.*.exists' => 'Uno de los servicios no pertenece a la Orden de Trabajo.',
            'repuestos.*.detalle_id.exists' => 'Uno de los repuestos no pertenece a la Orden de Trabajo.',
            'repuestos.*.cantidad.min' => 'La cantidad utilizada debe ser mayor a cero.',
        ];
    }
}
