<x-filament-panels::page>
    <div class="space-y-6">
        <!-- Formulario de configuración -->
        <x-filament::section>
            <x-slot name="heading">
                Configuración del Reporte
            </x-slot>
            
            <x-slot name="description">
                Seleccione el tipo de reporte y el período de tiempo para generar análisis detallados.
            </x-slot>

            <form wire:submit="generarReporte">
                {{ $this->form }}
                
                <div class="mt-6 flex gap-3">
                    {{ $this->getFormActions() }}
                </div>
            </form>
        </x-filament::section>

        <!-- Área de resultados -->
        <x-filament::section>
            <x-slot name="heading">
                Resultados del Reporte
            </x-slot>
            
            <div class="min-h-96 flex items-center justify-center bg-gray-50 dark:bg-gray-800 rounded-lg">
                <div class="text-center">
                    <x-heroicon-o-chart-bar class="mx-auto h-12 w-12 text-gray-400" />
                    <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">
                        Sin datos para mostrar
                    </h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        Configure los parámetros del reporte y haga clic en "Generar Reporte" para ver los resultados.
                    </p>
                </div>
            </div>
        </x-filament::section>

        <!-- Información adicional -->
        <x-filament::section>
            <x-slot name="heading">
                Tipos de Reportes Disponibles
            </x-slot>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <div class="p-4 border border-gray-200 dark:border-gray-700 rounded-lg">
                    <h4 class="font-medium text-gray-900 dark:text-gray-100">Ventas</h4>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                        Análisis de ventas diarias, semanales y mensuales con totales y tendencias.
                    </p>
                </div>
                
                <div class="p-4 border border-gray-200 dark:border-gray-700 rounded-lg">
                    <h4 class="font-medium text-gray-900 dark:text-gray-100">Productos</h4>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                        Productos más vendidos, inventario actual y alertas de stock bajo.
                    </p>
                </div>
                
                <div class="p-4 border border-gray-200 dark:border-gray-700 rounded-lg">
                    <h4 class="font-medium text-gray-900 dark:text-gray-100">Finanzas</h4>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                        Flujo de caja, ingresos vs gastos y análisis de rentabilidad.
                    </p>
                </div>
            </div>
        </x-filament::section>
    </div>
</x-filament-panels::page>