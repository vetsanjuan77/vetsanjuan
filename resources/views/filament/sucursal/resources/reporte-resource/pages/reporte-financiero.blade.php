<x-filament-panels::page>
    <div class="space-y-6">
        <!-- Encabezado de la página -->
        <div class="bg-gradient-to-r from-green-500 to-blue-600 rounded-lg shadow p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-bold">
                        💰 Reportes Financieros
                    </h2>
                    <p class="mt-1 text-green-100">
                        Análisis completo de rentabilidad, flujo de caja y estado financiero
                    </p>
                </div>
                <div class="flex space-x-2">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-white/20 text-white">
                        📈 Análisis en Tiempo Real
                    </span>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-white/20 text-white">
                        💹 Métricas Avanzadas
                    </span>
                </div>
            </div>
        </div>

        <!-- Indicadores financieros clave -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Ingresos Hoy</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white" id="ingresos-hoy">$0</p>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-red-100 rounded-full flex items-center justify-center">
                            <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Gastos Hoy</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white" id="gastos-hoy">$0</p>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Utilidad Hoy</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white" id="utilidad-hoy">$0</p>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center">
                            <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 8v8m-4-5v5m-4-2v2m-2 4h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Margen %</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white" id="margen-hoy">0%</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Widgets de reportes financieros -->
        <div class="space-y-6">
            @foreach ($this->getHeaderWidgets() as $widget)
                @livewire($widget)
            @endforeach
        </div>

        <!-- Análisis y recomendaciones financieras -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Análisis de tendencias -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                    📊 Análisis de Tendencias
                </h3>
                <div class="space-y-4">
                    <div class="flex items-center justify-between p-3 bg-green-50 dark:bg-green-900/20 rounded-lg">
                        <div>
                            <p class="text-sm font-medium text-green-800 dark:text-green-200">Crecimiento de Ingresos</p>
                            <p class="text-xs text-green-600 dark:text-green-400">Comparado con el mes anterior</p>
                        </div>
                        <span class="text-lg font-bold text-green-600" id="crecimiento-ingresos">+0%</span>
                    </div>
                    
                    <div class="flex items-center justify-between p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                        <div>
                            <p class="text-sm font-medium text-blue-800 dark:text-blue-200">Eficiencia de Gastos</p>
                            <p class="text-xs text-blue-600 dark:text-blue-400">Gastos vs ingresos</p>
                        </div>
                        <span class="text-lg font-bold text-blue-600" id="eficiencia-gastos">0%</span>
                    </div>
                    
                    <div class="flex items-center justify-between p-3 bg-purple-50 dark:bg-purple-900/20 rounded-lg">
                        <div>
                            <p class="text-sm font-medium text-purple-800 dark:text-purple-200">ROI Mensual</p>
                            <p class="text-xs text-purple-600 dark:text-purple-400">Retorno sobre inversión</p>
                        </div>
                        <span class="text-lg font-bold text-purple-600" id="roi-mensual">0%</span>
                    </div>
                </div>
            </div>

            <!-- Recomendaciones financieras -->
            <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-6">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-yellow-800 dark:text-yellow-200">
                            💡 Recomendaciones Financieras
                        </h3>
                        <div class="mt-2 text-sm text-yellow-700 dark:text-yellow-300">
                            <ul class="list-disc list-inside space-y-1">
                                <li>Mantén un margen de utilidad mínimo del 20% para garantizar sostenibilidad</li>
                                <li>Controla los gastos operativos para que no excedan el 60% de los ingresos</li>
                                <li>Diversifica las fuentes de ingresos para reducir riesgos financieros</li>
                                <li>Establece un fondo de emergencia equivalente a 3 meses de gastos operativos</li>
                                <li>Revisa mensualmente los indicadores de rentabilidad y flujo de caja</li>
                                <li>Implementa estrategias de cobranza para mejorar el flujo de efectivo</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Alertas financieras -->
        <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-4" id="alertas-financieras" style="display: none;">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-red-800 dark:text-red-200">
                        ⚠️ Alertas Financieras
                    </h3>
                    <div class="mt-2 text-sm text-red-700 dark:text-red-300" id="lista-alertas">
                        <!-- Las alertas se cargarán dinámicamente -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Actualizar métricas financieras cada 60 segundos
        setInterval(function() {
            // Aquí se pueden hacer llamadas AJAX para actualizar las métricas
            // Por ahora, solo como placeholder
        }, 60000);

        // Función para mostrar alertas financieras
        function mostrarAlertas(alertas) {
            const alertasDiv = document.getElementById('alertas-financieras');
            const listaAlertas = document.getElementById('lista-alertas');
            
            if (alertas.length > 0) {
                listaAlertas.innerHTML = '<ul class="list-disc list-inside space-y-1">' + 
                    alertas.map(alerta => '<li>' + alerta + '</li>').join('') + 
                    '</ul>';
                alertasDiv.style.display = 'block';
            } else {
                alertasDiv.style.display = 'none';
            }
        }
    </script>
</x-filament-panels::page>