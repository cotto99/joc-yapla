<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CotizadorController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ConfiguracionController;
use App\Http\Controllers\Admin\CotizacionController;

// ===== VISTA PÚBLICA =====
Route::get('/', [CotizadorController::class, 'index'])->name('cotizador.index');
Route::post('/cotizar', [CotizadorController::class, 'cotizar'])->name('cotizador.cotizar');
Route::post('/pedido/{cotizacion}', [CotizadorController::class, 'pedido'])->name('cotizador.pedido');
Route::get('/gracias', [CotizadorController::class, 'gracias'])->name('cotizador.gracias');

// ===== ADMIN =====
Route::prefix('admin')->middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
    Route::get('/cotizaciones', [CotizacionController::class, 'index'])->name('admin.cotizaciones');
    Route::get('/pedidos', [CotizacionController::class, 'pedidos'])->name('admin.pedidos');
    Route::patch('/pedidos/{pedido}/estado', [CotizacionController::class, 'actualizarEstado'])->name('admin.pedidos.estado');
    Route::get('/configuracion', [ConfiguracionController::class, 'index'])->name('admin.configuracion');
    Route::get('/cotizaciones/{cotizacion}', [CotizacionController::class, 'show'])->name('admin.cotizaciones.show');
    Route::post('/configuracion', [ConfiguracionController::class, 'update'])->name('admin.configuracion.update');
    Route::get('/resultado/{cotizacion}', [CotizadorController::class, 'resultado'])->name('cotizador.resultado');
});

Route::get('/dashboard', fn() => redirect()->route('admin.dashboard'))
    ->middleware(['auth'])->name('dashboard');

require __DIR__.'/auth.php';