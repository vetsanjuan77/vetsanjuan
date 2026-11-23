@extends('layouts.navfoot')
@section('title')
    <title>Pago</title>
@endsection
@section('content')
    <div class="container py-4">
        @php
            $tipoItem = (!empty($carrito) && isset($carrito[0]['tipo'])) ? $carrito[0]['tipo'] : '';
            $esServicio = !empty($carrito) && isset($carrito[0]['tipo']) && substr($tipoItem, 0, 5) === 'cita_';
        @endphp

        <h2 class="mb-4">{{ $esServicio ? 'Resumen del pago de servicio' : 'Resumen de tu compra' }}</h2>

        @if($esServicio)
            @php $totalServicio = $carrito[0]['precio']; @endphp
            <div class="card mb-4">
                <div class="card-body">
                    <table class="table mb-0">
                        <thead>
                            <tr>
                                <th>Servicio</th>
                                <th>Detalle</th>
                                <th>Precio</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>{{ $carrito[0]['servicio'] }}</td>
                                <td>{{ $carrito[0]['detalle'] }}</td>
                                <td><strong>$ {{ number_format($carrito[0]['precio'], 0, ',', '.') }} COP</strong></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="alert alert-warning">
                <strong>Costo de agendamiento:</strong> $ {{ number_format($totalServicio, 0, ',', '.') }} COP<br>
                <small>Es un consumible no reembolsable si no llega a la cita.</small>
            </div>
        @else
            <div class="card mb-4">
                <div class="card-body">
                    <table class="table table-bordered mb-0">
                        <thead>
                            <tr>
                                <th>Producto</th>
                                <th>Cantidad</th>
                                <th>Precio unitario</th>
                                <th>Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $total = 0; @endphp
                            @foreach ($carrito as $item)
                                @php
                                    $subtotal = $item['cantidad'] * $item['precio'];
                                    $total += $subtotal;
                                @endphp
                                <tr>
                                    <td>{{ $item['nombre'] }}</td>
                                    <td>{{ $item['cantidad'] }}</td>
                                    <td>$ {{ number_format($item['precio'], 0, ',', '.') }} COP</td>
                                    <td><strong>$ {{ number_format($subtotal, 0, ',', '.') }} COP</strong></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <h4 class="mt-2">Total a pagar: <strong>$ {{ number_format($total, 0, ',', '.') }} COP</strong></h4>
        @endif

        <div class="card mt-4">
            <div class="card-body">
                <form method="POST" action="{{ route('pago.confirmar') }}">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="nombre" class="form-label">Nombre</label>
                            <input type="text" id="nombre" name="nombre" class="form-control" value="{{ $prefill['nombre'] ?? '' }}" required>
                        </div>

                        @unless($esServicio)
                        <div class="col-md-6">
                            <label for="departamento" class="form-label">Departamento</label>
                            <select id="departamento" name="departamento_id" class="form-select" required>
                                <option value="">Seleccione departamento</option>
                                @foreach($departamentos as $d)
                                    <option value="{{ $d->id }}" {{ old('departamento_id') == $d->id ? 'selected' : '' }}>{{ $d->nombre }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label for="ciudad" class="form-label">Ciudad/Municipio</label>
                            <select id="ciudad" name="ciudad_id" class="form-select" required>
                                <option value="">Seleccione ciudad</option>
                            </select>
                        </div>

                        <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            const deptSelect = document.getElementById('departamento');
                            const ciudadSelect = document.getElementById('ciudad');
                            function loadCiudades(departamentoId, selectedCiudadId) {
                                ciudadSelect.innerHTML = '<option value="">Seleccione ciudad</option>';
                                if (!departamentoId) return;
                                const url = '{{ route('ciudades.by.departamento', ['departamentoId' => 'DEPT_ID']) }}'.replace('DEPT_ID', departamentoId);
                                fetch(url)
                                  .then(r => r.json())
                                  .then(data => {
                                      data.forEach(c => {
                                          const opt = document.createElement('option');
                                          opt.value = c.id;
                                          opt.textContent = c.nombre;
                                          if (selectedCiudadId && Number(selectedCiudadId) === Number(c.id)) opt.selected = true;
                                          ciudadSelect.appendChild(opt);
                                      });
                                  })
                                  .catch(() => {});
                            }
                            deptSelect.addEventListener('change', () => loadCiudades(deptSelect.value, null));
                            const preDept = '{{ old('departamento_id') }}';
                            const preCiudad = '{{ old('ciudad_id') }}';
                            if (preDept) {
                                loadCiudades(preDept, preCiudad);
                            }
                        });
                        </script>

                        <div class="col-md-6">
                            <label for="codigo_postal" class="form-label">Código Postal</label>
                            <input type="text" id="codigo_postal" name="codigo_postal" class="form-control" value="{{ $prefill['codigo_postal'] ?? '' }}" placeholder="Ej: 680001" required>
                        </div>

                        <div class="col-md-6">
                            <label for="tipo_calle" class="form-label">Tipo de Calle</label>
                            <select id="tipo_calle" name="tipo_calle" class="form-select" required>
                                <option value="">Seleccione tipo</option>
                                <option value="Calle" {{ ($prefill['tipo_calle'] ?? '') == 'Calle' ? 'selected' : '' }}>Calle</option>
                                <option value="Carrera" {{ ($prefill['tipo_calle'] ?? '') == 'Carrera' ? 'selected' : '' }}>Carrera</option>
                                <option value="Avenida" {{ ($prefill['tipo_calle'] ?? '') == 'Avenida' ? 'selected' : '' }}>Avenida</option>
                                <option value="Diagonal" {{ ($prefill['tipo_calle'] ?? '') == 'Diagonal' ? 'selected' : '' }}>Diagonal</option>
                                <option value="Transversal" {{ ($prefill['tipo_calle'] ?? '') == 'Transversal' ? 'selected' : '' }}>Transversal</option>
                                <option value="Circular" {{ ($prefill['tipo_calle'] ?? '') == 'Circular' ? 'selected' : '' }}>Circular</option>
                                <option value="Autopista" {{ ($prefill['tipo_calle'] ?? '') == 'Autopista' ? 'selected' : '' }}>Autopista</option>
                                <option value="Vía" {{ ($prefill['tipo_calle'] ?? '') == 'Vía' ? 'selected' : '' }}>Vía</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label for="direccion" class="form-label">Dirección</label>
                            <input type="text" id="direccion" name="direccion" class="form-control" value="{{ $prefill['direccion'] ?? '' }}" placeholder="Ej: 22 #77A-15" required>
                        </div>

                        <div class="col-md-6">
                            <label for="referencias" class="form-label">Referencias</label>
                            <input type="text" id="referencias" name="referencias" class="form-control" value="{{ $prefill['referencias'] ?? '' }}" placeholder="Ej: Casa 121, Apto 323, Edificio Torres">
                        </div>
                        @endunless

                        <div class="col-md-6">
                            <label for="telefono" class="form-label">Teléfono</label>
                            <input type="text" id="telefono" name="telefono" class="form-control" value="{{ $prefill['telefono'] ?? '' }}" required>
                        </div>

                        <div class="col-md-6">
                            <label for="metodo_id" class="form-label">Método de pago</label>
                            <select id="metodo_id" name="metodo_id" class="form-select" required>
                                @foreach($metodos as $m)
                                    <option value="{{ $m->id }}">{{ $m->nombre ?? $m->descripcion }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="mt-3 d-flex justify-content-end">
                        <button type="submit" class="btn btn-success btn-lg">Confirmar y Pagar</button>
                    </div>
                </form>
            </div>
        </div>

    </div>
@endsection
