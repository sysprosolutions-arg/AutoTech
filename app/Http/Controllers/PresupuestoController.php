<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePresupuestoRequest;
use App\Models\Presupuesto;
use App\Models\Repuesto;
use App\Models\TipoServicio;
use App\Models\Turno;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class PresupuestoController extends Controller
{
    public function create(Turno $turno): View|RedirectResponse
    {
        $turno->load(['cliente', 'vehiculo', 'estado', 'presupuesto']);

        if ($turno->presupuesto instanceof Presupuesto) {
            return redirect()->route('presupuestos.show', $turno->presupuesto);
        }

        return view('presupuestos.create', [
            'repuestos' => Repuesto::query()->where('activo', true)->orderBy('nombre')->get(),
            'servicios' => TipoServicio::query()
                ->where('activo', true)
                ->with('especialidad')
                ->orderBy('especialidad_id')
                ->orderBy('nombre')
                ->get(),
            'turno' => $turno,
        ]);
    }

    public function store(StorePresupuestoRequest $request): RedirectResponse
    {
        $user = $request->user();
        abort_unless($user instanceof User, 401);
        $data = $request->validated();

        $presupuesto = DB::transaction(function () use ($data, $user): Presupuesto {
            $turno = Turno::query()->lockForUpdate()->findOrFail((int) $data['turno_id']);

            if (Presupuesto::query()->where('turno_id', $turno->id)->exists()) {
                throw ValidationException::withMessages([
                    'turno_id' => 'El turno ya tiene un presupuesto generado.',
                ]);
            }

            $servicios = TipoServicio::query()
                ->whereIn('id', collect($data['servicios'])->pluck('id'))
                ->where('activo', true)
                ->lockForUpdate()
                ->get()
                ->keyBy('id');
            $repuestos = Repuesto::query()
                ->whereIn('id', collect($data['repuestos'] ?? [])->pluck('id'))
                ->where('activo', true)
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            $detalles = [];
            $totalCents = 0;

            foreach ($data['servicios'] as $item) {
                $servicio = $servicios->get((int) $item['id']);

                if (! $servicio instanceof TipoServicio) {
                    throw ValidationException::withMessages(['servicios' => 'Uno de los servicios ya no está disponible.']);
                }

                $cantidad = (int) $item['cantidad'];
                $precioCents = $this->priceToCents($servicio->precio_base);
                $subtotalCents = $precioCents * $cantidad;
                $totalCents += $subtotalCents;
                $detalles[] = [
                    'tipo' => 'servicio',
                    'tipo_servicio_id' => $servicio->id,
                    'repuesto_id' => null,
                    'descripcion' => $servicio->nombre,
                    'cantidad' => $cantidad,
                    'precio_unitario' => $this->centsToDecimal($precioCents),
                    'subtotal' => $this->centsToDecimal($subtotalCents),
                ];
            }

            foreach ($data['repuestos'] ?? [] as $item) {
                $repuesto = $repuestos->get((int) $item['id']);

                if (! $repuesto instanceof Repuesto || (int) $item['cantidad'] > $repuesto->stock) {
                    throw ValidationException::withMessages(['repuestos' => 'Uno de los repuestos ya no tiene stock suficiente.']);
                }

                $cantidad = (int) $item['cantidad'];
                $precioCents = $this->priceToCents($repuesto->precio_venta);
                $subtotalCents = $precioCents * $cantidad;
                $totalCents += $subtotalCents;
                $detalles[] = [
                    'tipo' => 'repuesto',
                    'tipo_servicio_id' => null,
                    'repuesto_id' => $repuesto->id,
                    'descripcion' => $repuesto->nombre,
                    'cantidad' => $cantidad,
                    'precio_unitario' => $this->centsToDecimal($precioCents),
                    'subtotal' => $this->centsToDecimal($subtotalCents),
                ];
            }

            $presupuesto = Presupuesto::query()->create([
                'turno_id' => $turno->id,
                'registrado_por' => $user->id,
                'fecha_emision' => now()->toDateString(),
                'valido_hasta' => now()->addDays(15)->toDateString(),
                'total' => $this->centsToDecimal($totalCents),
                'observaciones' => $data['observaciones'] ?? null,
            ]);
            $presupuesto->update(['numero' => sprintf('PRE-%06d', $presupuesto->id)]);
            $presupuesto->detalles()->createMany($detalles);
            $presupuesto->aprobacion()->create(['estado' => 'pendiente']);

            return $presupuesto;
        }, 3);

        return redirect()
            ->route('presupuestos.show', $presupuesto)
            ->with('status', "Presupuesto {$presupuesto->numero} generado correctamente.");
    }

    public function show(Presupuesto $presupuesto): View
    {
        $presupuesto->load([
            'turno.cliente',
            'turno.vehiculo',
            'registradoPor',
            'detalles.tipoServicio.especialidad',
            'detalles.repuesto',
            'aprobacion.registradoPor',
            'ordenTrabajo.empleado.especialidad',
            'ordenTrabajo.estado',
        ]);

        return view('presupuestos.show', [
            'presupuesto' => $presupuesto,
        ]);
    }

    private function priceToCents(string $amount): int
    {
        [$whole, $fraction] = array_pad(explode('.', $amount, 2), 2, '0');

        return ((int) $whole * 100) + (int) substr(str_pad($fraction, 2, '0'), 0, 2);
    }

    private function centsToDecimal(int $amount): string
    {
        return sprintf('%d.%02d', intdiv($amount, 100), $amount % 100);
    }
}
