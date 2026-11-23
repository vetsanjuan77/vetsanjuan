@php
    $seconds = is_numeric($seconds ?? null) ? (int) $seconds : 5;
    $url = is_string($url ?? null) && $url !== '' ? $url : null;
@endphp

@if ($url)
    <div class="space-y-2">
        <div class="rounded-md overflow-hidden border border-gray-200 dark:border-gray-700">
            <img src="{{ $url }}" alt="Previsualización del banner" class="w-full h-auto" />
        </div>
        <div class="text-xs text-gray-600 dark:text-gray-400">
            Tiempo de visualización: <span class="font-medium">{{ $seconds }}s</span>
        </div>
    </div>
@else
    <div class="text-sm text-gray-500 dark:text-gray-400">
        Sube una imagen para ver la previsualización.
    </div>
@endif
<div class="p-4 border rounded bg-white">
    <div class="flex items-center justify-between mb-2">
        <div>
            <span class="text-sm text-gray-600">Tiempo configurado:</span>
            <span class="font-semibold">{{ $seconds }}s</span>
        </div>
        <button type="button" class="px-3 py-1 text-sm bg-gray-100 hover:bg-gray-200 rounded" onclick="previewBanner()">Preview</button>
    </div>
    <div class="aspect-[16/6] w-full bg-gray-100 flex items-center justify-center overflow-hidden rounded">
        @if ($url)
            <img src="{{ $url }}" alt="Preview banner" class="object-cover w-full h-full" id="banner-preview-img">
        @else
            <span class="text-gray-500 text-sm">Selecciona una imagen para ver la previsualización.</span>
        @endif
    </div>
    <div class="mt-2 text-xs text-gray-500">Sugerencia de dimensiones mínimas: 1200x400 px para buena calidad.</div>
</div>
<script>
    function previewBanner(){
        const img = document.getElementById('banner-preview-img');
        if(!img){ return; }
        img.classList.remove('ring-2','ring-blue-500');
        void img.offsetWidth; // reflow
        img.classList.add('ring-2','ring-blue-500');
        setTimeout(()=>{
            img.classList.remove('ring-2','ring-blue-500');
        }, ({{ $seconds }} * 1000));
    }
</script>