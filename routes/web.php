<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CotizadorController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ConfiguracionController;
use App\Http\Controllers\Admin\CotizacionController;
use App\Http\Controllers\Admin\PedidoController;

// ===== RUTAS PÚBLICAS (sin auth) =====
Route::get('/',                             [CotizadorController::class, 'index'])->name('cotizador.index');
Route::post('/cotizar',                     [CotizadorController::class, 'cotizar'])->name('cotizador.cotizar');
Route::get('/resultado/{cotizacion}',       [CotizadorController::class, 'resultado'])->name('cotizador.resultado');
Route::post('/resultado/{cotizacion}/pedido',[CotizadorController::class, 'storePedido'])->name('cotizador.pedido');
Route::get('/gracias/{codigo}', [CotizadorController::class, 'gracias'])->name('cotizador.gracias');
Route::get('/tracking',                     [CotizadorController::class, 'trackingForm'])->name('cotizador.tracking');
Route::post('/tracking',                    [CotizadorController::class, 'tracking'])->name('cotizador.tracking.buscar');

// ===== ADMIN (con auth) =====
Route::prefix('admin')->middleware(['auth', 'verified'])->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');

    // Cotizaciones
    Route::get('/cotizaciones',                        [CotizacionController::class, 'index'])->name('admin.cotizaciones');
    Route::get('/cotizaciones/{cotizacion}',            [CotizacionController::class, 'show'])->name('admin.cotizaciones.show');
    Route::post('/cotizaciones/{cotizacion}/pedido',    [PedidoController::class, 'crearDesdeCotizacion'])->name('admin.cotizaciones.crear-pedido');

    // Pedidos
    Route::get('/pedidos',                             [PedidoController::class, 'index'])->name('admin.pedidos');
    Route::get('/pedidos/{pedido}',                    [PedidoController::class, 'show'])->name('admin.pedidos.show');
    Route::patch('/pedidos/{pedido}/estado',           [PedidoController::class, 'actualizarEstado'])->name('admin.pedidos.estado');
    Route::post('/pedidos/{pedido}/tracking',          [PedidoController::class, 'agregarTracking'])->name('admin.pedidos.tracking');

    // Configuración
    Route::get('/configuracion',  [ConfiguracionController::class, 'index'])->name('admin.configuracion');
    Route::post('/configuracion', [ConfiguracionController::class, 'update'])->name('admin.configuracion.update');
});


// Redirect dashboard genérico
Route::get('/dashboard', fn() => redirect()->route('admin.dashboard'))
    ->middleware(['auth'])->name('dashboard');

require __DIR__.'/auth.php';