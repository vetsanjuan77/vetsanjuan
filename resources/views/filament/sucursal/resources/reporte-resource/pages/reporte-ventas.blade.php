<x-filament-panels::page>
    <div class="space-y-6">
        <!-- Widgets de gráficos -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            @foreach ($this->getHeaderWidgets() as $widget)
                @livewire($widget)
            @endforeach
        </div>

        <!-- Información adicional -->
        <x-filament::section>
            <x-slot name="heading">
                Análisis de Ventas
            </x-slot>
            
            <x-slot name="description">
                Visualización detallada del rendimiento de ventas con gráficos interactivos y métricas clave.
            </x-slot>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-blue-50 dark:bg-blue-900/20 p-4 rounded-lg">
                    <h4 class="font-medium text-blue-900 dark:text-blue-100">Tendencias Diarias</h4>
                    <p class="text-sm text-blue-700 dark:text-blue-300 mt-2">
                        Monitorea el comportamiento de ventas día a día para identificar patrones y oportunidades de mejora.
                    </p>
                </div>
                
                <div class="bg-green-50 dark:bg-green-900/20 p-4 rounded-lg">
                    <h4 class="font-medium text-green-900 dark:text-green-100">Análisis Mensual</h4>
                    <p class="text-sm text-green-700 dark:text-green-300 mt-2">
                        Compara el rendimiento mensual para evaluar el crecimiento y planificar estrategias futuras.
                    </p>
                </div>
                
                <div class="bg-purple-50 dark:bg-purple-900/20 p-4 rounded-lg">
                    <h4 class="font-medium text-purple-900 dark:text-purple-100">Productos Estrella</h4>
                    <p class="text-sm text-purple-700 dark:text-purple-300 mt-2">
                        Identifica los productos más populares para optimizar inventario y estrategias de marketing.
                    </p>
                </div>
            </div>
        </x-filament::section>

        <!-- Acciones rápidas -->
        <x-filament::section>
            <x-slot name="heading">
                Acciones Rápidas
            </x-slot>
            
            <div class="flex flex-wrap gap-3">
                <x-filament::button 
                    tag="a" 
                    href="{{ route('filament.sucursal.resources.reportes.generar') }}"
                    icon="heroicon-o-document-chart-bar"
                    color="primary"
                >
                    Generar Reporte Personalizado
                </x-filament::button>
                
                <x-filament::button 
                    tag="a" 
                    href="{{ route('filament.sucursal.resources.reportes.inventario') }}"
                    icon="heroicon-o-cube"
                    color="secondary"
                >
                    Ver Reporte de Inventario
                </x-filament::button>
                
                <x-filament::button 
                    tag="a" 
                    href="{{ route('filament.sucursal.resources.reportes.financiero') }}"
                    icon="heroicon-o-currency-dollar"
                    color="success"
                >
                    Análisis Financiero
                </x-filament::button>
            </div>
        </x-filament::section>
    </div>
</x-filament-panels::page>