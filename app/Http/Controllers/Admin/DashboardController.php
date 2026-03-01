<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Cotizacion;
use App\Models\Pedido;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/Dashboard', [
            'stats' => [
                'total_cotizaciones' => Cotizacion::count(),
                'total_pedidos'      => Pedido::count(),
                'pendientes'         => Pedido::where('estado', 'pendiente')->count(),
                'confirmados'        => Pedido::where('estado', 'confirmado')->count(),
                'en_camino'          => Pedido::where('estado', 'en_camino')->count(),
                'entregados'         => Pedido::where('estado', 'entregado')->count(),
            ],
            'ultimas_cotizaciones' => Cotizacion::with('pedido')
                ->latest()->take(10)->get(),
            'ultimos_pedidos' => Pedido::with('cotizacion')
                ->latest()->take(10)->get(),
        ]);
    }
}