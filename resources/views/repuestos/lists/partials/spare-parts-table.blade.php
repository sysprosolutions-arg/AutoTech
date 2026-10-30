@if ($repuestos->isEmpty())
    <section class="content-card empty-state">
        <i class="bi {{ $emptyIcon }}" aria-hidden="true"></i>
        <h2>{{ $emptyTitle }}</h2>
        <p>{{ $emptyDescription }}</p>
    </section>
@else
    <section class="content-card p-0 overflow-hidden" aria-label="{{ $ariaLabel }}">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead>
                    <tr>
                        <th>Código</th>
                        <th>Repuesto</th>
                        <th>Marca</th>
                        <th>Proveedor</th>
                        <th class="text-end">Precio</th>
                        <th class="text-center">Stock</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($repuestos as $repuesto)
                        <tr>
                            <td><strong>{{ $repuesto->codigo }}</strong></td>
                            <td>{{ $repuesto->nombre }}</td>
                            <td>{{ $repuesto->marca }}</td>
                            <td>{{ $repuesto->proveedor?->razon_social ?? 'Sin proveedor asignado' }}</td>
                            <td class="text-end">${{ number_format((float) $repuesto->precio_venta, 2, ',', '.') }}</td>
                            <td class="text-center">{{ $repuesto->stock }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </section>
@endif
