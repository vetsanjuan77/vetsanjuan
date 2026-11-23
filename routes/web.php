<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportesFinancierosController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PagoController;
use App\Http\Controllers\CarritoController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CitasController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MascotaController;
use App\Http\Controllers\ClienteController;

use App\Http\Controllers\CitaPeluqueriaController;
use App\Http\Controllers\ReporteExportController;
use Illuminate\Support\Facades\Auth;

Route::get('/', [ProductController::class, 'home'])->name('home');



Route::prefix('/products')->controller(ProductController::class)->group(function () {
    Route::get('/', 'shop')->name('shop');
    Route::get('/{id}', 'showProduct')->name('shop.producto');
});

// Rutas para Nosotros y Contacto
Route::get('/nosotros', function () {
    return view('nosotros');
})->name('nosotros');

Route::get('/contacto', function () {
    return view('contacto');
})->name('contacto');

// Página del superhéroe Vetman
Route::get('/vetman', function () {
    return view('vetman');
})->name('vetman');

// Términos y Condiciones
Route::get('/tyc', function () {
    return view('tyc');
})->name('tyc');

// Ruta de pre-login (selección de perfil sin credenciales)
Route::get('/prelogin', function () {
    return view('auth.prelogin');
})->name('prelogin');

// Exportaciones (GET) para PDF y Excel
Route::get('/exports/reporte-financiero.pdf', [ReporteExportController::class, 'financieroPdf'])->name('exports.financiero.pdf');
Route::get('/exports/reporte-inventario.pdf', [ReporteExportController::class, 'inventarioPdf'])->name('exports.inventario.pdf');
Route::get('/exports/reporte-financiero.xlsx', [ReporteExportController::class, 'financieroExcel'])->name('exports.financiero.excel');
Route::get('/exports/reporte-inventario.xlsx', [ReporteExportController::class, 'inventarioExcel'])->name('exports.inventario.excel');

// Rutas del carrito
Route::prefix('carrito')->group(function () {
    Route::get('/', [CarritoController::class, 'index'])->name('carrito.index');
    Route::post('/agregar/{id}', [CarritoController::class, 'agregar'])->name('carrito.agregar');
    Route::delete('/{id}', [CarritoController::class, 'eliminar'])->name('carrito.eliminar');
    Route::patch('/{id}', [CarritoController::class, 'actualizar'])->name('carrito.actualizar');
    Route::post('/limpiar', [CarritoController::class, 'limpiar'])->name('carrito.limpiar');
});

// Rutas de pago
Route::prefix('pago')->group(function () {
    Route::get('/', [PagoController::class, 'mostrarFormulario'])->name('pago.formulario');
Route::get('/ciudades/departamento/{departamentoId}', [PagoController::class, 'getCiudadesPorDepartamento'])->name('ciudades.by.departamento');
    Route::post('/confirmar', [PagoController::class, 'iniciarMercadoPago'])->name('pago.confirmar');
    Route::post('/notificacion', [PagoController::class, 'notificacion'])->name('pago.notificacion');
    // Rutas de retorno de Mercado Pago
    Route::get('/exito', [PagoController::class, 'pagoExito'])->name('pago.exito');
    Route::get('/fallo', [PagoController::class, 'pagoFallo'])->name('pago.fallo');
    Route::get('/pendiente', [PagoController::class, 'pagoPendiente'])->name('pago.pendiente');
});



Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/citas/store', [CitasController::class, 'store'])->name('citas.store');
    Route::post('/citas-peluqueria/store', [CitaPeluqueriaController::class, 'store'])->name('citas_peluqueria.store');
    Route::post('/mascotas', [MascotaController::class, 'store'])->name('mascotas.store');
    Route::put('/mascotas/{mascota}', [MascotaController::class, 'update'])->name('mascotas.update');
    Route::delete('/mascotas/{mascota}', [MascotaController::class, 'destroy'])->name('mascotas.destroy');
    Route::put('/cliente/perfil', [ClienteController::class, 'update'])->name('cliente.update');
    Route::get('/vacunas/especie/{especieId}', [DashboardController::class, 'getVacunasByEspecie'])->name('vacunas.by.especie');
    Route::get('/mascotas/{mascotaId}/especie', [DashboardController::class, 'getMascotaEspecie'])->name('mascotas.especie');
    Route::get('/tamanos/especie/{especieId}', [MascotaController::class, 'getTamanosPorEspecie'])->name('tamanos.by.especie');
    Route::get('/razas/especie/{especieId}', [MascotaController::class, 'getRazasPorEspecie'])->name('razas.by.especie');
    Route::get('/tamanos/especie/{especieId}', [MascotaController::class, 'getTamanosPorEspecie'])->name('tamanos.by.especie');
    Route::get('/tipos-pelo', [MascotaController::class, 'getTiposPelo'])->name('tipos.pelo');
    
    // Rutas para las nuevas páginas separadas
    Route::get('/peluqueria', [DashboardController::class, 'peluqueria'])->name('peluqueria');
    Route::get('/veterinaria', [DashboardController::class, 'veterinaria'])->name('veterinaria');
    Route::get('/vacunacion', [DashboardController::class, 'vacunacion'])->name('vacunacion');

    // Nuevo: endpoint de slots disponibles para citas
    Route::get('/citas/slots', [DashboardController::class, 'getAvailableSlots'])->name('citas.slots');

    // Cancelación de citas por el cliente
    Route::post('/citas/{cita}/cancelar', [CitasController::class, 'cancelar'])->name('citas.cancelar');
    Route::post('/citas-peluqueria/{cita}/cancelar', [CitaPeluqueriaController::class, 'cancelar'])->name('citas_peluqueria.cancelar');


    // Rutas del perfil
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Reportes financieros (Estado de Resultados y Balance General)
    Route::get('/reports/estado-resultados', [ReportesFinancierosController::class, 'estadoResultados'])
        ->name('reports.estado_resultados');
    Route::get('/reports/balance-general', [ReportesFinancierosController::class, 'balanceGeneral'])
        ->name('reports.balance_general');
    Route::get('/dev/barcodes', function () {
        return response()->json(
            \App\Models\Presentacion::select('id','nombre','codigo_barras')
                ->orderBy('id')
                ->limit(20)
                ->get()
        );
    });

    // Recibo/imprimir factura
    Route::get('/facturas/{factura}/recibo', function (\App\Models\Factura $factura) {
        return view('facturas.recibo', compact('factura'));
    })->name('facturas.recibo');
});

require __DIR__ . '/auth.php';
