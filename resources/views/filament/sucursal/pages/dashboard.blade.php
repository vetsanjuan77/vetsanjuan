<x-filament-panels::page>
    <div class="space-y-6">
        <!-- Información de la Sucursal -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-white">
                        🏢 {{ Auth::user()->sucursal->nombre }}
                    </h2>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                        📍 {{ Auth::user()->sucursal->direccion }}
                    </p>
                    <div class="flex items-center gap-4 mt-2 text-sm text-gray-500 dark:text-gray-400">
                        <span>📞 {{ Auth::user()->sucursal->telefono }}</span>
                        <span>✉️ {{ Auth::user()->sucursal->email }}</span>
                    </div>
                </div>
                <div class="text-right">
                    <div class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                        ✅ Activa
                    </div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                        Código: {{ Auth::user()->sucursal->codigo }}
                    </p>
                </div>
            </div>
        </div>

        <!-- Mensaje de Bienvenida -->
        <div class="bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 rounded-lg border border-blue-200 dark:border-blue-800 p-6">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900 rounded-lg flex items-center justify-center">
                        <span class="text-xl">👋</span>
                    </div>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-medium text-blue-900 dark:text-blue-100">
                        ¡Bienvenido, {{ Auth::user()->name }}!
                    </h3>
                    <p class="text-blue-700 dark:text-blue-300 mt-1">
                        Administra tu sucursal desde este panel. Aquí encontrarás todas las herramientas necesarias para gestionar ventas, inventario, clientes y más.
                    </p>
                </div>
            </div>
        </div>

        <!-- Accesos Rápidos -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <a href="{{ route('filament.sucursal.resources.facturas.index') }}" class="block p-4 bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 hover:shadow-md transition-shadow">
                <div class="flex items-center">
                    <div class="w-10 h-10 bg-green-100 dark:bg-green-900 rounded-lg flex items-center justify-center">
                        <span class="text-xl">💰</span>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-900 dark:text-white">Facturación</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Crear facturas</p>
                    </div>
                </div>
            </a>

            <a href="{{ route('filament.sucursal.resources.productos.index') }}" class="block p-4 bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 hover:shadow-md transition-shadow">
                <div class="flex items-center">
                    <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900 rounded-lg flex items-center justify-center">
                        <span class="text-xl">📦</span>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-900 dark:text-white">Inventario</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Gestionar productos</p>
                    </div>
                </div>
            </a>

            <a href="{{ route('filament.sucursal.resources.clientes.index') }}" class="block p-4 bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 hover:shadow-md transition-shadow">
                <div class="flex items-center">
                    <div class="w-10 h-10 bg-purple-100 dark:bg-purple-900 rounded-lg flex items-center justify-center">
                        <span class="text-xl">👥</span>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-900 dark:text-white">Clientes</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Administrar clientes</p>
                    </div>
                </div>
            </a>

            <a href="{{ route('filament.sucursal.resources.cajas.index') }}" class="block p-4 bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 hover:shadow-md transition-shadow">
                <div class="flex items-center">
                    <div class="w-10 h-10 bg-orange-100 dark:bg-orange-900 rounded-lg flex items-center justify-center">
                        <span class="text-xl">💳</span>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-900 dark:text-white">Caja</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Control de caja</p>
                    </div>
                </div>
            </a>
        </div>

        <!-- Widgets de Estadísticas -->
        <div class="space-y-6">
            <x-filament-widgets::widgets
                :widgets="$this->getHeaderWidgets()"
                :columns="$this->getHeaderWidgetsColumns()"
            />
        </div>

        <!-- Widgets del Footer -->
        <div class="space-y-6">
            <x-filament-widgets::widgets
                :widgets="$this->getFooterWidgets()"
                :columns="$this->getFooterWidgetsColumns()"
            />
        </div>
    </div>
</x-filament-panels::page>