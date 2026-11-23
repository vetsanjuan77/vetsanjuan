<div class="space-y-4">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 items-start">
        <div>
            <h3 class="text-lg font-semibold">Vista previa del producto</h3>
            @if($imagenUrl)
                <img src="{{ $imagenUrl }}" alt="Imagen del producto" class="rounded-lg border w-full max-h-64 object-contain" />
            @else
                <div class="rounded-lg border p-6 text-sm text-gray-600">Sin imagen seleccionada aún. Sube o edita una imagen para ver la previsualización.</div>
            @endif
            <div class="mt-3">
                <div class="text-xl font-bold">{{ $nombre }}</div>
                <div class="text-gray-700">{{ $descripcion }}</div>
                @if(!is_null($precio))
                    <div class="mt-2 text-green-700 font-semibold">Precio: ${{ number_format($precio, 0, ',', '.') }}</div>
                @endif
            </div>
        </div>
        <div>
            <h3 class="text-lg font-semibold">Presentaciones</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                @forelse($presentaciones as $p)
                    <div class="rounded-lg border p-3">
                        <div class="font-medium">{{ $p['nombre'] }}</div>
                        @if(!is_null($p['precio']))
                            <div class="text-sm text-gray-700">Precio: ${{ number_format($p['precio'], 0, ',', '.') }}</div>
                        @endif
                        @if(!empty($p['imagenUrl']))
                            <img src="{{ $p['imagenUrl'] }}" alt="Imagen de presentación" class="mt-2 rounded-md border w-full h-40 object-contain" />
                        @else
                            <div class="mt-2 text-xs text-gray-500">Sin imagen</div>
                        @endif
                    </div>
                @empty
                    <div class="text-sm text-gray-600">Aún no hay presentaciones agregadas.</div>
                @endforelse
            </div>
        </div>
    </div>
    <p class="text-xs text-gray-500">Los cambios (edición/recorte) se reflejan inmediatamente aquí antes de guardar.</p>
</div>