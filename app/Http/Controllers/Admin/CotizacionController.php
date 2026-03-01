<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Cotizacion;
use App\Models\Pedido;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CotizacionController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/Cotizaciones', [
            'cotizaciones' => Cotizacion::with('pedido')
                ->latest()->paginate(20),
        ]);
    }

    public function pedidos()
    {
        return Inertia::render('Admin/Pedidos', [
            'pedidos' => Pedido::with('cotizacion')
                ->latest()->paginate(20),
        ]);
    }

    public function actualizarEstado(Request $request, Pedido $pedido)
    {
        $request->validate([
            'estado' => 'required|in:pendiente,confirmado,en_camino,entregado,cancelado',
            'notas'  => 'nullable|string',
        ]);

        $pedido->update([
            'estado' => $request->estado,
            'notas'  => $request->notas,
        ]);

        return redirect()->back()->with('success', 'Estado actualizado.');
    }

    public function show(Cotizacion $cotizacion)
{
    $cotizacion->load('pedido');
    return Inertia::render('Admin/CotizacionDetalle', [
        'cotizacion' => $cotizacion
    ]);
}
}