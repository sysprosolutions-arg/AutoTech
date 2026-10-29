<?php

namespace App\Http\Requests;

use App\Models\Repuesto;
use App\Models\TipoServicio;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class StorePresupuestoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, ValidationRule|array<mixed>|string> */
    public function rules(): array
    {
        return [
            'turno_id' => ['required', 'integer', Rule::exists('turnos', 'id'), Rule::unique('presupuestos', 'turno_id')],
            'servicios' => ['required', 'array', 'min:1'],
            'servicios.*' => ['required', 'array:id,cantidad'],
            'servicios.*.id' => ['required', 'integer', 'distinct:strict', Rule::exists('tipos_servicio', 'id')->where('activo', true)],
            'servicios.*.cantidad' => ['required', 'integer', 'min:1', 'max:99'],
            'repuestos' => ['nullable', 'array'],
            'repuestos.*' => ['required', 'array:id,cantidad'],
            'repuestos.*.id' => ['required', 'integer', 'distinct:strict', Rule::exists('repuestos', 'id')->where('activo', true)],
            'repuestos.*.cantidad' => ['required', 'integer', 'min:1', 'max:99'],
            'observaciones' => ['nullable', 'string', 'max:1000'],
        ];
    }

    /** @return array<int, callable> */
    public function after(): array
    {
        return [
            function (Validator $validator): void {
                $servicioInput = $this->input('servicios', []);
                $repuestoInput = $this->input('repuestos', []);

                if (! is_array($servicioInput)) {
                    return;
                }

                $repuestoInput = is_array($repuestoInput) ? $repuestoInput : [];

                $servicioIds = collect($servicioInput)
                    ->pluck('id')
                    ->filter()
                    ->all();

                if ($servicioIds !== []) {
                    $especialidades = TipoServicio::query()
                        ->whereIn('id', $servicioIds)
                        ->pluck('especialidad_id')
                        ->unique();

                    if ($especialidades->count() > 1) {
                        $validator->errors()->add(
                            'servicios',
                            'Seleccioná servicios de una misma especialidad para asignar un mecánico compatible.',
                        );
                    }
                }

                $repuestos = Repuesto::query()
                    ->whereIn('id', collect($repuestoInput)->pluck('id')->filter())
                    ->get()
                    ->keyBy('id');

                foreach ($repuestoInput as $index => $item) {
                    if (! is_array($item)) {
                        continue;
                    }

                    $repuesto = $repuestos->get((int) ($item['id'] ?? 0));
                    $cantidad = (int) ($item['cantidad'] ?? 0);

                    if ($repuesto instanceof Repuesto && $cantidad > $repuesto->stock) {
                        $validator->errors()->add(
                            "repuestos.{$index}.cantidad",
                            "La cantidad de {$repuesto->nombre} supera el stock disponible ({$repuesto->stock}).",
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
            'turno_id.exists' => 'El turno seleccionado no existe.',
            'turno_id.unique' => 'El turno ya tiene un presupuesto generado.',
            'servicios.required' => 'Seleccioná al menos un servicio.',
            'servicios.min' => 'Seleccioná al menos un servicio.',
            'servicios.*.id.exists' => 'Uno de los servicios seleccionados no está disponible.',
            'servicios.*.cantidad.min' => 'La cantidad de cada servicio debe ser mayor a cero.',
            'repuestos.*.id.exists' => 'Uno de los repuestos seleccionados no está disponible.',
            'repuestos.*.cantidad.min' => 'La cantidad de cada repuesto debe ser mayor a cero.',
            'observaciones.max' => 'Las observaciones no pueden superar los 1000 caracteres.',
        ];
    }
}
